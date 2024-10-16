<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\Ledger;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Accounts\Handlers\AccountGateway;
use App\Models\Tenant\Addon;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LedgerController extends Controller
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
     * View Page of Ledger
     *
    */
    public function ViewLedger()
    {
        // $starttime = microtime(true);

        // $timediff = microtime(true) - $starttime;
        // return $timediff;
        return view('Tenant.ledger.view');
    }

    /**
     * Create Part inventory
     *
    */
    public function showEditForm($config=null)
    {

        return view('Tenant.ledger.edit', compact('config'));
    }

    public function showSingleEditForm($id)
    {
        $ledger = Ledger::find((int)$id);

        # Call the load job function
        return $this->showEditForm((object)[
            'ledger'=>$ledger,
            'action'=>'edit'
        ]);
    }

    /**
     * POST request of Editing the ledger
     *
    */
    public function edit(request $request){

        $validated = $request->validate([
            'amount' => 'required|gt:0'
        ]);

        $amount = (float)$request->amount;
        $id = (int) $request->ledger_id;
        $ledger = \App\Models\Tenant\Ledger::with('relations')->findOrFail($id);
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $month = Carbon::parse($request->month)->startOfMonth()->format('Y-m-d');

        $effect_all = $request->has('effect_all');

        if($effect_all){
            $ledger_relations = $ledger->relations;

            foreach ($ledger_relations as $relation) {
                # Update source
                $source = $relation->source;
                if(isset($source->amount)) $source->amount=$amount;
                if(isset($source->date)) $source->date=$date;
                if(isset($source->given_date)) $source->given_date=$date;
                if(isset($source->month)) $source->month=$month;

                if($relation->source_model === "App\Accounts\Models\Account_transaction") {
                    // Edit time

                    if(isset($source->time)) $source->time=Carbon::parse($source->time)->setDateFrom(Carbon::parse($date))->toIso8601String();

                }

                $source->save();

            }
        }

        # Update Ledger
        $ledger->amount=$amount;
        $ledger->date=$date;
        $ledger->month=$month;
        $ledger->update();

        return $ledger;
    }

    /**
     * DELETE request of Delete the ledger
     *
    */
    public function delete($ledger_id){

        $id = (int) $ledger_id;
        $ledger = \App\Models\Tenant\Ledger::with('relations')->findOrFail($id);

        $ledger_relations = $ledger->relations;

        # This will return all tables records that are deleted
        $feed = [];

        foreach ($ledger_relations as $relation) {
            # Delete source
            $source = $relation->source;

            # If not found, means its already deleted
            if(!isset($source)) {

                # Delete relation too
                $relation->delete();

                continue;
            }

            $feed[] = [
                'model' => $relation->source_model,
                'id' => $relation->source_id,
                'tag' => $relation->tag,
                'data' => $source
            ];

            if($relation->source_model === "App\Accounts\Models\Account_transaction") {
                # Wee need to delete the links of this transaction too
                $source->links()->delete();
            }
            if($relation->source_model === Ledger::class) {
                # Wee need to delete the relations of this ledger too
                $source->relations()->delete();
            }

            $source->delete();

            # Delete relation too
            $relation->delete();

        }
        # Delete Ledger
        $ledger->delete();

        return response()->json([
            "status" => 1,
            'feed' => $feed
        ]);
    }


    /**
     * Generate description of ledger (based on source)
     *
    */
    public static function generate_description($ledger)
    {
        // dump($ledger);
        if(!isset($ledger->generated_source))return $ledger->id.'<span class="text-danger">Source not found</span>';
        $tag = $ledger->tag;
        $desc='';
        switch ($tag) {
            case 'expense':
                $prefix = 'EX-'.$ledger->id;
                # Check if attachment found
                $suffix='';
                if(isset($ledger->generated_source->invoice_tax_img) && $ledger->generated_source->invoice_tax_img !== ''){
                    $attachment = asset(Storage::url($ledger->generated_source->invoice_tax_img));
                    $suffix.= ' <a href="'.$attachment.'" target="_blank">
                        <i class="la la-file-picture-o"></i>
                    </a>';
                }
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->type.' | '.$ledger->generated_source->description.' | '.Carbon::parse($ledger->generated_source->month)->format('M Y').$suffix.'</span>
                ';
                break;
            case 'transaction_ledger':
                $prefix = 'TLX-'.$ledger->id;

                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">Bulk Transaction | '.$ledger->generated_source->title.' | '.Carbon::parse($ledger->generated_source->month)->format('M Y').'</span>
                    '.(isset($ledger->generated_source->description)?('<span class="description-subtitle">'.$ledger->generated_source->description.'</span>'):'').'
                ';
                break;
            case 'client_income':
                $prefix = 'CI-'.$ledger->id;

                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">Income | '.$ledger->generated_source->title.' | '.Carbon::parse($ledger->generated_source->month)->format('M Y').'</span>
                    '.(isset($ledger->generated_source->description)?('<span class="description-subtitle">'.$ledger->generated_source->description.'</span>'):'').'
                ';
                break;
            case 'vehiclebills':
                $prefix = 'VB-'.$ledger->id;

                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->title.'</span>
                    '.(isset($ledger->generated_source->description)?('<span class="description-subtitle">'.$ledger->generated_source->description.'</span>'):'').'
                ';
                break;
            case 'simbill_latecharge':
            case 'sim_bill':
                $prefix = 'SBX-'.$ledger->id;

                if(isset($ledger->props) && isset($ledger->props['prefix']) && isset($ledger->props['prefix']['text'])){
                    # Add additional dynamic prefix
                    if(isset($ledger->props['prefix']['url'])){
                        $prefix .= " | <a href='".$ledger->props['prefix']['url']."'>".$ledger->props['prefix']['text']."</a>";
                    }
                    else{
                        $prefix .= " | ".$ledger->props['prefix']['text'];
                    }
                }

                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->title.' | '.Carbon::parse($ledger->generated_source->month)->format('M Y').'</span>
                    '.(isset($ledger->generated_source->description)?('<span class="description-subtitle">'.$ledger->generated_source->description.'</span>'):'').'
                ';
                break;
            case 'addon_expense':
                $source_type = $ledger->generated_source->addon->source_type;
                if($source_type === 'driver'){
                    $prefix = "ADX-$ledger->id";
                }else if($source_type === 'vehicle'){
                    $prefix = "AVX-$ledger->id";
                }else{
                    $prefix = "ASX-$ledger->id";
                }

                if(isset($ledger->props) && isset($ledger->props['prefix']) && isset($ledger->props['prefix']['text'])){
                    # Add additional dynamic prefix
                    if(isset($ledger->props['prefix']['url'])){
                        $prefix .= " | <a href='".$ledger->props['prefix']['url']."'>".$ledger->props['prefix']['text']."</a>";
                    }
                    else{
                        $prefix .= " | ".$ledger->props['prefix']['text'];
                    }
                }

                # Check if attachment found
                $suffix='';
                if(isset($ledger->generated_source->attachment) && $ledger->generated_source->attachment !== ''){
                    $attachment = asset(Storage::url($ledger->generated_source->attachment));
                    $suffix.= ' <a href="'.$attachment.'" target="_blank">
                        <i class="la la-file-picture-o"></i>
                    </a>';
                }
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->addon->setting->title. ' Expense | '. $ledger->generated_source->type .' | '.Carbon::parse($ledger->generated_source->month)->format('M Y').$suffix.'</span>
                    '.(isset($ledger->generated_source->description)?('<span class="description-subtitle">'.$ledger->generated_source->description.'</span>'):'').'
                ';
                break;
            case 'driver_visa_expense':
                $prefix = 'DVX-'.$ledger->id;
                # Check if attachment found
                $suffix='';
                if(isset($ledger->generated_source->attachment) && $ledger->generated_source->attachment !== ''){
                    $attachment = asset(Storage::url($ledger->generated_source->attachment));
                    $suffix.= ' <a href="'.$attachment.'" target="_blank">
                        <i class="la la-file-picture-o"></i>
                    </a>';
                }

                $drivername=  '';
                if($ledger->generated_source->driver_id){
                    $drivername = ' | <a href="'.route('tenant.admin.drivers.viewDetails',$ledger->generated_source->driver_id).'">' .Driver::find($ledger->generated_source->driver_id)->name.'</a>';
                }
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->type.' | '.$ledger->generated_source->description. $drivername .' | '.Carbon::parse($ledger->generated_source->month)->format('M Y').$suffix.'</span>
                ';
                break;
            case 'invoice':
                $prefix = 'INV-'.$ledger->id;
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">Invoice #'.$ledger->source_id.' Payment Received | '.Carbon::parse($ledger->generated_source->date)->format('d M, Y').'</span>
                ';
                break;
            case 'invoice_payment':
                $prefix = 'INV-'.$ledger->id;
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">Invoice Payment Received</span>
                    <span class="description-subtitle">'.$ledger->generated_source->description.'</span>
                ';
                break;
            case 'multi_invoice_payment':
                $prefix = 'INV-'.$ledger->id;
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">Multiple Invoice Payments Received</span>
                    <span class="description-subtitle">'.$ledger->generated_source->description.'</span>
                ';
                break;
            case 'client_payment':
                $prefix = 'CP-'.$ledger->id;
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">Client Extra Payment</span>
                    <span class="description-subtitle">'.$ledger->generated_source->description.'</span>
                ';
                break;
            case 'interbooking_transfer_out':
                $prefix = 'VBT-' . $ledger->id;
                if(isset($ledger->props['prefix']['url']) && isset($ledger->props['prefix']['text'])){
                    $prefix .= " | <a href='".$ledger->props['prefix']['url']."'>".$ledger->props['prefix']['text']."</a>";
                }
                # Check if attachment found
                $suffix='';
                if(isset($ledger->generated_source->attachment) && $ledger->generated_source->attachment !== ''){
                    $attachment = asset(Storage::url($ledger->generated_source->attachment));
                    $suffix.= ' <a href="'.$attachment.'" target="_blank">
                        <i class="la la-file-picture-o"></i>
                    </a>';
                }

                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->title.$suffix.'</span>
                    <span class="description-subtitle">'.(isset($ledger->generated_source->description) && $ledger->generated_source->description !== ''? $ledger->generated_source->description:'').'</span>';
                break;
            case 'interbooking_transfer_in':
                $prefix = 'VBT-'.$ledger->id;

                # Check if attachment found
                $suffix='';
                if(isset($ledger->generated_source->attachment) && $ledger->generated_source->attachment !== ''){
                    $attachment = asset(Storage::url($ledger->generated_source->attachment));
                    $suffix.= ' <a href="'.$attachment.'" target="_blank">
                        <i class="la la-file-picture-o"></i>
                    </a>';
                }

                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->title.$suffix.'</span>
                    <span class="description-subtitle">'.(isset($ledger->generated_source->description) && $ledger->generated_source->description !== ''? $ledger->generated_source->description:'').'</span>
                ';
                break;
            case 'amount_transfer':
            case 'statementledger_transaction':
                $prefix = 'SL-'.$ledger->id;

                if(isset($ledger->props) && isset($ledger->props['prefix']) && isset($ledger->props['prefix']['text'])){
                    # Add additional dynamic prefix
                    if(isset($ledger->props['prefix']['url'])){
                        $prefix .= " | <a href='".$ledger->props['prefix']['url']."'>".$ledger->props['prefix']['text']."</a>";
                    }
                    else{
                        $prefix .= " | ".$ledger->props['prefix']['text'];
                    }
                }

                # Check if attachment found
                $suffix='';
                if(isset($ledger->generated_source->attachment) && $ledger->generated_source->attachment !== ''){
                    $attachment = asset(Storage::url($ledger->generated_source->attachment));
                    $suffix.= ' <a href="'.$attachment.'" target="_blank">
                        <i class="la la-file-picture-o"></i>
                    </a>';
                }

                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->title.$suffix.'</span>
                    <span class="description-subtitle">'.(isset($ledger->generated_source->description) && $ledger->generated_source->description !== ''?' | '.$ledger->generated_source->description:'').'</span>
                ';

                break;
            case 'vehicle_booking':
                $prefix = 'BK-'.$ledger->id;
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">Booking Deposit</span>
                    <span class="description-subtitle">'.$ledger->generated_source->notes.'</span>
                ';
                break;
            case 'partinvoice':
                $prefix = 'PINV-'.$ledger->id;
                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">Part Purchased'.(isset($ledger->generated_source->ref_invoice_no)?' | '.$ledger->generated_source->ref_invoice_no:'').'</span>
                ';
                break;
            # All employee ledger entries
            case 'discipline_fine':
            case 'bonus':
            case 'advance':
            case 'employee_ledger':
            case 'salary_paid':
            case 'salary':
                if($tag=='bonus') $prefix = 'B-'.$ledger->id;
                else if($tag=='discipline_fine') $prefix = 'DF-'.$ledger->id;
                else if($tag=='advance') $prefix = 'AV-'.$ledger->id;
                else if($tag=='salary_paid') $prefix = 'SP-'.$ledger->id;
                else if($tag=='employee_ledger') $prefix = 'EL-'.$ledger->id;
                else if($tag=='salary') $prefix = 'ES-'.$ledger->id;

                $prefix .= " | ".$ledger->generated_source->user->name;


                # Check if attachment found
                $suffix='';
                if(isset($ledger->generated_source->attachment) && $ledger->generated_source->attachment !== ''){
                    $attachment = asset(Storage::url($ledger->generated_source->attachment));
                    $suffix.= ' <a href="'.$attachment.'" target="_blank">
                        <i class="la la-file-picture-o"></i>
                    </a>';
                }

                $desc = '
                    <span class="description-subtitle">'.$prefix.'</span>
                    <span class="description-title">'.$ledger->generated_source->title.$suffix.'</span>
                    <span class="description-subtitle">'.(isset($ledger->generated_source->description) && $ledger->generated_source->description!=''?' | '.$ledger->generated_source->description:'').'</span>
                ';
                break;

            default:
                $desc='<span class="text-danger">Cannot generate appropriate description</span>';
                break;
        }

        return $desc;
    }

    public static function generate_source($ledgers)
    {
        $models = collect([]);

        foreach ($ledgers as $item) {
            # in case source_id is null
            if(!isset($item->source_id))continue;
            $model = $item->source_model;

            #check if model already exists in collection
            $model_found = $models->first(function ($value, $key)  use ($model){
                return $value->name == $model;
            });

            if(isset($model_found)){
                #update the ids
                $model_found->ids->push($item->source_id);
            }
            else{
                #insert new model
                $models->push((object)[
                    'name'=>$model,
                    'ids'=>collect([
                        $item->source_id
                    ])
                ]);
            }

        }


        #query for each model to find source
        foreach ($models as $model) {
            $class=$model->name;
            # find the source in respected table

            # Need to check if source is employee ledger, we need to find the user too
            if($class == get_class(new \App\Models\Tenant\Employee_ledger)){
                $collection = $class::with('user')->whereIn((new $class)->getKeyName(),$model->ids->toArray())->get();
            }
            else if($class == get_class(new \App\Models\Tenant\AddonExpense)){
                $collection = $class::with([
                    'addon.link' => function($query){
                        $query->select('id', 'setting_id','source_type','source_id');
                    },
                    'addon.setting' => function($query){
                        $query->select('id', 'title');
                    }
                ])
                ->whereIn((new $class)->getKeyName(),$model->ids->toArray())->get();
            }
            else{
                $collection = $class::whereIn((new $class)->getKeyName(),$model->ids->toArray())->get();
            }

            $model->collection = $collection->toArray();
            #attach the source in ledger array
            foreach ($collection as $item) {
                $ledger_found = $ledgers->first(function ($value, $key)  use ($item){
                    return $value->source_id == $item->id;
                });
                if(isset($ledger_found))$ledger_found->generated_source = (object)$item;
            }

        }
    }
}
