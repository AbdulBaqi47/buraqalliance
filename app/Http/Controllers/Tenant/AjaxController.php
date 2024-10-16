<?php

namespace App\Http\Controllers\Tenant;

use App\Accounts\Models\Account_log;
use App\Accounts\Models\Account_relation;
use App\Accounts\Models\Account_transaction;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Addon;
use App\Models\Tenant\AddonsSetting;
use App\Models\Tenant\VehicleBillsSetting;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;
use App\Models\Tenant\Job;
use App\Models\Tenant\Part;
use App\Models\Tenant\Partinvoice;
use App\Models\Tenant\Client;
use App\Models\Tenant\Bike;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\User;
use App\Models\Tenant\Employee_ledger;
use App\Models\Tenant\Installment;
use App\Models\Tenant\ImportHistory;
use App\Models\Tenant\Investor;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Job_service;
use App\Models\Tenant\Log;
use App\Models\Tenant\Role;
use App\Models\Tenant\Report;
use App\Models\Tenant\Sim;
use App\Models\Tenant\SimEntity;
use App\Models\Tenant\Task;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\StatementLedger;
use App\Models\Tenant\StatementLedgerItem;
use App\Models\Tenant\StatementLedgerItemGroup;
use App\Services\InjectService;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AjaxController extends Controller
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
     * Route: tenant.admin.drivers.data
     * PageRoute: admin.drivers.view
    */
    public function getDrivers(Request $request ,$type)
    {
        $drivers = Driver::with([
            'booking.investor',
            'booking.vehicle',
            'client_entities.client',
            'addons.setting',
            'sim_entities'=>function($query){
                $query->whereNull('unassign_date');
            },
            'sim_entities.sim',
            'vehicle_entities'=>function($query){
                $query->whereNull('unassign_date');
            },
            'vehicle_entities.vehicle'
         ])->is($type)->get();

         return Datatables::of($drivers)
         ->addColumn('actions', function($driver){
             return [
                 'status'=>1,
             ];
         })
        ->editColumn('status', function($driver){
            return $driver->status ?? '';
        })
        ->editColumn('date', function($driver) {
            return Carbon::parse($driver->date)->format('F d, Y');
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
    /**
     * Route: admin.installments.data
     * PageRoute: admin.installments.view
    */
    public function getInstallments(Request $request)
    {
        $request->validate([
            'type' => 'in:pending,charged,all'
        ]);
        $queryBuilder = Installment::with(['source','account' => function($query) {
            $query->pluck('title');
        }]);
        if($request->type === 'pending'){
            $installments = $queryBuilder->whereNull('transaction_ledger_id')->get();
        }else if($request->type === 'charged'){
            $installments = $queryBuilder->whereNotNull('transaction_ledger_id')->get();
        }else{
            $installments = $queryBuilder->get();
        }
        return Datatables::of($installments)
        ->addColumn('actions', function($installment){
            return [
                'status'=>1,
            ];
        })
        ->editColumn('charge_date',function($item){
            return Carbon::parse($item->charge_date)->format('Y-m-d');
        })
        ->editColumn('pay_date',function($item){
            return Carbon::parse($item->pay_date)->format('Y-m-d');
        })
        ->addColumn('status', function($item){
            return $item->transaction_ledger_id === null ? "Pending": "Charged";
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.addons.data
     * PageRoute: admin.addons.view
    */
    public function getAddons(Request $request)
    {
        $route = $request->route;
        $filter_required = $request->showAll === 'false';

        if(isset($request->showCompleted)){
            $show_completed = ($request->showCompleted === 'true') ? '=':'!=';
            $text_value = 'completed';
        }
        else{
            $show_completed = '!=';
            $text_value = 'x';
        }

        $routeToSettingTitle = [
            'rta_card' => ['RTA'],
            'visa_department' => ['Visa'],
            'driving_license' => ['Driving License Dubai','Driving License Sharjah'],
            'driving_license_dubai' => ['Driving License Dubai'],
            'driving_license_sharjah' => ['Driving License Sharjah'],
        ];

        $addons = Addon::with([
            'setting' => function($query){
                if(isset($query))$query->select('id', 'title', 'source_type', 'source_required');
            },
            'link' => function($query){
                $query->select('id', 'plate', 'chassis_number', 'vehicle_booking_id', 'name', 'booking_id');
            },
            'expenses' => function($query){
                $query->select('addon_id','given_date','amount', 'charge_amount', 'type', 'description');
            },
            'deductions' => function($query){
                $query->select('addon_id', 'date', 'amount');
            },
        ])
        ->where('status', $show_completed ,$text_value)
        ->whereHas('setting',function($query) use ($route, $routeToSettingTitle){
            if(isset($routeToSettingTitle[$route])) {
                $query->whereIn('title', $routeToSettingTitle[$route]);
            }
        })
        ->get();

        if($filter_required){
            $addons = $addons->filter(function($addon){
                return $addon->breakdown->remaining > 0;
            });
        }

        return Datatables::of($addons)
        ->addColumn('actions', function($addon){
            return [
                'status'=>1,
            ];
        })
        ->editColumn('status', function($addon){
            return $addon->status ?? null;
        })
        ->editColumn('payment_status', function($addon){
            return $addon->payment_status ?? null;
        })
        ->editColumn('price', function($addon){
            return $addon->breakdown->total_price;
        })
        ->addColumn('source', function($addon){
            if(!isset($addon->source_id)) return 'Available';

            if($addon->source_type === "driver"){
                return $addon->link->full_name;
            }
            if($addon->source_type === "staff"){
                return $addon->link->name;
            }
            if($addon->source_type === "vehicle" && $addon->source_model === VehicleBooking::class){
                return 'B#'.$addon->link->id;
            }

            return (isset($addon->link->vehicle_booking_id) ? 'V#' . $addon->link->vehicle_booking_id . ' / ' : '') . $addon->link->plate.' / '.$addon->link->chassis_number;
        })
        ->addColumn('remaining', function($addon){
            return $addon->breakdown->remaining;
        })
        ->rawColumns(['actions', 'readable_details'])

        # Remove not-needed column, so response will be less to download
        ->removeColumn('expenses')
        ->removeColumn('deductions')

        ->make(true);
    }

    /**
     * Route: admin.reports.data
     * PageRoute: admin.reports.view
    */
    public function getReports(Request $request)
    {


        $reports = Report::all();

        return Datatables::of($reports)
        ->addColumn('actions', function($report){
            return [
                'status'=>1,
            ];
        })
        ->make(true);
    }

    /**
     * Route: admin.drivers.addons.data
     * PageRoute: admin.drivers.viewDetails
    */
    public function getDriverAddons(Request $request, int $driver_id)
    {
        $addons = Addon::with([
            'setting' => function($query){
                if(isset($query))$query->select('id', 'title', 'source_type', 'source_required');
            },
            'driver' => function($query){
                if(isset($query))$query->select('id','name', 'booking_id');
            },
            'expenses' => function($query){
                $query->select('addon_id','given_date','amount', 'charge_amount', 'type', 'description','date');
            },
            'deductions' => function($query){
                $query->select('addon_id', 'date', 'amount','date');
            },
        ])
        ->where('source_type','driver')
        ->where('source_id',$driver_id)
        ->get();

        return Datatables::of($addons)
        ->addColumn('actions', function($addon){
            return [
                'status'=>1,
            ];
        })
        ->editColumn('price', function($addon){
            return $addon->breakdown->total_price;
        })
        ->editColumn('status', function($addon){
            return $addon->status ?? null;
        })
        ->addColumn('source', function($addon){
            if(!isset($addon->source_id)) return 'Available';

            if($addon->source_type === "driver"){
                return $addon->driver->full_name;
            }
            return $addon->vehicle->plate.' / '.$addon->vehicle->chassis_number;
        })
        ->addColumn('remaining', function($addon){
            return $addon->breakdown->remaining;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.addons.setting.data
     * PageRoute: admin.addons.setting.view
    */
    public function getAddonSetting()
    {
        $addonSettings = AddonsSetting::all();

        return Datatables::of($addonSettings)
        ->addColumn('actions', function($addon){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: tenant.admin.vehicle.bills.setting.data
     * PageRoute: tenant.admin.vehicle.bills.setting.view
    */
    public function getVehicleBillsSetting()
    {
        $settings = VehicleBillsSetting::all();

        return Datatables::of($settings)
        ->addColumn('actions', function($item){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.bookings.data
     * PageRoute: admin.bookings.view
    */
    public function getBookings(Request $request)
    {
        $bookings = VehicleBooking::with([
            'investor' => function($query){
                $query->select('id', 'name', 'refid');
            },
            'vehicle_type' => function($query){
                $query->select('make', 'model', 'cc');
            },
            'drivers' => function($query){
                $query->select('id', 'name', 'booking_id');
            },
            'vehicle' => function($query){
                $query->select('id', 'vehicle_booking_id', 'plate', 'model');
            },
            'reserve_vehicle' => function($query){
                $query->select('id', 'reserve_vehicle_booking_id', 'plate', 'chassis_number');
            },
        ]);

        $status =  $request->get('status', 'open');

        $bookings->where("status", $status);

        # For Open Bookings, we have 2 pages:
        # for active & inactive bookings
        if($status === 'open'){
            $bookings->where('activation_status', $request->get('active_status', 'active'));
        }


        # Exec query
        $bookings = $bookings->get();

        $closingBalanceData = [];
        # Calculate closing balance against each booking
        if(app('helper_service')->routes->has_custom_access('bookings_closing_balance')){

            $bookingsIds = $bookings->pluck('id')->unique()->values()->toArray();
            $filter_month = Carbon::parse($request->get('filter_month'))->format('Y-m-d');
            $filter_month_range = $request->get('filter_month_range', 'with_previous');

            $closingBalanceData = VehicleBooking::fetchClosingBalance(
                $bookingsIds,
                [
                    'month' => $filter_month,
                ],
                $filter_month_range
            );

        }


        return Datatables::of($bookings)
        ->addColumn('actions', function($booking){
            return [
                'status'=>1,
            ];
        })
        ->editColumn('date', function($booking) {
            return Carbon::parse($booking->date)->format('F d, Y');
        })
        ->addColumn('closing_balance', function($booking) use ($closingBalanceData) {
            return isset($closingBalanceData[$booking->id]) ? $closingBalanceData[$booking->id] : 0;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.investors.data
     * PageRoute: admin.investors.view
    */
    public function getInvestors()
    {
        $investors = Investor::with([
            'bookings' => function($query){
                $query->select('id', 'investor_id', 'status');
            },
            'bookings.vehicle' => function($query){
                $query->select('id', 'vehicle_booking_id');
            },
            'manages',
        ]);

        # Exec query
        $investors = $investors->get();



        return Datatables::of($investors)
        ->addColumn('actions', function($investor){
            return [
                'status'=>1,
            ];
        })
        ->addColumn('bookings_count', function($investor){
            if(!isset($investor->bookings))return 0;
            return $investor->bookings->where('status', 'open')->count();
        })
        ->addColumn('vehicle_count', function($investor){
            if(!isset($investor->bookings))return 0;

            return $investor->bookings->whereNotNull('vehicle')->count();
        })
        ->addColumn('open_balance', function($investor){
            return 0;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.clients.data
     * PageRoute: admin.clients.view
    */
    public function getClients($type)
    {

        $clients = Client::with([
            'entities',
        ])
        ->is($type)
        ->orderBy('created_at')
        ->get();

        return Datatables::of($clients)
        ->addColumn('actions', function($client){
            return [
                'status'=>1,
            ];
        })
        ->addColumn('open_balance', function($client){
            return $client->open_balance ?? 0;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: tenant.admin.invoices.data
     * PageRoute: tenant.admin.invoices.view
    */
    public function getInvoices()
    {

        $invoices = Invoice::with([
            'client',
            'payment_refs.payables'
        ])
        ->orderBy('created_at')
        ->get();

        return Datatables::of($invoices)
        ->addColumn('actions', function($invoice){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.tasks.internal.data
     * PageRoute: admin.tasks.internal.view
    */
    public function getInternalTasks()
    {

        $tasks = Task::with([
            'employee' => function($query){
                $query->select('id', 'name');
            },
        ])
        ->orderByDesc('created_at')
        ->get();

        return Datatables::of($tasks)
        ->addColumn('actions', function($client){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns(['actions'])
        ->make(true);
    }


    /**
     * Route: admin.sims.data
     * PageRoute: admin.sims.view
    */
    public function getSims()
    {

        $sims = Sim::with(['entities' => function($query){
            $query->whereNull('unassign_date');
        },'entities.source'])->orderBy('created_at')->get();

        return Datatables::of($sims)
        ->addColumn('actions', function($sim){
            return [
                'status'=>1,
            ];
        })
        ->editColumn('purchasing_date', function($sim){
            if(!isset($sim->purchasing_date)) return '';
            return Carbon::parse($sim->purchasing_date)->format('M d, Y');
        })
        ->editColumn('type', function($sim){
            if(!isset($sim->type)) return '';
            return $sim->type;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }


    /**
     * Route: admin.getEntitySims.data
     * PageRoute: admin.drivers.viewDetails, admin.employee.view
    */
    public function getEntityBasedSims(Request $request)
    {
        $request->validate([
            'source_type' => ['required','in:driver,booking,staff'],
            'source_id' => ['required'],
        ]);

        $model_classes = [
            'driver' => Driver::class,
            'booking' => VehicleBooking::class,
            'staff' => User::class
        ];

        $sim_entity = SimEntity::with('sim')
        ->whereNull('unassign_date')
        ->where('source_model',$model_classes[$request->source_type])
        ->where('source_id', in_array($request->source_type, ['driver','booking']) ? intval($request->source_id) : $request->source_id)
        ->get();

        return Datatables::of($sim_entity)
        ->addColumn('actions', function($sim){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns(['actions'])
        ->make(true);
    }


    /**
     * Route: admin.vehicles.data
     * PageRoute: admin.vehicles.view
    */
    public function getVehicles($type)
    {
        $vehicles = Vehicle::with([
            'entities' => function($query){
                $query->whereNull('unassign_date')->where('source_model', Driver::class);
            },
            'entities.source',

            'vehicle_client_entities' => function($query){
                $query->whereNull('unassign_date');
            },
            'vehicle_client_entities.client'
        ]);

        $vehicles = $vehicles->is($type)->get();

        return Datatables::of($vehicles)
        ->addColumn('actions', function($vehicle){
            return [
                'status'=>1,
            ];
        })
        ->editColumn('chassis_number', function($vehicle){
            return $vehicle->chassis_number??"";
        })
        ->editColumn('engine_number', function($vehicle){
            return $vehicle->engine_number??"";
        })
        ->editColumn('model', function($vehicle){
            return $vehicle->model??"";
        })
        ->editColumn('color', function($vehicle){
            return $vehicle->color??"";
        })
        ->editColumn('plate', function($vehicle){
            return $vehicle->plate??"";
        })
        ->addColumn('state', function($vehicle){
            return $vehicle->state??"";
        })
        ->addColumn('reserve_vehicle_booking_id', function($vehicle){
            return $vehicle->reserve_vehicle_booking_id??"";
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.statementledger.data
     * PageRoute: admin.statementledger.booking.view / admin.statementledger.vehicle.view
    */
    public function getStatementLedger(Request $request, $filter = null)
    {
        # Payload
        $namespace = $request->get('namespace');
        $id = $request->get('id');
        $running_balance=0;


        $filter_type = $request->get('filter_type');
        $filter_value = $request->get('filter_value');
        $start = null;
        $end = null;

        $ledger = StatementLedger::ofNamespace($namespace, $id)->get();

        // if(!isset($ledger)){
        //     throw new Exception("Invalid namespace");
        // }


        $ledgerIds = $ledger->pluck('_id')->toArray();

        // return $ledgerIds;


        # ------------------------------------
        # Fetch dates according to selection
        # ------------------------------------

        $aggregator = null;
        $previous_aggregator = null;

        if($filter_type === "month"){
            $start = Carbon::parse($filter_value)->startOfMonth()->format('Y-m-d');
            $end = Carbon::parse($filter_value)->endOfMonth()->format('Y-m-d');


            $only_month=Carbon::parse($filter_value)->format('m');
            $only_year=Carbon::parse($filter_value)->format('Y');

            # Aggregator to fetch specified range entries
            $aggregator = [
                [
                    '$addFields'=> [

                        'g_year' => ['$year'=> ['$toDate'=> '$month'] ],
                        'g_month'=> ['$month'=> ['$toDate'=> '$month'] ]
                    ]
                ],
                [
                    '$match'=> ['g_month'=>(int)$only_month,'g_year'=> (int)$only_year, 'statement_ledger_id'=>['$in' => $ledgerIds]]
                ],
                [
                    '$project'=>['_id'=>1]
                ]
            ];

            # Aggregator to fetch previous balance
            $previous_aggregator = [

                [
                    '$match'=> [
                        '$expr'=>[ '$lt'=> [ ['$toDate'=> '$month'] ,  ['$toDate'=> "$filter_value"] ] ],
                        "statement_ledger_id"=>['$in' => $ledgerIds],
                        "deleted_at" => null // Exclude soft deleted
                    ]
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
            ];

        }
        else if($filter_type === "custom"){
            $picker_range_value = explode(',', $filter_value);

            $start = Carbon::parse(trim($picker_range_value[0]))->format('Y-m-d');
            $end = Carbon::parse(trim($picker_range_value[1]))->format('Y-m-d');

            # Aggregator to fetch specified range entries
            $aggregator = [
                [
                    '$match'=> [
                        '$expr'=>[
                            '$and' => [
                                [ '$gte'=> [ ['$toDate'=> '$date'], ['$toDate'=> "$start"] ] ],
                                [ '$lte'=> [ ['$toDate'=> '$date'], ['$toDate'=> "$end"] ] ]
                            ]
                        ],
                        'statement_ledger_id'=>['$in' => $ledgerIds],
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    '$project'=>['_id'=>1]
                ]
            ];

            # Aggregator to fetch previous balance
            $previous_aggregator = [

                [
                    '$match'=> [
                        '$expr'=>[ '$lt'=> [ ['$toDate'=> '$date'] ,  ['$toDate'=> "$start"] ] ],
                        "statement_ledger_id"=>['$in' => $ledgerIds],
                        "deleted_at" => null // Exclude soft deleted
                    ]
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
            ];
        }

        $item_ids =StatementLedgerItem::raw(function($collection) use ( $aggregator ){
            return $collection->aggregate($aggregator);
        })->pluck('_id');
        $items = StatementLedgerItem::with([
            'driver' => function($query){
                $query->select('id', 'name');
            }
        ])->whereIn('_id',$item_ids)->get();

        # ------------------------------------------
        # Group Items
        # - Custom groups (statementledger_group table)
        # ------------------------------------------


        $customGroupItems = [];


        $itemTags = $items->pluck('tag')->unique()->values()->toArray();
        $customGroups = StatementLedgerItemGroup::whereIn('tags.title', $itemTags)->get();

        # Filter items having no groups defined
        $items = $items->filter(function ($item) use ($customGroups, &$customGroupItems) {

            $foundInCustomGroup = $customGroups->contains(function ($group, $key) use ($item) {
                $tags = collect($group->tags)->map(function($item){ return $item['title']; })->values()->toArray();
                return in_array($item->tag, $tags);
            });

            if($foundInCustomGroup){
                # Store it to another array to append later
                $customGroupItems[] = $item;
            }

            return !$foundInCustomGroup;
        });


        foreach ($customGroups as $customGroup) {
            $tags = collect($customGroup->tags)->map(function($item){ return trim(strtolower($item['title'])); })->values()->toArray();

            $groups = collect([]);

            # Find the items tag by tag, and append to items in groups
            $groupItems = collect($customGroupItems)->whereIn('tag', $tags);

            if(isset($customGroup->driver_based) && $customGroup->driver_based === true){
                // Group By driver_id too
                foreach ($groupItems->groupBy('driver_id') as $key => $item) {
                    $groups->push($item);
                }
            }
            else{
                // Append as a single group
                $groups->push( $groupItems );

            }

            foreach ($groups as $group) {

                if(count($group) === 0){
                    # No items found against this group
                    continue 2;
                }

                $firstItem = $group->first();

                # Sort group
                $group = $group->sort(function ($a, $b) {
                    return strtotime($a->date) > strtotime($b->date);
                })->values();


                $baseTitle = $customGroup->title;

                $title = $baseTitle.' ['.count($group).'] <a href="" style="text-decoration:underline!important" onclick="STATEMENT_LEDGER.handleGroupDetailClick(event, this)" >[View Details]</a>';


                $firstItem->groups = $group
                ->map(function($item) {

                    // Transform attachment to URL
                    if(isset($item->attachment) && $item->attachment !== ''){
                        $item->attachment = Storage::url($item->attachment);
                    }

                    return $item;
                })
                ->toArray();


                $description = $group->map(function($i) use ($baseTitle){

                    $amount = ' ['.$i->amount.']';
                    $title = $i->title;

                    $type = '';
                    if(str_contains(strtolower($i->tag), "careem")){
                        $type = 'careem';
                    }
                    if(str_contains(strtolower($i->tag), "machines")){
                        $type = 'pos_machine';
                    }
                    if(str_contains(strtolower($i->tag), "uber")){
                        $type = 'uber';
                    }
                    if(str_contains(strtolower($i->tag), "yango")){
                        $type = 'yango';
                    }

                    if($type !== ''){
                        # Remove Client name from income entries
                        $title = str_replace($baseTitle, '', $i->title);
                    }


                    if($type === 'careem' || $type === 'uber' || $type === 'yango'){
                        # Append cash,bank too
                        $amount = ' [Total: '.$i->additional_details['base']. ($i->additional_details['cash'] != 0 ? ', Cash: '.$i->additional_details['cash'] : '' ) . ', Bank: '.$i->amount.']';
                    }

                    if($type === 'pos_machine'){
                        $title = "Date " . $title;
                    }

                    return $title . $amount;
                })->join('<br />');


                if(isset($customGroup->collapse) && $customGroup->collapse === true){
                    // No description
                    $description = '';
                }

                if(isset($customGroup->date_override) && $customGroup->date_override > 0){
                    // Make the date according to override
                    $base_date = Carbon::parse($start);
                    $date = Carbon::parse($base_date->format('Y') . '-' . $base_date->format('m') . '-' . $customGroup->date_override );
                    if($date->format('m') != $base_date->format('m')){
                        // It seems date exceed current month
                        $date = $base_date->lastOfMonth();
                    }

                    $firstItem->date = $date->format("Y-m-d");
                }

                $firstItem->amount = round( $group->sum('amount'), 2 );
                $firstItem->title = $title;
                $firstItem->st_title = $baseTitle;
                $firstItem->st_description = $description; // This will exported as excel
                $firstItem->description = $description;
                $firstItem->attachment = null; # attachments will be on details page

                $items->prepend($firstItem);
            }
        }

        # sort by asc (So we can calculate balance from start)
        $items = $items->sort(function ($a, $b) {
            return strtotime($a->date) > strtotime($b->date);
        });



        $previous_ledger = StatementLedgerItem::raw(function($collection) use ( $previous_aggregator ){
            return $collection->aggregate($previous_aggregator);
        })->first();

        $previous_balance = isset($previous_ledger->balance)?$previous_ledger->balance:0;


        $running_balance=round($previous_balance, 0);

        $flag = new StatementLedgerItem;
        $flag->_id=00001;
        $flag->month='';
        $flag->title='Opening Balance';
        $flag->description='';
        $flag->type='skip';
        $flag->tag='skip';
        $flag->amount=$running_balance;
        $flag->attachment=null;
        $items->prepend($flag);

        $flag = new StatementLedgerItem;
        $flag->_id=00002;
        $flag->month='';
        $flag->title='Closing Balance';
        $flag->description='';
        $flag->type='skip';
        $flag->tag='skip';
        $flag->amount=0;
        $flag->attachment=null;
        $items->push($flag);

        return Datatables::of($items)

        ->editColumn('date', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            return Carbon::parse($ledger_item->date)->format('M d, Y');
        })
        ->editColumn('description', function($ledger_item){
            if($ledger_item->type=='skip')return '<strong>'.$ledger_item->title.'</strong>'; # It is opening and closing rows

            $prefix = '';

            # Check if attachment found
            $suffix='';
            if(isset($ledger_item->attachment) && $ledger_item->attachment !== ''){
                $attachment = Storage::url($ledger_item->attachment);

                $suffix.= ' <a href="'.$attachment.'" target="_blank">
                    <i class="la la-file-picture-o"></i>
                </a>';
            }

            # Append "Breakdown" button to addon_charge type entires
            if( !isset($ledger_item->groups) && preg_match('/_addon$/', $ledger_item->tag) ){
                $suffix.= '
                <a href="#" kr-ajax="'.route('tenant.admin.statementledger.linked.view', $ledger_item->id).'?view=addon_breakdown" class="btn btn-sm btn-outline-primary btn-square btn-evelate ml-2 py-1 px-2" title="View BreakDown" kr-ajax-block-page-when-processing="" kr-ajax-size="70%" kr-ajax-modalclosed="STATEMENT_LEDGER.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()">
                    View Breakdown
                </a>
                ';
            }

            # Append "Breakdown" button to vehicle bills type entires
            if( !isset($ledger_item->groups) && preg_match('/_vehiclebills$/', $ledger_item->tag) ){

                $uuid = null;
                if(isset($ledger_item->additional_details) && isset($ledger_item->additional_details['uuid'])){
                    $uuid = $ledger_item->additional_details['uuid'];
                }

                $suffix.= '
                <a href="#" kr-ajax="'.route('tenant.admin.statementledger.linked.view', $ledger_item->id).'?view=vehiclebills_breakdown&uuid='.$uuid.'" class="btn btn-sm btn-outline-primary btn-square btn-evelate ml-2 py-1 px-2" title="View BreakDown" kr-ajax-block-page-when-processing="" kr-ajax-size="50%" kr-ajax-modalclosed="STATEMENT_LEDGER.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()">
                    View Breakdown
                </a>
                ';
            }

            $desc = trim($ledger_item->description);

            if(isset($ledger_item->driver_id)){
                $desc .= '<p class="m-0">Driver: <a href="'.route('tenant.admin.drivers.viewDetails', $ledger_item->driver_id).'">'.$ledger_item->driver->full_name.'</a></p>';
            }

            if(!isset($desc) || $desc==''){
                # return only title
                return $prefix . '<span class="transaction__desc-title" title="'.$ledger_item->tag.'">' . $ledger_item->title.$suffix . '</span>';
            }

            # return title along with detailed description
            return $prefix . '<span class="transaction__desc-title" title="'.$ledger_item->tag.'">'.$ledger_item->title.$suffix.'</span><span class="transaction__desc-subtitle">'.$desc.'</span>';
        })
        ->addColumn('cr', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            if ($ledger_item->type=='cr')
            {
                return $ledger_item->amount;
            }
            return 0;
        })
        ->addColumn('dr', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            if ($ledger_item->type=='dr')
            {
                return $ledger_item->amount;
            }
            return 0;
        })
        ->addColumn('balance', function($ledger_item) use(&$running_balance){
            if($ledger_item->type=='skip') return '<strong >'.round($running_balance,2).'</strong>';
            if($ledger_item->type=='dr'){
                $running_balance -= round($ledger_item->amount,2);
            }
            else{
                $running_balance += round($ledger_item->amount,2);
            }

            return round($running_balance,2);
        })
        ->addColumn('actions', function($ledger_item){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns([
            'description',
            'balance',
            'actions'
        ])
        ->make(true);
    }


    /**
     * Route: admin.drivers.statement.data
     * PageRoute: admin.drivers.statement.view
     */
    public function getDriverLedger(Request $request, $filter = null)
    {
        # Payload
        $driver_id = (int) $request->get('driver_id');
        $running_balance=0;


        $filter_type = $request->get('filter_type');
        $filter_value = $request->get('filter_value');
        $start = null;
        $end = null;

        # ------------------------------------
        # Fetch dates according to selection
        # ------------------------------------

        $aggregator = null;

        if($filter_type === "month"){
            $start = Carbon::parse($filter_value)->startOfMonth()->format('Y-m-d');
            $end = Carbon::parse($filter_value)->endOfMonth()->format('Y-m-d');


            $only_month=Carbon::parse($filter_value)->format('m');
            $only_year=Carbon::parse($filter_value)->format('Y');

            # Aggregator to fetch specified range entries
            $aggregator = [
                [
                    '$addFields'=> [

                        'g_year' => ['$year'=> ['$toDate'=> '$month'] ],
                        'g_month'=> ['$month'=> ['$toDate'=> '$month'] ]
                    ]
                ],
                [
                    '$match'=> [
                        'g_month' =>(int)$only_month,
                        'g_year' => (int)$only_year,
                        'driver_id' => $driver_id,
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    '$project'=>['_id'=>1]
                ]
            ];

        }
        else if($filter_type === "custom"){
            $picker_range_value = explode(',', $filter_value);

            $start = Carbon::parse(trim($picker_range_value[0]))->format('Y-m-d');
            $end = Carbon::parse(trim($picker_range_value[1]))->format('Y-m-d');

            # Aggregator to fetch specified range entries
            $aggregator = [
                [
                    '$match'=> [
                        '$expr'=>[
                            '$and' => [
                                [ '$gte'=> [ ['$toDate'=> '$date'], ['$toDate'=> "$start"] ] ],
                                [ '$lte'=> [ ['$toDate'=> '$date'], ['$toDate'=> "$end"] ] ]
                            ]
                        ],
                        'driver_id' => $driver_id,
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    '$project'=>['_id'=>1]
                ]
            ];

        }

        $item_ids =StatementLedgerItem::raw(function($collection) use ( $aggregator ){
            return $collection->aggregate($aggregator);
        })->pluck('_id');
        $items = StatementLedgerItem::with([
            'driver' => function($query){
                $query->select('id', 'name');
            },
            'vehicle_ledger.booking.vehicle'
        ])->whereIn('_id',$item_ids)->get();

        # ------------------------------------------
        # Group Items
        # - Custom groups (statementledger_group table)
        # ------------------------------------------


        $customGroupItems = [];


        $itemTags = $items->pluck('tag')->unique()->values()->toArray();
        $customGroups = collect([]); # SKIP GROUPS FOR NOW
        // $customGroups = StatementLedgerItemGroup::whereIn('tags.title', $itemTags)->get();

        # Filter items having no groups defined
        $items = $items->filter(function ($item) use ($customGroups, &$customGroupItems) {

            $foundInCustomGroup = $customGroups->contains(function ($group, $key) use ($item) {
                $tags = collect($group->tags)->map(function($item){ return $item['title']; })->values()->toArray();
                return in_array($item->tag, $tags);
            });

            if($foundInCustomGroup){
                # Store it to another array to append later
                $customGroupItems[] = $item;
            }

            return !$foundInCustomGroup;
        });


        foreach ($customGroups as $customGroup) {
            $tags = collect($customGroup->tags)->map(function($item){ return trim(strtolower($item['title'])); })->values()->toArray();

            $groups = collect([]);

            # Find the items tag by tag, and append to items in groups
            $groupItems = collect($customGroupItems)->whereIn('tag', $tags);

            if(isset($customGroup->driver_based) && $customGroup->driver_based === true){
                // Group By driver_id too
                foreach ($groupItems->groupBy('driver_id') as $key => $item) {
                    $groups->push($item);
                }
            }
            else{
                // Append as a single group
                $groups->push( $groupItems );

            }

            foreach ($groups as $group) {

                if(count($group) === 0){
                    # No items found against this group
                    continue 2;
                }

                $firstItem = $group->first();

                # Sort group
                $group = $group->sort(function ($a, $b) {
                    return strtotime($a->date) > strtotime($b->date);
                })->values();


                $baseTitle = $customGroup->title;

                $title = $baseTitle.' ['.count($group).'] <a href="" style="text-decoration:underline!important" onclick="VEHICLE_LEDGER.handleGroupDetailClick(event, this)" >[View Details]</a>';


                $firstItem->groups = $group
                ->map(function($item) {

                    // Transform attachment to URL
                    if(isset($item->attachment) && $item->attachment !== ''){
                        $item->attachment = Storage::url($item->attachment);
                    }

                    return $item;
                })
                ->toArray();


                $description = $group->map(function($i) use ($baseTitle){

                    $amount = ' ['.$i->amount.']';
                    $title = $i->title;

                    $type = '';
                    if(str_contains(strtolower($i->tag), "careem")){
                        $type = 'careem';
                    }
                    if(str_contains(strtolower($i->tag), "machines")){
                        $type = 'pos_machine';
                    }
                    if(str_contains(strtolower($i->tag), "uber")){
                        $type = 'uber';
                    }
                    if(str_contains(strtolower($i->tag), "yango")){
                        $type = 'yango';
                    }

                    if($type !== ''){
                        # Remove Client name from income entries
                        $title = str_replace($baseTitle, '', $i->title);
                    }


                    if($type === 'careem' || $type === 'uber' || $type === 'yango'){
                        # Append cash,bank too
                        $amount = ' [Total: '.$i->additional_details['base']. ($i->additional_details['cash'] != 0 ? ', Cash: '.$i->additional_details['cash'] : '' ) . ', Bank: '.$i->amount.']';
                    }

                    if($type === 'pos_machine'){
                        $title = "Date " . $title;
                    }

                    return $title . $amount;
                })->join('<br />');


                if(isset($customGroup->collapse) && $customGroup->collapse === true){
                    // No description
                    $description = '';
                }

                if(isset($customGroup->date_override) && $customGroup->date_override > 0){
                    // Make the date according to override
                    $base_date = Carbon::parse($start);
                    $date = Carbon::parse($base_date->format('Y') . '-' . $base_date->format('m') . '-' . $customGroup->date_override );
                    if($date->format('m') != $base_date->format('m')){
                        // It seems date exceed current month
                        $date = $base_date->lastOfMonth();
                    }

                    $firstItem->date = $date->format("Y-m-d");
                }

                $firstItem->amount = round( $group->sum('amount'), 2 );
                $firstItem->title = $title;
                $firstItem->st_title = $baseTitle;
                $firstItem->st_description = $description; // This will exported as excel
                $firstItem->description = $description;
                $firstItem->attachment = null; # attachments will be on details page

                $items->prepend($firstItem);
            }
        }



        # sort by asc (So we can calculate balance from start)
        $items = $items->sort(function ($a, $b) {
            return strtotime($a->date) > strtotime($b->date);
        });

        $running_balance=0;

        $flag = new StatementLedgerItem;
        $flag->_id=00001;
        $flag->month='';
        $flag->title='Opening Balance';
        $flag->description='';
        $flag->type='skip';
        $flag->tag='skip';
        $flag->amount=$running_balance;
        $flag->attachment=null;
        $items->prepend($flag);

        $flag = new StatementLedgerItem;
        $flag->_id=00002;
        $flag->month='';
        $flag->title='Closing Balance';
        $flag->description='';
        $flag->type='skip';
        $flag->tag='skip';
        $flag->amount=0;
        $flag->attachment=null;
        $items->push($flag);

        return Datatables::of($items)

        ->editColumn('date', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            $booking = null;
            if(isset($ledger_item->vehicle_ledger) && isset($ledger_item->vehicle_ledger->booking)){
                $booking = $ledger_item->vehicle_ledger->booking;
            }

            $text = '';
            if(isset($booking)){
                if($booking->status === "closed"){
                    $text = 'V#'.$booking->id.' / '.$booking->vehicle->plate;
                }
                else{
                    $text = 'B#'.$booking->id;
                }
            }

            $response = '
                <div class="d-flex flex-column">
                    <span>'.Carbon::parse($ledger_item->date)->format('M d, Y').'</span>
                    <span class="small kt-font-bold">'.$text.'</span>
                </div>
            ';

            return $response;
        })
        ->editColumn('description', function($ledger_item){
            if($ledger_item->type=='skip')return '<strong>'.$ledger_item->title.'</strong>'; # It is opening and closing rows

            $prefix = '';

            # Check if attachment found
            $suffix='';
            if(isset($ledger_item->attachment) && $ledger_item->attachment !== ''){
                $attachment = Storage::url($ledger_item->attachment);

                $suffix.= ' <a href="'.$attachment.'" target="_blank">
                    <i class="la la-file-picture-o"></i>
                </a>';
            }

            # Append "Breakdown" button to addon_charge type entires
            if( !isset($ledger_item->groups) && preg_match('/_addon$/', $ledger_item->tag) ){
                $suffix.= '
                <a href="#" kr-ajax="'.route('tenant.admin.statementledger.linked.view', $ledger_item->id).'?view=addon_breakdown" class="btn btn-sm btn-outline-primary btn-square btn-evelate ml-2 py-1 px-2" title="View BreakDown" kr-ajax-block-page-when-processing="" kr-ajax-size="50%" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()">
                    View Breakdown
                </a>
                ';
            }

            $desc = trim($ledger_item->description);

            if(isset($ledger_item->driver_id)){
                $desc .= '<p class="m-0">Driver: <a href="'.route('tenant.admin.drivers.viewDetails', $ledger_item->driver_id).'">'.$ledger_item->driver->full_name.'</a></p>';
            }

            if(!isset($desc) || $desc==''){
                # return only title
                return $prefix . '<span class="transaction__desc-title" title="'.$ledger_item->tag.'">' . $ledger_item->title.$suffix . '</span>';
            }

            # return title along with detailed description
            return $prefix . '<span class="transaction__desc-title" title="'.$ledger_item->tag.'">'.$ledger_item->title.$suffix.'</span><span class="transaction__desc-subtitle">'.$desc.'</span>';
        })
        ->addColumn('cr', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            if ($ledger_item->type=='cr')
            {
                return $ledger_item->amount;
            }
            return 0;
        })
        ->addColumn('dr', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            if ($ledger_item->type=='dr')
            {
                return $ledger_item->amount;
            }
            return 0;
        })
        ->addColumn('balance', function($ledger_item) use(&$running_balance){
            if($ledger_item->type=='skip') return '<strong >'.round($running_balance,2).'</strong>';
            if($ledger_item->type=='dr'){
                $running_balance -= round($ledger_item->amount,2);
            }
            else{
                $running_balance += round($ledger_item->amount,2);
            }

            return round($running_balance,2);
        })
        ->addColumn('actions', function($ledger_item){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns([
            'date',
            'description',
            'balance',
            'actions'
        ])
        ->make(true);
    }

    /**
     * Route: admin.imports.histories.data
     * PageRoute: loaded partially
    */
    public function getImportHistories(Request $request)
    {
        $type = $request->get('type', '');
        $histories = ImportHistory::orderByDesc('date')
        ->where('type', $type)
        ->get();

        return Datatables::of($histories)
        ->addColumn('actions', function($client){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.statementledger.groups.data
     * PageRoute: admin.statementledger.groups.view
    */
    public function getStatementLedgerGroups()
    {
        $groups = StatementLedgerItemGroup::all();

        return Datatables::of($groups)
        ->addColumn('actions', function($addon){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Route: admin.statementledger.addon.data
     * PageRoute: admin.statementledger.driver.view / admin.statementledger.company.view
    */
    public function getStatementLedgerAddon(Request $request, $filter = null){
        # Payload
        $namespace = $request->get('namespace');
        $id = $request->get('id');

        $addons = Addon::with([
            'setting' => function($query){
                $query->select('id', 'title');
            },
            'link'
        ])
        ->where('source_type', 'driver')
        ->where('source_id', (int)$id)
        ->where('source_model', Driver::class)
        ->get()
        ->each->setAppends(['breakdown']);

        # Hide addons with 0 payables
        $addons = $addons->filter(function($item){
            return $item->breakdown->remaining > 0;
        })
        ->values();

        return Datatables::of($addons)
        ->addColumn('remaining', function($addon){
            return $addon->breakdown->remaining;
        })
        ->addColumn('dt_title', function($addon){
            $text = '';

            if($addon->source_model === Driver::class){
                $text = $addon->link->full_name;
            }
            return $text;
        })
        ->addColumn('actions', function($item){
            return [
                'status'=>1,
            ];
        })
        ->rawColumns(['actions'])
        ->make(true);
    }


    /**
     * Route: admin.ledger.data
     * PageRoute: admin.ledger.view
    */
    public function getLedger(Request $request, $filter)
    {
        $filter_by = $request->filter_by ?? 'month';

        //If Selected Filter is Month
        if($filter_by === 'month'){
            $month = Carbon::parse($request->value)->format('m');
            $year = Carbon::parse($request->value)->format('Y');
            // Creating Aggregator Array To Filter Data On Based Of Selected filter
            $aggregator = [
                [
                    '$addFields'=> [
                        // Add Fields To Aggregation
                        'filter_year' => ['$year'=> ['$toDate'=> '$month'] ],
                        'filter_month'=> ['$month'=> ['$toDate'=> '$month'] ]
                    ]
                ],
                [
                    // Compare and Filter
                    '$match'=> ['filter_month'=>(int)$month,'filter_year'=> (int)$year]
                ],
                [
                    // Ignore Creted Fields To Be Returned
                    '$project'=>['_id' => 1]
                ]
            ];
        }
        // If Selected Filter is Date and type in Day this will Ignore Month Filter
        if($filter_by === 'day'){
            /*
             * If <<Day>> Dropdown Selected then this will work
            */
            $d_month = Carbon::parse($request->value)->format('m');
            $d_year = Carbon::parse($request->value)->format('Y');
            $d_day = Carbon::parse($request->value)->format('d');
            // Creating Aggregator Array To Filter Data On Based Of Selected filter
            $aggregator = [
                [
                    '$addFields'=> [
                        // Add Fields To Aggregation
                        'filter_year' => ['$year'=> ['$toDate'=> '$date'] ],
                        'filter_month'=> ['$month'=> ['$toDate'=> '$date'] ],
                        'filter_day'=> ['$dayOfMonth'=> ['$toDate'=> '$date'] ],
                    ]
                ],
                [
                    // Compare and Filter
                    '$match'=>
                    [
                        'filter_month'=>(int)$d_month,
                        'filter_year'=> (int)$d_year,
                        'filter_day' => (int)$d_day,
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    // Ignore Created Fields To Be Returned
                    '$project'=>['_id' => 1]
                ]
            ];
        }

        # ---------------------
        # Fetching Ids from agg
        # (Raw query cannot eager load)
        # ---------------------
        $ledger_raw = Ledger::raw(function($collection) use ($aggregator){
            return $collection->aggregate($aggregator);
        });
        $ledger_ids = $ledger_raw->pluck('_id');

        # ------------------------------
        # Fetching actual data from ids
        # (with eager load)
        # ------------------------------
        $ledger_data = Ledger::whereIn('_id', $ledger_ids)
        ->with('user');

        # ------------------------------
        # Checking access of entries
        # (If admin: allow all)
        # (else: match ids from roles)
        # ------------------------------

        if(!Auth::user()->is_admin){

            $employee_role = Auth::user()->getCustomRole('entry_access');

            $access_all = isset($employee_role) && $employee_role->access_scope === "all";


            # If "all" contains, means user have access to all user's entries
            if(!$access_all){

                $access_ids = [Auth::user()->id];
                if(isset($employee_role)){
                    $access_ids = array_merge($employee_role->access_data??[], $access_ids);
                }

                $ledger_data->whereHas('user', function($query) use ($access_ids){
                    $query->whereIn('_id', $access_ids);
                });
            }
        }


        # -------------------------------------------
        # Filter out "cash" entries
        # (When "All entries" checked from frontend)
        # -------------------------------------------

        if($filter!='all'){
            $ledger_data->where('is_cash', true);
        }

        # EXECUTE THE QUERY
        $ledger_data = $ledger_data->get();


        # This will modify the collection and append 'source' accordingly
        \App\Http\Controllers\Tenant\LedgerController::generate_source($ledger_data);

        return Datatables::of($ledger_data)
        ->addColumn('actions', function($ledger){
            return [
                'status'=>1,
            ];
        })
        ->editColumn('date', function($ledger){
            return Carbon::parse($ledger->date)->format('d F, Y');
        })
        ->addColumn('description', function($ledger){
            return \App\Http\Controllers\Tenant\LedgerController::generate_description($ledger);
        })
        ->addColumn('cr', function($ledger){
            if($ledger->type=='cr')return $ledger->amount;
            return 0;
        })
        ->addColumn('dr', function($ledger){
            if($ledger->type=='dr')return $ledger->amount;
            return 0;
        })
        ->addColumn('account', function($ledger){
            if(isset($ledger->props['account'])){
                return $ledger->props['account']['title'];
            }
            else{
                return "Non Cash Entry";
            }
        })
        ->addColumn('paid_by', function($ledger){
            if(!isset( $ledger->user )) return "No User";
            return $ledger->user->name;
        })
        ->rawColumns(['actions', 'description'])
        ->make(true);
    }

    /**
     * Route: admin.employee.data
     * PageRoute: admin.employee.view
    */
    public function getEmployees()
    {
        $users = User::with('granted_routes')
        ->where(function($query){
            $query->where('type', '!=', 'su');
        })
        ->get();

        return Datatables::of($users)
        ->addColumn('actions', function($user){
            return [
                'status'=>1,
            ];
        })
        ->addColumn('accounts', function($user){
            return $user->accounts->map->only(['_id', 'title'])->all();
        })
        ->addColumn('basic_salary', function($user){
            $salary = 'Salary is undefined';
            if(isset($user->props) && isset($user->props['basic_salary']))$salary='Basic Salary: <strong>'.$user->props['basic_salary'].'</strong>';
            return $salary;
        })
        ->rawColumns(['actions', 'basic_salary', 'accounts'])
        ->make(true);
    }


    /**
     * Route: admin.employee.ledger.data
     * PageRoute: admin.employee.ledger.view
    */
    public function getEmployeeLedger(Request $request)
    {
        $running_balance=0;

        $month=$request->month;
        $employee_id=$request->employee_id;
        $only_month=carbon::parse($month)->format('m');
        $only_year=carbon::parse($month)->format('Y');
        $employee_ledger =Employee_ledger::raw(function($collection) use ($only_month,$only_year, $employee_id){
            return $collection->aggregate([
                [
                    '$addFields'=> [
                        'g_year' => ['$year'=> '$month'],
                        'g_month'=> ['$month'=> '$month']
                    ]
                ],
                [
                    '$match'=> [
                        'g_month'=>(int)$only_month,
                        'g_year'=> (int)$only_year,
                        'user_id'=>$employee_id,
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    '$project'=>['g_month'=>0, 'g_year'=>0]
                ],
                [
                    '$addFields'=> [
                        'user_id' => ['$toObjectId'=> '$user_id'],
                    ]
                ],
                [
                    '$lookup'=>[
                        'from'=>'users',
                        'localField'=>'user_id',
                        'foreignField'=>'_id',
                        'as'=>'user'
                    ]
                ]
            ]);
        });

        // return $employee_ledger;

        # Fetch previous balance
        $helper_service = new InjectService();
        $selection=(object)[
            'show_previous'=>1,
            'show_current'=>1,
            'show_generate'=>1
        ];
        $calculated_salary=$helper_service->helper->calculate_salary($month,$employee_id, $selection);
        // return response()->json($calculated_salary);
        $running_balance=$calculated_salary->previous_balance;

        $flag = new Employee_ledger;
        $flag->_id=00001;
        $flag->month='';
        $flag->description='Opening Balance';
        $flag->type='skip';
        $flag->is_cash=false;
        $flag->amount=$running_balance;
        $employee_ledger->prepend($flag);

        $flag = new Employee_ledger;
        $flag->_id=00002;
        $flag->month='';
        $flag->description='Closing Balance';
        $flag->type='skip';
        $flag->is_cash=false;
        $flag->amount=0;
        $employee_ledger->push($flag);

        # This will modify the collection and append 'source' accordingly

        return Datatables::of($employee_ledger)

        ->editColumn('date', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            return Carbon::parse($ledger_item->date)->format('M d, Y');
        })
        ->addColumn('description', function($ledger_item){
            if($ledger_item->type=='skip')return '<strong>'.$ledger_item->description.'</strong>'; # It is opening and closing rows

            # Check if attachment found
            $suffix='';
            if(isset($ledger_item->attachment) && $ledger_item->attachment !== ''){
                $attachment = Storage::url($ledger_item->attachment);

                $suffix.= ' <a href="'.$attachment.'" target="_blank">
                    <i class="la la-file-picture-o"></i>
                </a>';
            }

            return '
                <div>
                    <span class="description-title">'.$ledger_item->title.$suffix.'</span>
                    '.(isset($ledger_item->description)&&$ledger_item->description!==''?'<span class="description-subtitle">'.$ledger_item->description.'</span>':'').'
                </div>
            ';
        })
        ->addColumn('credit', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            if($ledger_item->is_cash==true) return 0;
            if ($ledger_item->type=='cr')
            {
                return $ledger_item->amount;
            }
            return 0;
        })
        ->addColumn('debit', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            if($ledger_item->is_cash==true) return 0;
            if ($ledger_item->type=='dr')
            {
                return $ledger_item->amount;
            }
            return 0;
        })
        ->addColumn('cash_paid', function($ledger_item){
            if($ledger_item->type=='skip')return ''; # It is opening and closing rows

            if($ledger_item->is_cash==true){
                return $ledger_item->amount;
            }
            return  0;
        })
        ->addColumn('balance', function($ledger_item) use(&$running_balance){
            if($ledger_item->type=='skip') return '<strong >'.round($running_balance,2).'</strong>';
            if($ledger_item->type=='dr'){
                $running_balance -= round($ledger_item->amount,2);
            }
            else{
                $running_balance += round($ledger_item->amount,2);
            }

            return round($running_balance,2);
        })
        ->addColumn('actions', function($ledger_item){
            return '';
        })
        ->rawColumns([
            'description',
            'balance',
            'actions'
        ])
        ->with([
            'is_salary_generated'=>$calculated_salary->is_generated
        ])
        ->make(true);
    }

     /**
     * Route: admin.employee.ledger.addon
     * PageRoute: admin.employee.ledger.view
    */
    public function getEmployeeLedgerAddon(Request $request){


        $employee_id=$request->id;

        $addons = Addon::with([
            'setting' => function($query){
                $query->select('id', 'title');
            }
        ])
        ->where('source_type', 'staff')
        ->where('source_id', $employee_id)
        ->get()
        ->each->setAppends(['breakdown']);


        return Datatables::of($addons)
        ->addColumn('actions', function($item){
            return [
                'status'=>1,
            ];
        })
        ->addColumn('remaining', function($addon){
            return $addon->breakdown->remaining;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }


    /**
     * App Settings like low inventory count and others
     * Since this will have some load thats why we are doing this as an AJAX request to reduce TTFB
    */
    public function getAppConfig()
    {
        $timenow = Carbon::now();
        # Fetch from helper service
        $helper_service = new InjectService();
        // Fetch Pending Cheques
        $visa_expiry_drivers = $pending_cheques = $liscence_expiry_drivers = $rta_permit_expiry_drivers = $all_bank_accounts = [];
        if($helper_service->routes->has_access('accounts.transaction.pending')){
            $pending_cheques = Account_transaction::with(['account'=> function($query){
                $query->select('title');
            }])
            ->where('time', '<=', $timenow->copy()->addDays(10)->format('Y-m-d'))
            ->where('status', 'pending')
            ->where('type', 'dr')
            ->get()->map(function($item) use (&$all_bank_accounts){
                $all_bank_accounts[] = $item->account->title;
                return [
                    'account' => $item->account->title,
                    'amount' => $item->amount,
                    'description' => $item->description,
                    'additional_details' => $item->additional_details
                ];
            });
        }
        // Fetch Expiring - Visa / Lisence / RTA
        if($helper_service->routes->has_custom_access('addon_department',['visa_department']))
        {
            $visa_expiry_drivers = Driver::where('visa_expiry', '<=', $timenow->copy()->addDays(15)->format('Y-m-d'))
            ->where('visa_expiry', '>=', $timenow->copy()->format('Y-m-d'))
            ->get()
            ->map(function($item){
                return [
                    'id' => $item->id,
                    'name' => $item->full_name,
                    'expiry' => $item->visa_expiry,
                    'email' => $item->email,
                    'phone_number' => $item->phone_number,
                    'nationality' => $item->nationality,
                    'booking_id' => $item->booking_id,
                ];
            });
        }
        if($helper_service->routes->has_custom_access('addon_department',['driving_license'])){
            $liscence_expiry_drivers = Driver::where('liscence_expiry', '<=', $timenow->copy()->addDays(15)->format('Y-m-d'))
            ->where('liscence_expiry', '>=', $timenow->copy()->format('Y-m-d'))
            ->get()
            ->map(function($item){
                return [
                    'id' => $item->id,
                    'name' => $item->full_name,
                    'expiry' => $item->liscence_expiry,
                    'email' => $item->email,
                    'phone_number' => $item->phone_number,
                    'nationality' => $item->nationality,
                    'booking_id' => $item->booking_id,
                ];
            });
        }
        if($helper_service->routes->has_custom_access('addon_department',['rta_card'])){
            $rta_permit_expiry_drivers = Driver::where('rta_permit_expiry','<=', $timenow->copy()->addDays(15)->format('Y-m-d'))
            ->where('rta_permit_expiry', '>=', $timenow->copy()->format('Y-m-d'))
            ->get()
            ->map(function($item){
                return [
                    'id' => $item->id,
                    'name' => $item->full_name,
                    'expiry' => $item->rta_permit_expiry,
                    'email' => $item->email,
                    'phone_number' => $item->phone_number,
                    'nationality' => $item->nationality,
                    'booking_id' => $item->booking_id,
                ];
            });
        }
        return [
            'pending_cheques' => ['all_accounts' => array_values(array_unique($all_bank_accounts)), "data" => $pending_cheques],
            'expiring_visa' => $visa_expiry_drivers,
            'expiring_liscense' => $liscence_expiry_drivers,
            'expiring_rta' => $rta_permit_expiry_drivers
        ];
    }

    public function getActivityLog(Request $request){
        // get Subject Type and ID From Request
        $subject_id = $request->subject_id;
        $subject_model = $request->subject_type;
        $category = $request->category ?? 'default';
        // Check If Subject ID is numeric if numeric then typecast to int elsewise proceed to find with BSON
        if(is_numeric($subject_id)){
            $subject_id = intval($subject_id);
        }
        if($category === 'accounts'){
            $logs = Account_log::with('user')->orderByDesc('created_at')->where('subject_id',$subject_id)->where('subject_model',$subject_model)->get();
        }else{
            $logs = Log::with('user')->orderByDesc('created_at')->where('subject_id',$subject_id)->where('subject_model',$subject_model)->get();
        }
        return response()->json($logs);
    }
}
