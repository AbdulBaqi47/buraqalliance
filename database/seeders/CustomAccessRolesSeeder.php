<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Role;

class CustomAccessRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**
         * ------------------------------
         * Add any custom access here:
         *  : Check Access [Backend]: app('helper_service')->routes->has_custom_access('{tag}', ['{sub_tag}'])
         *  : Check Access [Frontend - Blade]: $helper_service->routes->has_custom_access('{tag}', ['{sub_tag}'])
         *  : PS: if sub_tag param not defined or is null, it will check access for all module
         * ------------------------------
         *
         * PS: Add data source of any module that required partial access in "\App\Http\Controllers\Tenant\EmployeeController::showCustomRoutesForm" method
         */

        if(!Role::where('tag', 'entry_access')->exists()){
            $role = new Role;
            $role->title = 'Entry access';
            $role->description = "Access to daily ledger's entries";
            $role->tag = 'entry_access';
            $role->save();
        }

        if(!Role::where('tag','addon_department')->exists()){
            $role = new Role;
            $role->title = 'Addon Departments';
            $role->description = "";
            $role->tag = 'addon_department';
            $role->save();
        }

        if(!Role::where('tag','bookings_closing_balance')->exists()){
            $role = new Role;
            $role->title = 'Closing Balance';
            $role->description = "<b>Closing Balance</b> column in Bookings / Vehicle pages";
            $role->tag = 'bookings_closing_balance';
            $role->save();
        }

        if(!Role::where('tag','negative_account_balance')->exists()){
            $role = new Role;
            $role->title = 'Negative Balance (Accounts)';
            $role->description = "This will allow negative balance for accounts, means entries can be added without balance validation.";
            $role->tag = 'negative_account_balance';
            $role->save();
        }
    }
}
