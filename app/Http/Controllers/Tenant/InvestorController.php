<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant\Client;
use App\Models\Tenant\Invoice;
use App\Accounts\Handlers\AccountGateway;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\Invoice_payment;
use App\Models\Tenant\Client_payment;
use App\Models\Tenant\Investor;
use Illuminate\Support\Facades\Hash;

class InvestorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:tenant_auth');
    }

    /**
     * View Page of Investors
     *
    */
    public function ViewInvestors()
    {
        return view('Tenant.investor.view');
    }


    /**
     * Create view Page of investor addition
     *
    */
    public function showInvestorForm($config=null)
    {

        $all_emails = Investor::select('email')->get()->keyBy('email')->keys();

        return view('Tenant.investor.create', compact('config', 'all_emails'));
    }

    /**
     * POST request of creating the investor
     *
    */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'phone' => 'required|unique:investors|max:255',
            'email' => 'required|email|unique:investors|max:255',
            'refid' => 'required|unique:investors|max:255',
            'password' => 'required|confirmed|min:8'
            // 'refid_front_image' => 'required|image',
            // 'refid_back_image' => 'required|image',
        ]);


        # Payload
        $investor = new Investor;
        $investor->name = $request->name;
        $investor->phone = $request->phone;
        $investor->email = $request->email;
        $investor->notes = $request->notes;
        $investor->refid = $request->refid;
        $investor->password = Hash::make($request->password);

        #need to check if image added
        $images = [];
        if($request->hasFile('refid_front_image')){
            $filepath = Storage::putfile('investors', $request->file('refid_front_image'));
            $images[] = [
                'type' => 'front',
                'src' => $filepath
            ];
        }
        if($request->hasFile('refid_back_image')){
            $filepath = Storage::putfile('investors', $request->file('refid_back_image'));
            $images[] = [
                'type' => 'back',
                'src' => $filepath
            ];
        }
        $investor->images = $images;

        $investor->save();

        return response()->json($investor);
    }

    /**
     * Create view Page of investor
     *
    */
    public function showDetails($id, $config=null)
    {

        $investor = Investor::with([
            'bookings' => function($query){
                $query->select('id', 'investor_id', 'status');
            },
            'bookings.vehicle' => function($query){
                $query->select('id', 'vehicle_booking_id', 'plate', 'chassis_number');
            },
            // 'bookings.drivers' => function($query){
            //     $query->select('id', 'name', 'booking_id');
            // }
        ])
        ->findOrFail((int)$id)
        ->append('balance');


        return view('Tenant.investor.detail', compact('investor', 'config'));
    }

    /**
     * GET request of editing the page
     *
    */
    public function showEditForm($investor_id)
    {
        # Find the investor
        $investor = Investor::findOrFail((int)$investor_id);

        $investor->actions=[
            'status'=>1,
        ];

        # Call the load function
        return $this->showInvestorForm((object)[
            'investor'=>$investor,
            'action'=>'edit'
        ]);

        return view('Tenant.investor.edit', compact('investor'));
    }

    public function edit(Request $request)
    {
        $investor = Investor::findOrFail((int)$request->investor_id);

        $validatePayload = [
            'name' => 'required|max:255',
            'phone' => ['required','max:255'],
            'email' => ['required','email','max:255', Rule::unique('email')->ignore($investor->_id, '_id')],
            'refid' => ['required','max:255'],
            // 'refid_front_image' => 'required|image',
            // 'refid_back_image' => 'required|image',
        ];

        if($request->has('change_password')){
            $validatePayload['password'] = 'required|confirmed|min:8';

            $investor->password = Hash::make($request->password);
        }

        $request->validate($validatePayload);

        $investor->name = $request->name;
        $investor->phone = $request->phone;
        $investor->email = $request->email;
        $investor->refid = $request->refid;
        $investor->notes = $request->notes;

        $images = [];
        if($request->hasFile('refid_front_image')){
            // if(isset($investor->images) && count($investor->images) > 0 && collect($investor->images)->contains('type', 'front') ) Storage::delete(collect($investor->images)->firstWhere('type', 'front')['src']); # it will delete img from live too

            $filepath = Storage::putfile('investors', $request->file('refid_front_image'));
            $images[] = [
                'type' => 'front',
                'src' => $filepath
            ];


        }
        if($request->hasFile('refid_back_image')){
            // if(isset($investor->images) && count($investor->images) > 0 && collect($investor->images)->contains('type', 'back') ) Storage::delete(collect($investor->images)->firstWhere('type', 'back')['src']); # it will delete img from live too

            $filepath = Storage::putfile('investors', $request->file('refid_back_image'));
            $images[] = [
                'type' => 'back',
                'src' => $filepath
            ];
        }

        $investor->images = $images;

        $investor->save();
        return redirect()->route('tenant.admin.investors.view')->with('message', 'Investor Updated Successfully');
    }


    public function changeInvestorManagerView(int $investor_id)
    {
        $investor = Investor::findOrFail($investor_id);
        $investorOptions = Investor::where('id', '!=', $investor_id)

        ->where('manages_to', '!=', $investor_id)
        ->select('id', 'name', 'manages_by')
        ->get()
        ->map(function($item) use ($investor){
            return [
                'id'=> $item->id,
                'text' => $item->name,
                'selected' => in_array($investor->id, isset($item->manages_by) && is_array($item->manages_by) ? $item->manages_by : [])
            ];
        });
        return view('Tenant.investor.change_investor_manager', compact('investor','investorOptions'));
    }

    public function changeInvestorManagerAction(Request $request, int $investor_id)
    {

        $request->validate([
            'manager_ids' => ['nullable','array']
        ]);

        $ids = $request->has('manager_ids') && is_array( $request->manager_ids ) ? $request->manager_ids : [];

        $investor = Investor::findOrFail($investor_id);

        $investor->manages()->sync(array_map('intval', $ids)); # ids to integer

        return response()->json($investor);
    }
}
