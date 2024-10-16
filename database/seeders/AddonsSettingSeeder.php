<?php

namespace Database\Seeders;

use App\Models\Tenant\AddonsSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddonsSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        # ----------------------
        # Driver Visa Settings
        # ----------------------
        if(!AddonsSetting::where('title', 'Visa')->where('source_type', 'driver')->exists()){
            AddonsSetting::create([
                'title'=> 'Visa',
                'amount'=> 0,
                'source_type'=> 'driver',
                'source_required'=> true,
                'types' => [
                    [
                        "title" => 'Job Offer + Contract',
                        "display_title" => "Job Offer + Contract",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Dubai Insurance',
                        "display_title" => "Dubai Insurance",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Labour fees',
                        "display_title" => "Labour fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Entry Permit',
                        "display_title" => "Entry Permit",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Change Status',
                        "display_title" => "Change Status",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Medical Typing',
                        "display_title" => "Medical Typing",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Emirates ID Typing',
                        "display_title" => "Emirates ID Typing",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Tawjeeh Class Fees',
                        "display_title" => "Tawjeeh Class Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'New Residency Fees',
                        "display_title" => "New Residency Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Additional Fees',
                        "display_title" => "Additional Fees",
                        "amount" => null,
                        "charge" => true
                    ]    
                ],
                'categories' => [
                    [
                        "handle" => 'visa-type',
                        "title" => "Visa Type",
                        "field_type" => "radio",
                        "field_values" => "New,Renew",
                        "required" => true
                    ],
                    [
                        "handle" => 'status',
                        "title" => "Status",
                        "field_type" => "radio",
                        "field_values" => "Inside,Outside",
                        "required" => true
                    ],
                    [
                        "handle" => 'driving-license',
                        "title" => "Driving License",
                        "field_type" => "radio",
                        "field_values" => "Yes,No",
                        "required" => true
                    ],
                    [
                        "handle" => 'visa-no',
                        "title" => "Visa No",
                        "field_type" => "radio",
                        "field_values" => "1st,2nd",
                        "required" => true
                    ]    
                ],
                'conditions' => []
                
            ]);
        }

        # --------------------------------
        # Driver License Dubai Settings
        # --------------------------------
        if(!AddonsSetting::where('title', 'Driving License Dubai')->where('source_type', 'driver')->exists()){
            AddonsSetting::create([
                'title'=> 'Driving License Dubai',
                'amount'=> 0,
                'source_type'=> 'driver',
                'source_required'=> true,
                'types' => [
                    [
                        "title" => 'Eye Test',
                        "display_title" => "Eye Test",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'File Registration Fees ( 40 Classes )',
                        "display_title" => "File Registration Fees ( 40 Classes )",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'File Registration Fees ( 20 Classes )',
                        "display_title" => "File Registration Fees ( 20 Classes )",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Parking Fail + Extra Class',
                        "display_title" => "Parking Fail + Extra Class",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Assessment Fail + Extra Class',
                        "display_title" => "Assessment Fail + Extra Class",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Final Test Fail + Extra Class',
                        "display_title" => "Final Test Fail + Extra Class",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'License Issue Fees',
                        "display_title" => "License Issue Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Other country License charges',
                        "display_title" => "Other country License charges",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Theory Test Fail',
                        "display_title" => "Theory Test Fail",
                        "amount" => null,
                        "charge" => true
                    ]        
                ],
                'categories' => [],
                'conditions' => []
                
            ]);
        }

        # -----------------------------------
        # Driver License Sharjah Settings
        # -----------------------------------
        if(!AddonsSetting::where('title', 'Driving License Sharjah')->where('source_type', 'driver')->exists()){
            AddonsSetting::create([
                'title'=> 'Driving License Sharjah',
                'amount'=> 0,
                'source_type'=> 'driver',
                'source_required'=> true,
                'types' => [
                    [
                        "title" => 'Eye Test',
                        "display_title" => "Eye Test",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'MOI Fees',
                        "display_title" => "MOI Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'File Opening Fees',
                        "display_title" => "File Opening Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Registration Fees',
                        "display_title" => "Registration Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Parking Fail + Extra Class',
                        "display_title" => "Parking Fail + Extra Class",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Assessment Fail + Extra Class',
                        "display_title" => "Assessment Fail + Extra Class",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Final Test Fail + Extra Class',
                        "display_title" => "Final Test Fail + Extra Class",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'License Issue Fees',
                        "display_title" => "License Issue Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Number Change',
                        "display_title" => "Number Change",
                        "amount" => null,
                        "charge" => false
                    ]        
                ],
                'categories' => [],
                'conditions' => []
                
            ]);
        }

        # ---------------------
        # Driver RTA Settings
        # ---------------------
        if(!AddonsSetting::where('title', 'RTA')->where('source_type', 'driver')->exists()){
            AddonsSetting::create([
                'title'=> 'RTA',
                'amount'=> 0,
                'source_type'=> 'driver',
                'source_required'=> true,
                'types' => [
                    [
                        "title" => 'English Test Fees',
                        "display_title" => "English Test Fees",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'RTA Medical Fees',
                        "display_title" => "RTA Medical Fees",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'CID Police Report',
                        "display_title" => "CID Police Report",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Induction Training ETDI Fees',
                        "display_title" => "Induction Training ETDI Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'RTA Induction Fees',
                        "display_title" => "RTA Induction Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'RTA E-Test Theory Test',
                        "display_title" => "RTA E-Test Theory Test",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'RTA Card Fees',
                        "display_title" => "RTA Card Fees",
                        "amount" => null,
                        "charge" => false
                    ],
                    [
                        "title" => 'Refresher Training ETDI Fees',
                        "display_title" => "Refresher Training ETDI Fees",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'Refresher Training RTA Fees',
                        "display_title" => "Refresher Training RTA Fees",
                        "amount" => null,
                        "charge" => true
                    ],
                    [
                        "title" => 'RTA Learning Fees',
                        "display_title" => "RTA Learning Fees",
                        "amount" => null,
                        "charge" => false
                    ]        
                ],
                'categories' => [],
                'conditions' => []
                
            ]);
        }

    }
}
