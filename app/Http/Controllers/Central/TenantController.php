<?php

namespace App\Http\Controllers\Central;

use App\Accounts\Models\Account_log;
use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\Tenant\Log;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function view()
    {
        return view('Central.tenants.view');

    }

    /**
     * Create view Page of tenants addition
     *
    */
    public function showTenantForm($config=null)
    {
        return view('Central.tenants.create', compact('config'));
    }

    /**
     * POST request of creating the tenant
     *
    */
    public function create(Request $request)
    {

        $request->validate([
            'name' => 'required|max:255',
            'domain'    => 'required|max:255|unique:domains,domain|regex:/^[A-Za-z0-9\-]*$/',
            'su_name' => 'required|max:255',
            'su_email' => 'required|max:255',
            'su_password' => 'required|confirmed|min:8',
        ]);
        $domain_prefix = strtolower($request->get('domain', ''));
        $domain = $domain_prefix.'.buraqalliance.com';
        $name = $request->get('name', '');

        $tenant = Tenant::create([
            'id' => $domain_prefix,
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $tenant
        ->domains()
        ->create([
            'domain' => $domain
        ]);

        $tenant->run(function () use ($request) {
            User::create([
                'name' => $request->su_name,
                'email' => $request->su_email,
                'user_type'=>'employee',
                'designation'=>'Super User',
                'email_verified_at' => null,
                'password' => bcrypt($request->su_password), // password
                'remember_token' => Str::random(10),
                'type'=>'su',
                'props'=>[
                    'default' => true
                ],
                'status'=>1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        });


        return redirect()->route('central.admin.tenants.view')->with('message', "Tenant created successfully!");

    }

    /**
     * GET request of editing the page
     *
    */
    public function showEditForm()
    {
        $id = request()->get('id');

        # Find the modal
        $tenant = Tenant::findOrFail($id);
        $tenant->actions=[
            'status'=>1,
        ];

        $tenant->domain_name = $tenant->id;

        $default_user = null;
        $tenant->run(function () use (&$default_user) {
            $default_user = User::where('props.default', true)->first();
        });

        $tenant->default_user = $default_user;

        # Call the load function
        return $this->showTenantForm((object)[
            'tenant'=>$tenant,
            'action'=>'edit'
        ]);

    }

    public function edit(Request $request)
    {

        $tenant = Tenant::findOrFail($request->tenant_id);

        $request->validate([
            'tenant_id' => 'required|max:255',
            'name' => 'required|max:255',
            'su_name' => 'required|max:255',
            'su_email' => 'required|max:255',
        ]);

        $name = $request->get('name', '');

        $tenant->name = $name;
        $tenant->update();

        $tenant->run(function () use ($request) {
            $default_user = User::where('props.default', true)->first();
            $change_pass = false;
            if($request->has('su_change_password'))$change_pass = true;

            if(isset($default_user)){
                $default_user->name = $request->su_name;
                $default_user->email = $request->su_email;
                if($change_pass)$default_user->password = Hash::make($request->su_password);
                $default_user->update();
            }
            else{
                $p = $request->su_password;
                if(!$change_pass){
                    $p = 'admin!!@@3';
                }
                # Create new user
                User::create([
                    'name' => $request->su_name,
                    'email' => $request->su_email,
                    'user_type'=>'employee',
                    'designation'=>'Super User',
                    'email_verified_at' => null,
                    'password' => bcrypt($p), // password
                    'remember_token' => Str::random(10),
                    'type'=>'su',
                    'props'=>[
                        'default' => true
                    ],
                    'status'=>1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

        });


        return redirect()->route('central.admin.tenants.view')->with('message', "Tenant updated successfully!");
    }

    /**
     * DELETE request of Delete the tenant along with DB + DOMAIN
     *
    */
    public function delete($id)
    {
        $tenant = Tenant::findOrFail($id);

        $tenant->domains()->delete();

        $tenant->delete();

        return response()->json([
            'status' => 1
        ]);
    }
}
