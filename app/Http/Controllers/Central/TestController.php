<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant\User;
use Illuminate\Support\Str;

class TestController extends Controller
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

    public function index()
    {
        $tenant_name = 'gg5';
        $tenant =\App\Models\Central\Tenant::create([
            'id' => $tenant_name,
        ]);
        // return  $tenant1->domains()->get();
        $tenant->domains()->create(['domain' => $tenant_name.'.buraqalliancev2.test']);

        $tenant->run(function () use ($tenant_name) {
            User::create([
                'name' => 'Admin',
                'email' => $tenant_name.'@buraqalliance.com',
                'user_type'=>'employee',
                'designation'=>'Super User',
                'email_verified_at' => null,
                'password' => bcrypt('buraq!!@@47'), // password
                'remember_token' => Str::random(10),
                'type'=>'su',
                'props'=>[],
                'status'=>1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        });

        return 'Disabled by admin';

        return '';
    }

}
