<?php

namespace App\Services\Injected;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant\Employee_ledger;
use App\Models\Tenant\Part;
use App\Models\Tenant\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/*
| ----------------------------------------------------
|   Responsible for calling some functions in views.
| ----------------------------------------------------
|
|
|
|
*/
class HelperService
{
    /**
     * Create new instance of RouteService.
     *
     */
    public function __construct()
    {
    }

    /* Will check if logged in user is super user */
    public function isSuperUser()
    {
        if(Auth::check()){
            return Auth::user()->type === "su";
        }
        return false;
    }

    /* Will check if logged in user is super user */
    public function parseNumber($value)
    {
        return floatval(str_replace(",","", trim($value)));
    }

    public function calculate_salary($month,$employee_id, $selection=null)
    {
        /**
         * -----------------------------------------------------------------------
         *                              PAYLOAD
         * -----------------------------------------------------------------------
        */
        #Payload formatting
        $month = Carbon::parse($month)->format('Y-m-d');
        $onlyMonth=Carbon::parse($month)->format('m');
        $onlyYear=Carbon::parse($month)->format('Y');

        #set $data_only based on selection
        $selection = (object)$selection;
        $previous_ledger=null;
        $current_ledger=null;
        $employee_ledger=null;
        $employee=null;

        $show_previous = isset($selection->show_previous)&&$selection->show_previous==0?false:true;
        $show_current = isset($selection->show_current)&&$selection->show_current==0?false:true;
        $show_generate = isset($selection->show_generate)&&$selection->show_generate==0?false:true;

        /**
         * -----------------------------------------------------------------------
         *                              QUERIES
         * -----------------------------------------------------------------------
        */
        #get previous balance
        if($show_previous){
            $previous_ledger=Employee_ledger::raw(function($collection) use ($employee_id, $month){
                return $collection->aggregate([
                    [
                        '$addFields'=> [
                            'formatted_month' => ['$toDate'=> "$month"],
                        ]
                    ],
                    [
                        '$match'=> [
                            '$expr'=>[ '$lt'=> [ '$month','$formatted_month' ] ],
                            "user_id"=>"$employee_id",
                            "deleted_at" => null // Exclude soft deleted
                        ]
                    ],
                    [
                        '$project'=>['formatted_month'=>0]
                    ],
                    [
                        '$group'=> [
                            "_id"=> NULL,
                            "cr"=> [
                                '$sum'=> [
                                    '$cond'=> [
                                        [ '$eq'=> [ '$type', 'dr' ] ],
                                        0,
                                        '$amount'

                                    ]
                                ]
                            ],
                            "dr"=> [
                                '$sum'=> [
                                    '$cond'=> [
                                        [ '$eq'=> [ '$type', 'cr' ] ],
                                        0,
                                        '$amount'

                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        '$project'=> [
                            '_id'=>0,
                            "cr"=> '$cr',
                            "dr"=> '$dr',
                            "balance" => [ '$subtract' => [ '$cr', '$dr' ] ],
                        ]
                    ]
                ]);
            })->first();
        }
        if($show_current){
            $current_ledger=Employee_ledger::raw(function($collection) use ($employee_id, $month){
                return $collection->aggregate([
                    [
                        '$addFields'=> [
                            'formatted_month' => ['$toDate'=> "$month"],
                        ]
                    ],
                    [
                        '$match'=> [
                            '$expr'=>[ '$eq'=> [ '$month','$formatted_month' ] ],
                            "user_id"=>"$employee_id",
                            "deleted_at" => null // Exclude soft deleted
                        ]
                    ],
                    [
                        '$project'=>['formatted_month'=>0]
                    ],
                    [
                        '$group'=> [
                            "_id"=> NULL,
                            "cr"=> [
                                '$sum'=> [
                                    '$cond'=> [
                                        [ '$eq'=> [ '$type', 'dr' ] ],
                                        0,
                                        '$amount'

                                    ]
                                ]
                            ],
                            "dr"=> [
                                '$sum'=> [
                                    '$cond'=> [
                                        [ '$eq'=> [ '$type', 'cr' ] ],
                                        0,
                                        '$amount'

                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        '$project'=> [
                            '_id'=>0,
                            "cr"=> '$cr',
                            "dr"=> '$dr',
                            "balance" => [ '$subtract' => [ '$cr', '$dr' ] ],
                        ]
                    ]
                ]);
            })->first();
        }
        #get Employee ledger collection to get the user data
        if($show_generate){
            $employee_ledger=Employee_ledger::raw(function($collection) use ($onlyMonth,$onlyYear, $employee_id){
                return $collection->aggregate([
                    [
                        '$addFields'=> [
                            'g_year' => ['$year'=> '$month'],
                            'g_month'=> ['$month'=> '$month']
                        ]
                    ],
                    [
                        '$match'=> [
                            'g_month'=>(int)$onlyMonth,
                            'g_year'=> (int)$onlyYear,
                            'user_id'=>$employee_id,
                            "deleted_at" => null // Exclude soft deleted
                        ]
                    ],
                    [
                        '$project'=>['g_month'=>0, 'g_year'=>0]
                    ]
                ]);
            });
            $employee = User::find($employee_id);
        }

        /**
         * -----------------------------------------------------------------------
         *               VARIABES & DATA FETCHING BASED ON $selection
         * -----------------------------------------------------------------------
        */

        #Variables
        $basic_salary=0;

        $previous_balance=isset($previous_ledger->balance)?$previous_ledger->balance:0;
        $current_balance=isset($current_ledger->balance)?$current_ledger->balance:0;

        $current_cr=isset($current_ledger->cr)?$current_ledger->cr:0;
        $current_dr=isset($current_ledger->dr)?$current_ledger->dr:0;

        #end variable to return
        $response=collect([]);


        #get user to get the basic salary
        if (isset($employee->props)) {
            $basic_salary=$employee->props['basic_salary'];
        }

        # By default, employee salary is basic salary
        $generated_salary=$basic_salary;

        #Need to check if salary is already generated and present in ledger
        $already_generated_salary=0;
        $is_generated=false;
        if(isset($employee_ledger))$already_generated_salary=$employee_ledger->where('tag','salary')->sum('amount');

        if($already_generated_salary>0){
            # It seems some salary is already generated, we need to overwrite the generated salary
            $generated_salary = $already_generated_salary;

            # Seems salary is already generated
            $is_generated=true;
        }

        #Payable salary
        $payable_salary = $current_balance+$previous_balance;


        /**
         * -----------------------------------------------------------------------
         *                          RESPONSE CREATION
         * -----------------------------------------------------------------------
        */
        #Creating response
        $response =(Object)[];
        $response->generated_salary=round($generated_salary,2);
        $response->basic_salary=round($basic_salary,2);
        $response->employee=$employee;
        $response->previous_balance=round($previous_balance,2);
        $response->payable_salary=round($payable_salary,2);
        $response->is_generated=$is_generated;

        # /START | For debugging purpose, (PLEASE REMOVE THIS DATA ON PRODUCTION)
        // $response->employee_ledger=$employee_ledger;
        // $response->previous_ledger=$previous_ledger;
        // $response->current_ledger=$current_ledger;
        # /END | For debugging purpose

        return $response;
    }

    public function getConfig()
    {
        # Set default values
        $cookie = (object)[
            'sidebar'=>1,
            'clientdetails_sidebar'=>1,
        ];
        if(isset($_COOKIE['KRCONFIG'])){
            $cookie = (object)json_decode($_COOKIE['KRCONFIG'], true);
        }
        return $cookie;
    }

    public function request_type()
    {
        if(\Request::ajax())return 'ajax';

        return 'http';
    }

    public function storage_basepath()
    {
        return Storage::url('');
    }

    /**
     * Generate a random unique ID
     *
     * @param integer $length Length of unqiue string
     *
     */
    public function generateUniqueId($length = 8)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        $crypto_rand_secure = function ($min, $max) {
            $range = $max - $min;
            if ($range < 1) {
                return $min;
            }
            // not so random...
            $log = ceil(log($range, 2));
            $bytes = (int) ($log / 8) + 1; // length in bytes
            $bits = (int) $log + 1; // length in bits
            $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
            do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd = $rnd & $filter; // discard irrelevant bits
            } while ($rnd > $range);
            return $min + $rnd;
        };

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$crypto_rand_secure(0, $max - 1)];
        }

        return $token;
    }

    public function low_inventory($countOnly = false)
    {
        # Count of all parts having low inventory
        $parts = Part::where('low_inventory_qty', '>', 0)
        ->get()
        ->filter(function($part){
            $inventory_remaining = $part->inventory->remaining;

            # If remaining inventiry is lower than alert threshold
            return $inventory_remaining < $part->low_inventory_qty;
        });;

        if($countOnly){
            return $parts->count();
        }
        return $parts;
    }

    public function negativeBalanceData() : array|object {
        $negativeAccessRole = Auth::user()->getCustomRole('negative_account_balance');
        $negativeAccessToAll = isset($negativeAccessRole) && $negativeAccessRole->access_scope === "all";
        $negativeAccessIds = isset($negativeAccessRole) && isset($negativeAccessRole->access_data) ? $negativeAccessRole->access_data : [];

        return (object) [
            'all' => $negativeAccessToAll,
            'ids' => $negativeAccessIds
        ];
    }
}
