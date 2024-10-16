<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Auth;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Tenant\Employee_ledger;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Accounts\Handlers\AccountGateway;
use App\Services\InjectService;
use App\Models\Tenant\Employee_route;
use App\Accounts\Models\Account_access;
use App\Models\Tenant\EmployeeRole;
use App\Models\Tenant\Role;

class EmployeeController extends Controller
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
     * View Page of employee
     *
    */
    public function ViewEmployees()
    {
        // $user = User::find("6040df18b731315a957a0336");
        // $filtered = $user->accounts->map->only(['_id', 'title']);

        return view('Tenant.employees.view');
    }

    /**
     * Create Expense form
     *
    */
    public function showEmployeeForm($config=null)
    {
        #All employees emails to check uniqueness
        $users = User::employees()->get();
        $all_emails = $users->keyBy('email')->keys();
        $desg= $users->groupBy('user_type', true)->map(function ($pb) { return $pb->keyBy('designation')->keys(); });

        # Need to get all the accounts, so admin can give access to user
        $accounts = AccountGateway::getAllAccounts();
        return view('Tenant.employees.create', compact('all_emails', 'desg', 'accounts', 'config'));
    }

    /**
     * POST request of creating the employee
     *
    */
    public function create(request $request)
    {
        # Serverside validation
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|confirmed|min:8'
        ]);

        $grant_all_accounts = false;
        if($request->has('grant_all_accounts'))$grant_all_accounts = true;

        $salary=0;
        if(isset($request->salary) && $request->salary!='')$salary=(float)$request->salary;

        $props=[
            'basic_salary'=>$salary,
            'all_accounts'=>$grant_all_accounts
        ];


        # Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_type'=>$request->user_type,
            'designation'=>$request->designation,
            'password' => Hash::make($request->password),
            'type'=>'normal', # normal,su
            'props'=>$props,
            'status'=>1
        ]);

        # Account access
        $account_accesses = $user->account_access;

        if(!$grant_all_accounts){
            $granted_accounts = $request->employee_accounts;

            if(isset($granted_accounts)&&count($granted_accounts)>0){
                # Loop through each account and add them to db
                foreach ($granted_accounts as $account_id) {
                    $account_access = new Account_access;
                    $account_access->user_id=$user->id;
                    $account_access->account_id=$account_id;
                    $account_access->save();

                }
            }
        }

        $user->actions = ['status'=>1];
        $user->basic_salary = 'Basic Salary: <strong>'.$salary.'</strong>';
        $user->accounts = $user->accounts->map->only(['_id', 'title']);

        return $user;
    }

    /**
     * GET request of editing the page
     *
    */
    public function showEditForm($id)
    {
        # Find the job
        $user = User::find($id);
        $user->actions = ['status'=>1];
        $user->accounts = $user->accounts->map->only(['_id', 'title']);

        # Call the load job function
        return $this->showEmployeeForm((object)[
            'user'=>$user,
            'action'=>'edit'
        ]);

    }

    /**
     * POST request of editing the employee
     *
    */
    public function edit(Request $request)
    {
        # Payload
        $user_id = $request->user_id;
        $user = User::find($user_id);
        if(!isset($user)){
            abort(505, 'User not found');
        }

        $change_pass = false;
        if($request->has('change_password'))$change_pass = true;

        # Check some validations
        if($change_pass){
            Validator::make($request->all(), [
                'email' => [
                    'required',
                    'max:255',
                    Rule::unique('users')->ignore($user->_id, '_id'),
                ],
                'name' => ['required', 'max:255'],
                'password' => ['required', 'confirmed', 'min:8'],
            ])->validate();
        }
        else{
            Validator::make($request->all(), [
                'email' => [
                    'required',
                    'max:255',
                    Rule::unique('users')->ignore($user->_id, '_id'),
                ],
                'name' => ['required', 'max:255'],
            ])->validate();
        }

        # Payload Continue...
        $is_active = 0;
        if($request->has('is_active'))$is_active = 1;

        $grant_all_accounts = false;
        if($request->has('grant_all_accounts'))$grant_all_accounts = true;


        $salary=0;
        if(isset($request->salary) && $request->salary!='')$salary=(float)$request->salary;

        $props=[
            'basic_salary'=>$salary,
            'all_accounts'=>$grant_all_accounts
        ];

        # Find & Edit user

        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type=$request->user_type;
        $user->designation=$request->designation;
        $user->props = $props;
        $user->status = $is_active;
        if($change_pass)$user->password = Hash::make($request->password);
        $user->save();


        # Account access
        $account_accesses = $user->account_access;

        if(!$grant_all_accounts){
            $granted_accounts = $request->employee_accounts;

            if(isset($granted_accounts)&&count($granted_accounts)>0){
                # Loop through each account and add them to db
                foreach ($granted_accounts as $account_id) {

                    # Find if already

                    # Get key of found record (we need key to forget this item later)
                    $already_granted_key=$account_accesses->search(function($i) use ($account_id){
                        return $i->account_id == $account_id;
                    });

                    # Fetch item from key (so we can update the item)
                    $already_granted=null;
                    if($already_granted_key!==false)$already_granted=$account_accesses[$already_granted_key];

                    if(!$already_granted){
                        $account_access = new Account_access;
                        $account_access->user_id=$user->id;
                        $account_access->account_id=$account_id;
                        $account_access->save();
                    }
                    else{
                        #Remove this item from array so it won't deleted
                        $account_accesses->forget($already_granted_key);
                    }

                }
            }
        }

        # Delete the remaining access who cannot be founded
        foreach ($account_accesses as $access) {
            $access->delete();
        }

        # For datatables purpose
        $user->actions = ['status'=>1];
        $user->basic_salary = 'Basic Salary: <strong>'.$salary.'</strong>';
        $user->accounts = $user->accounts->map->only(['_id', 'title']);


        return response()->json($user);
    }

    /**
     * Create Route Access
     *
    */
    public function showRoutesForm()
    {
        # Get all the registered routes
        $helper_service = new InjectService();
        $routes = $helper_service->routes->getRegisteredRoutes();

        return view('Tenant.employees.routes.create', compact('routes'));
    }

    /**
     * Create Custom Route Access
     *
    */
    public function showCustomRoutesForm($id)
    {

        $employee = User::with('employee_roles')
        ->findOrFail($id);

        $roles = Role::all()
        ->map(function($role) use ($employee){

            $customData = null;

            # Generate data source based on tag:
            #   : This list will define partial access to module
            #   : i.e. for entry access, Employee1 can have access to only Employee2
            #   : i.e. for addon department, a user can have access to all departments or just the Visa department
            switch ($role->tag) {
                case 'entry_access':
                    $customData = User::with('employee_roles.role')
                    ->employees()
                    ->where('_id','!=', $employee->_id)
                    ->get()
                    ->map(function($empl) use ($role){
                        return (object)[
                            'id' => $empl->id,
                            'text' => $empl->name,
                            'selected' => false
                        ];
                    })
                    ->toArray();
                    break;

                case 'addon_department':
                    $customData = [
                        (object)[
                            'id' => "visa_department",
                            'text' => "Visa Department",
                            'selected' => false
                        ],
                        (object)[
                            'id' => "driving_license_dubai",
                            'text' => "Driving License (Dubai)",
                            'selected' => false
                        ],
                        (object)[
                            'id' => "driving_license_sharjah",
                            'text' => "Driving License (Sharjah)",
                            'selected' => false
                        ],
                        (object)[
                            'id' => "rta_card",
                            'text' => "RTA",
                            'selected' => false
                        ]
                    ];

                    break;


                case 'negative_account_balance':
                    # Need to get all the accounts, so admin can give access to user
                    $customData = AccountGateway::getAllAccounts()
                    ->groupBy('department')
                    ->map(function($groups, $department){
                        $title = ucwords(str_replace("_", " ", $department));
                        if($department === 'bank') $title = "Bank Accounts";
                        if($department === 'cih') $title = "Cash in Hand Accounts";

                        $data = $groups
                        ->map(function($item){
                            return (object)[
                                'id' => $item->id,
                                'text' => $item->title,
                                'selected' => false
                            ];
                        })
                        ->toArray();

                        return (object)[
                            'title' => $title,
                            'data' => $data
                        ];
                    })
                    ->toArray();
                    break;

                default:
                    # code...
                    break;
            }

            # -----------------------------
            # Fill data - For edit purpose
            # -----------------------------
            $role->all = false;

            $employee_role = $employee->employee_roles->where('role_id', $role->id)->first();

            if(isset($employee_role)){

                # --------------
                # Fill CustomData
                # --------------
                if( isset($customData) ){
                    $customData = collect($customData)
                    ->map(function($item) use ($employee_role, $role){

                        if(isset($item->data)){
                            // It is group, check all group entries

                            $item->data =collect($item->data)
                            ->map(function($sub_item) use ($employee_role, $role){
                                if( isset($employee_role->access_data) && in_array($sub_item->id, $employee_role->access_data) ){
                                    // select the custom data
                                    $sub_item->selected = true;
                                }

                                return $sub_item;
                            })
                            ->toArray();

                        }
                        else{

                            if( isset($employee_role->access_data) && in_array($item->id, $employee_role->access_data) ){
                                // select the custom data
                                $item->selected = true;
                            }
                        }


                        return $item;
                    })
                    ->toArray();
                }


                # --------------
                # Fill ALL Access
                # --------------
                if(isset($employee_role->access_scope) && $employee_role->access_scope === "all"){
                    $role->all = true;
                }
            }


            $role->custom_data = $customData;

            $role->title = isset($role->title) ? $role->title : ucwords(str_replace("_", " ", $role->tag));
            $role->description = isset($role->description) ? $role->description : null;

            return $role;

        });

        return view('Tenant.employees.routes.create_custom', compact('id', 'roles'));
    }

    /**
     * POST request of creating the custom routes access
     *
    */
    public function create_custom_routes(request $request)
    {

        # --------------------
        #   Basic Validation
        # --------------------
        $request->validate([
            'employee_id' => 'required|max:255',
            'access' => 'required|array'
        ]);

        $employee_id = $request->employee_id;

        EmployeeRole::where('employee_id', $employee_id)->delete();

        # Loop through access and save it
        $access = $request->get('access', []);
        foreach ($access as $rolePayload) {

            # Skip If no "all" || "data" found
            if(!isset($rolePayload['all']) && !isset($rolePayload['data'])) continue;

            $role_id = $rolePayload['id'];

            # --------------------
            # Save Employee Access
            # --------------------
            $employee_role = new EmployeeRole;

            $employee_role->employee_id = $employee_id;
            $employee_role->role_id = $role_id;

            $employee_role->access_scope = isset($rolePayload['all']) ? 'all' : 'partial';
            $employee_role->access_data = isset($rolePayload['data']) ? $rolePayload['data'] : null;

            $employee_role->save();
        }

        # For datatables purpose
        $employee = User::with('granted_routes')->findOrFail($employee_id);
        $employee->actions = ['status'=>1];
        $employee->basic_salary = 'Basic Salary: <strong>'.$employee->props['basic_salary'].'</strong>';

        return response()->json($employee);

    }

    /**
     * POST request of creating the routes access
     *
    */
    public function create_routes(request $request){
        # Normal validation
        $validated = $request->validate([
            'employee_id' => 'required|max:255'
        ]);

        # Payload
        $routes = [];
        if(isset($request->routes))$routes = array_values($request->routes);

        $employee_id=$request->employee_id;

        # Find employee
        $employee = User::with('granted_routes')->find($employee_id);

        # Routes that are already granted to this user
        $granted_routes = $employee->granted_routes;

        # Add/Update each route
        foreach ($routes as $route) {
            #Convet to object (for convinience purpose)
            $route = (object)$route;

            # Route name is compursolry
            if(isset($route->route_name)){
                # Check if this route is already added

                # Get key of found record (we need key to forget this item later)
                $already_granted_key=$granted_routes->search(function($i) use ($route){
                    return $i->route_name == $route->route_name;
                });

                # Fetch item from key (so we can update the item)
                $already_granted=null;
                if($already_granted_key!==false)$already_granted=$granted_routes[$already_granted_key];

                $has_soft = false;
                if(isset($route->has_soft))$has_soft = true;

                if(isset($already_granted)){
                    # check if anything needs to be updated

                    if($already_granted->has_soft!=$has_soft){
                        #Update the route
                        $already_granted->has_soft=$has_soft;
                        $already_granted->update();
                    }

                    #Remove this item from array so it won't deleted
                    $granted_routes->forget($already_granted_key);
                }
                else{
                    # No route found, add this route to granted
                    $employee_route = new Employee_route;
                    $employee_route->employee_id = $employee_id;
                    $employee_route->route_name = $route->route_name;
                    $employee_route->has_soft = $has_soft;
                    $employee_route->save();
                }
            }

        }


        # Delete the remaining routes who cannot be founded
        foreach ($granted_routes as $route) {
            $route->delete();
        }

        # For datatables purpose
        $employee = User::with('granted_routes')->find($employee_id);
        $employee->actions = ['status'=>1];
        $employee->basic_salary = 'Basic Salary: <strong>'.$employee->props['basic_salary'].'</strong>';

        return response()->json($employee);

    }

    /**
     * View employee ledger (employee accounts)
     *
    */

    public function ViewEmployeesLedger()
    {
        $employees = User::employees()->get();
        return view('Tenant.employees.ledger.view', compact('employees'));
    }

    /**
     * Advance (employee accounts)
     *
    */
    public function showAdvanceForm(){
        $employees = User::employees()->get();
        return view('Tenant.employees.ledger.advance.create', compact('employees'));
    }
    public function createAdvance(Request $request){

        # This request involve cash, we better validate accounts
        AccountGateway::validateCookie();

        $validated = $request->validate([
            'amount' => 'required|gt:0'
        ]);

        #Add advance to Employee Ledger
        $e_ledger=new Employee_ledger;
        $e_ledger->type='dr';
        $e_ledger->tag='advance';
        $e_ledger->title='Advance';
        $e_ledger->description=$request->description;
        $e_ledger->month=Carbon::parse($request->month)->format('Y-m-d');
        $e_ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $e_ledger->user_id=$request->user_id;
        $e_ledger->is_cash=true;
        $e_ledger->amount=(float)$request->amount;
        $e_ledger->save();

        #get Seleted Account ID
        $selected_account = AccountGateway::getSelectedAccount();

        # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$e_ledger->id;
        $ledger->source_model=get_class($e_ledger);
        $ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $ledger->month = Carbon::parse($request->month)->format('Y-m-d'); // For Filteration Purpose
        $ledger->is_cash=true;
        $ledger->tag="advance";
        $ledger->amount=(float)$request->amount;
        $ledger->props=[
            'by'=>Auth::user()->id,
            'account'=>[
                'id'=>$selected_account->_id,
                'title'=>$selected_account->title
            ]
        ];
        $ledger->save();

        # add new transaction
        $transaction = AccountGateway::add_transaction([
            'type'=>'dr',
            'title'=>'Advance',
            'date' => Carbon::parse($ledger->date)->format('Y-m-d'),
            'description'=>$request->description,
            'tag'=>'advance',
            'amount'=>(float)$request->amount,
            'links'=>[
                [
                    'modal'=>get_class(new Employee_ledger),
                    'id'=>$e_ledger->_id,
                    'tag'=>'advance'
                ],
                [
                    'modal'=>get_class(new Ledger),
                    'id'=>$ledger->id,
                    'tag'=>'ledger'
                ],
            ]
        ]);

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transaction->id;
        $relation->source_model = get_class($transaction);
        $relation->tag = 'transaction';
        $relation->is_real = false;
        $relation->save();

        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $e_ledger->id;
        $relation->source_model = get_class($e_ledger);
        $relation->tag = 'advance';
        $relation->is_real = true;
        $relation->save();

        return response()->json($e_ledger);
    }

    /**
     * Bonus (employee accounts)
     *
    */
    public function showBonusForm(){
        $employees = User::employees()->get();
        return view('Tenant.employees.ledger.bonus.create', compact('employees'));
    }
    public function createBonus(Request $request){

        $validated = $request->validate([
            'amount' => 'required|gt:0'
        ]);

        #Add bonus to Employee Ledger
        $e_ledger=new Employee_ledger;
        $e_ledger->type='cr';
        $e_ledger->tag='bonus';
        $e_ledger->title='Bonus';
        $e_ledger->description=$request->description;
        $e_ledger->month=Carbon::parse($request->month)->format('Y-m-d');
        $e_ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $e_ledger->user_id=$request->user_id;
        $e_ledger->is_cash=false;
        $e_ledger->amount=(float)$request->amount;
        $e_ledger->save();

        # Save ledger(Third Table)
        $ledger = new Ledger;
        $ledger->type="cr";
        $ledger->source_id=$e_ledger->id;
        $ledger->source_model=get_class($e_ledger);
        $ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $ledger->month = Carbon::parse($request->month)->format('Y-m-d'); // For Filteration Purpose
        $ledger->is_cash=false;
        $ledger->tag="bonus";
        $ledger->amount=(float)$request->amount;
        $ledger->props=[
            'by'=>Auth::user()->id,
        ];
        $ledger->save();


        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $e_ledger->id;
        $relation->source_model = get_class($e_ledger);
        $relation->tag = 'bonus';
        $relation->is_real = true;
        $relation->save();

        return response()->json($e_ledger);
    }

    /**
     * Fine (employee accounts)
     *
    */
    public function showFineForm(){
        $employees = User::employees()->get();
        return view('Tenant.employees.ledger.fine.create', compact('employees'));
    }
    public function createFine(Request $request){

        $validated = $request->validate([
            'amount' => 'required|gt:0'
        ]);

        #Add advance to Employee Ledger
        $e_ledger=new Employee_ledger;
        $e_ledger->type='dr';
        $e_ledger->tag='discipline_fine';
        $e_ledger->title='Descipline Fine';
        $e_ledger->description=$request->description;
        $e_ledger->month=Carbon::parse($request->month)->format('Y-m-d');
        $e_ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $e_ledger->user_id=$request->user_id;
        $e_ledger->is_cash=false;
        $e_ledger->amount=(float)$request->amount;
        $e_ledger->save();

        # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$e_ledger->id;
        $ledger->source_model=get_class($e_ledger);
        $ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $ledger->month = Carbon::parse($request->month)->format('Y-m-d'); // For Filteration Purpose
        $ledger->is_cash=false;
        $ledger->tag="discipline_fine";
        $ledger->amount=(float)$request->amount;
        $ledger->props=[
            'by'=>Auth::user()->id,
        ];
        $ledger->save();


        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $e_ledger->id;
        $relation->source_model = get_class($e_ledger);
        $relation->tag = 'discipline_fine';
        $relation->is_real = true;
        $relation->save();

        return response()->json($e_ledger);
    }

    /**
     * Salary (employee accounts)
     *
    */
    /**
     * Generate Salary (employee accounts)
     *
    */
    public function generateSalaryForm(){
        // $helper_service = new InjectService();
        // $calculate_salary=$helper_service->helper->calculate_salary('2021-03-01','6040af16b731315a957a0327');
        // return $sad;
        $employees = User::employees()->get();
        return view('Tenant.employees.ledger.salary.generate.create', compact('employees'));
    }
    public function createSalary(Request $request){

        $validated = $request->validate([
            'amount' => 'required|gt:0'
        ]);

        #payload
        $month = Carbon::parse($request->month)->format('Y-m-d');
        $onlyMonth=Carbon::parse($month)->format('m');
        $onlyYear=Carbon::parse($month)->format('Y');
        $employee_id=$request->user_id;

        #Add salary to Employee Ledger
        $e_ledger=new Employee_ledger;

        #Need to check if salary is already created, we need to update it
        $already_salary = Employee_ledger::raw(function($collection) use ($onlyMonth,$onlyYear, $employee_id){
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
                        'tag'=>'salary',
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    '$project'=>['g_month'=>0, 'g_year'=>0]
                ]
            ]);
        })->first();
        if(isset($already_salary))$e_ledger=$already_salary;

        $e_ledger->type='cr';
        $e_ledger->tag='salary';
        $e_ledger->title='Salary received from Kingriders';
        $e_ledger->description='';
        $e_ledger->month=$month;
        $e_ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $e_ledger->user_id=$request->user_id;
        $e_ledger->is_cash=false;
        $e_ledger->amount=(float)$request->amount;
        $e_ledger->save();

        #find relation
        $relation_found = Table_relation::where('tag', 'salary')
        ->where('source_id', $e_ledger->id)
        ->get()
        ->first();


        # Save ledger
        $ledger = new Ledger;
        if(isset($relation_found)){
            $ledger = $relation_found->ledger;
        }
        $ledger->type="cr";
        $ledger->source_id=$e_ledger->id;
        $ledger->source_model=get_class($e_ledger);
        $ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $ledger->month = $month; // For Filteration Purpose
        $ledger->is_cash=false;
        $ledger->tag="salary";
        $ledger->amount=(float)$request->amount;
        $ledger->props=[
            'by'=>Auth::user()->id,
        ];
        $ledger->save();

        #add relations
        $relation = new Table_relation;
        if(isset($relation_found)){
            $relation = $relation_found;
        }
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $e_ledger->id;
        $relation->source_model = get_class($e_ledger);
        $relation->tag = 'salary';
        $relation->is_real = true;
        $relation->save();

        return response()->json($e_ledger);
    }

    /**
     * Calculate Salary (employee accounts)
     *
    */
    public function calculateSalary(Request $request){
        # Call to service where all salary calculation logic is implemented
        $helper_service = new InjectService();
        $selection = null;
        if(isset($request->selection))$selection=$request->selection;
        $calculate_salary=$helper_service->helper->calculate_salary($request->month,$request->employee_id, $selection);
        return response()->json($calculate_salary);
    }


    /**
     * Pay Salary (employee accounts)
     *
    */
    public function paySalaryForm(){
        $employees = User::employees()->get();
        return view('Tenant.employees.ledger.salary.pay.create', compact('employees'));
    }
    public function paySalary(Request $request){

        # This request involve cash, we better validate accounts
        AccountGateway::validateCookie();

        $validated = $request->validate([
            'amount' => 'required|gt:0'
        ]);

        #Find employee
        $employee = User::find($request->user_id);
        #Pay salary to Employee Ledger
        $e_ledger=new Employee_ledger;
        $e_ledger->type='dr';
        $e_ledger->tag='salary_paid';
        $e_ledger->title='Salary paid';
        $e_ledger->description=null;
        $e_ledger->month=Carbon::parse($request->month)->format('Y-m-d');
        $e_ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $e_ledger->user_id=$employee->id;
        $e_ledger->is_cash=true;
        $e_ledger->amount=(float)$request->amount;
        $e_ledger->save();

        #get Seleted Account ID
        $selected_account = AccountGateway::getSelectedAccount();

        # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$e_ledger->id;
        $ledger->source_model=get_class($e_ledger);
        $ledger->date=Carbon::parse($request->given_date)->format('Y-m-d');
        $ledger->month = Carbon::parse($request->month)->format('Y-m-d'); // For Filteration Purpose
        $ledger->is_cash=true;
        $ledger->tag="salary_paid";
        $ledger->amount=(float)$request->amount;
        $ledger->props=[
            'by'=>Auth::user()->id,
            'account'=>[
                'id'=>$selected_account->_id,
                'title'=>$selected_account->title
            ]
        ];
        $ledger->save();

        # add new transaction
        $transaction = AccountGateway::add_transaction([
            'type'=>'dr',
            'title'=>'Salary Paid',
            'date' => Carbon::parse($ledger->date)->format('Y-m-d'),
            'description'=>$employee->name.' | '.Carbon::parse($request->month)->format('F Y'),
            'tag'=>'salary_paid',
            'amount'=>(float)$request->amount,
            'links'=>[
                [
                    'modal'=>get_class(new Employee_ledger),
                    'id'=>$e_ledger->id,
                    'tag'=>'salary_paid'
                ],
                [
                    'modal'=>get_class(new Ledger),
                    'id'=>$ledger->id,
                    'tag'=>'ledger'
                ],
            ]
        ]);

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transaction->id;
        $relation->source_model = get_class($transaction);
        $relation->tag = 'transaction';
        $relation->is_real = false;
        $relation->save();

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $e_ledger->id;
        $relation->source_model = get_class($e_ledger);
        $relation->tag = 'salary_paid';
        $relation->is_real = true;
        $relation->save();

        return response()->json($e_ledger);
    }
}
