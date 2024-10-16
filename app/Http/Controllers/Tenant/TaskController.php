<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Addon;
use App\Models\Tenant\Task;
use App\Models\Tenant\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{

    /*
    |---------------------------
    | Routes Methods (FRONTEND)
    |---------------------------
    */

    /**
     * UI of sidebar shown to employees
     *
    */
    public function view_frontend()
    {

        $counts = [];

        # ------------------
        #  Internal Tasks
        # ------------------
        $tasks = Auth::user()->tasks()->get();

        $task_count = count($tasks);
        $task_pending_count = 0;

        $task_accordion = [];
        if($task_count > 0){
            $pending_tasks = $tasks->where('status', 'pending')->values();
            $task_pending_count = count($pending_tasks);

            $task_accordion = [
                (object)[
                    'title' => 'ToDo',
                    'icon' => 'flaticon2-list-3',
                    'records' => $pending_tasks
                ],
                (object)[
                    'title' => 'Working',
                    'icon' => 'flaticon2-rocket',
                    'records' => $tasks->where('status', 'inprogress')->values()
                ],
                (object)[
                    'title' => 'Completed',
                    'icon' => 'flaticon2-notepad',
                    'records' => $tasks->where('status', 'completed')->values()
                ]
            ];
        }



        # ------------------
        #   Fetch Departments
        # ------------------
        $addons_departments = $this->getDepartments();

        if(count($addons_departments) === 0 && $task_count === 0){
            return abort(403, "Access denied");
        }


        # --------------
        # Fetch Addons
        # --------------
        $addons = Addon::with([
            'setting' => function($query){
                if(isset($query))$query->select('id', 'title', 'source_type', 'source_required');
            },
            'link',
            'expenses' => function($query){
                $query->select('addon_id','given_date','amount', 'charge_amount', 'type', 'description');
            },
            'deductions' => function($query){
                $query->select('addon_id', 'date', 'amount');
            },
        ])
        ->whereHas('setting', function($query) use ($addons_departments){
            $match = collect($addons_departments)->pluck('match')->toArray();
            $query->whereIn('title', $match);
        })
        ->get()
        ->each->setAppends(['breakdown']);


        # ---------------------------------
        # Map Addons to each department
        # ---------------------------------
        $addons_departments_groups = collect($addons_departments)
        ->map(function($addons_department, $index) use ($addons){
            $addons_department = (object) $addons_department;

            $matched_addons =  $addons->where('setting.title', $addons_department->match);

            $addons_department->accordions = [
                (object)[
                    'title' => 'Pending',
                    'icon' => 'flaticon2-layers',
                    'records' => $matched_addons->where('status', 'initiated')->values()
                ],
                (object)[
                    'title' => 'ToDo',
                    'icon' => 'flaticon2-list-3',
                    'records' => $matched_addons->where('status', 'pending_to_start')->values()
                ],
                (object)[
                    'title' => 'Working',
                    'icon' => 'flaticon2-rocket',
                    'records' => $matched_addons->where('status', 'inprogress')->values()
                ],
                (object)[
                    'title' => 'Completed',
                    'icon' => 'flaticon2-notepad',
                    'records' => $matched_addons->where('status', 'completed')->values()
                ]
            ];

            # Active 1st item
            $addons_department->active = $index === 0;


            return $addons_department;

        })
        ->values()
        ->groupBy('title');

        // return $addons_departments_groups;

        $addons_department_titles = $addons_departments_groups
        ->map(function($data, $key){

            $count = $data->reduce(function (?int $carry, $item) {
                return $carry + collect($item->accordions)
                ->where('title', 'ToDo')
                ->sum(function ($accordion) {
                    return count($accordion->records);
                });
            });
            return [
                'title' => $key,
                'count' => $count
            ];
        })
        ->values();

        // return $addons_department_titles;

        return view('Tenant.tasks.frontend.view', compact('addons_departments_groups', 'addons_department_titles', 'task_count', 'task_pending_count', 'task_accordion'))->render();

    }

    /**
     * Settings of UI like count and other things
     *
    */
    public function view_frontend_settings()
    {

        # -------------------------------------------------
        # Settings:
        #   : ToDo count
        #   : Need to show task button to current user?
        # -------------------------------------------------

        $show = false;
        $count = 0;

        if(!Auth::user()->is_admin){

            # ----------------------
            #   Fetch Departments
            # ----------------------
            $addons_departments = $this->getDepartments();

            if( count($addons_departments) > 0 ){

                $show = true;

                # --------------
                #  Fetch Count
                # --------------
                $count += Addon::whereHas('setting', function($query) use ($addons_departments){
                    $match = collect($addons_departments)->pluck('match')->toArray();
                    $query->whereIn('title', $match);
                })
                ->where('status', 'pending_to_start')
                ->count();

            }

            # ----------------------
            #   Internal tasks
            # ----------------------
            $tasks_count = Auth::user()->tasks()->where('status', 'pending')->count();

            if($tasks_count > 0) $show = true;
            $count += $tasks_count;
        }

        // $count = 6;

        return response()->json( compact('show', 'count') );

    }


    /*
    |---------------------------
    | Routes Methods (CONFIG)
    |---------------------------
    */

    /**
     * View Page of internal task
     *
    */
    public function view_internal_tasks()
    {
        return view('Tenant.tasks.internal.view');
    }

    /**
     * Create Page of internal tasks
     *
    */
    public function show_internal_task_from($config=null)
    {
        $categories = Task::select('category')
        ->get()
        ->keyBy('category')
        ->keys();

        $employees = User::employees()
        ->get()
        ->map(function($item){
            return (object)[
                'id' => $item->id,
                'text' => $item->name,
                'selected' => false
            ];
        });

        return view('Tenant.tasks.internal.create', compact('config', 'categories', 'employees'));
    }

    /**
     * POST request of creating the task
     *
    */
    public function create_internal_task(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'employee_id' => 'required|exists:users,_id|max:255',
            'category' => 'required|max:255',
            'title' => 'required|max:255'
        ]);

        $employee_id = $request->get('employee_id');

        $employee = User::findOrFail($employee_id);

        $title = ucwords($request->get('title', ''));

        $task = new Task;
        $task->employee_id = $employee_id;
        $task->date = Carbon::parse($request->get('date'))->format('Y-m-d');
        $task->category = ucwords($request->get('category', ''));
        $task->title = $title;
        $task->description = $request->get('description', null);
        $task->status = "pending";
        $task->save();

        #need to check if image added
        if ($request->hasFile('attachments')) {

            $attachments = [];
            foreach($request->file('attachments') as $key => $attachment)
            {
                $fullfilename = $attachment->getClientOriginalName();
                $filename = pathinfo($fullfilename, PATHINFO_FILENAME);
                $extension = pathinfo($fullfilename, PATHINFO_EXTENSION);

                $index = $key + 1;

                $name = "Task #$task->id _ $filename [$index].$extension";
                $filepath = Storage::putFileAs('tasks', $attachment, $name);
                $attachments[] = $filepath;
            }

            $task->attachments = $attachments;
        }

        $task->update();

        $task->employee = $employee;

        $task->actions=[
            'status'=>1,
        ];

        return response()->json($task);

        // return redirect()->route('tenant.admin.tasks.internal.add')->with('message', "Task #$task->id is created and assign to $employee->name successfully!");

    }

    /**
     * POST request of updating the task
     *
    */
    public function edit_internal_task(Request $request)
    {

        $task = Task::findOrFail((int)$request->task_id);


        $request->validate([
            'date' => 'required|date',
            'employee_id' => 'required|exists:users,_id|max:255',
            'category' => 'required|max:255',
            'title' => 'required|max:255'
        ]);

        $employee_id = $request->get('employee_id');

        $employee = User::findOrFail($employee_id);

        $title = ucwords($request->get('title', ''));

        $task->employee_id = $employee_id;
        $task->date = Carbon::parse($request->get('date'))->format('Y-m-d');
        $task->category = ucwords($request->get('category', ''));
        $task->title = $title;
        $task->description = $request->get('description', null);



        #need to check if image added
        if ($request->hasFile('attachments')) {

            if(isset($task->attachments) && count($task->attachments) > 0){
                foreach ($task->attachments as $attachment) {
                    if(Storage::exists($attachment)){
                        // Storage::delete($attachment); # it will delete img from live too
                    }
                }
            }

            $attachments = [];
            foreach($request->file('attachments') as $key => $attachment)
            {
                $fullfilename = $attachment->getClientOriginalName();
                $filename = pathinfo($fullfilename, PATHINFO_FILENAME);
                $extension = pathinfo($fullfilename, PATHINFO_EXTENSION);

                $index = $key + 1;

                $name = "Task #$task->id _ $filename [$index].$extension";
                $filepath = Storage::putFileAs('tasks', $attachment, $name);
                $attachments[] = $filepath;
            }

            $task->attachments = $attachments;
        }

        $task->update();

        $task->employee = $employee;

        $task->actions=[
            'status'=>1,
        ];

        return response()->json($task);

        // return redirect()->route('tenant.admin.tasks.internal.add')->with('message', "Task #$task->id is created and assign to $employee->name successfully!");

    }

    /**
     * POST request of updating the task status
     *
    */
    public function update_internal_task_status(int $id, Request $request)
    {

        $request->validate([
            'status' => 'required|in:pending,inprogress,completed'
        ]);

        $task = Task::findOrFail($id);
        $task->status = $request->get('status', 'pending');
        $task->update();

        return response()->json([
            'status' => 1
        ]);
    }


    /*
    |--------------------
    | Helper Methods
    |--------------------
    */
    private function getDepartments()
    {

        $addons_departments = [];

        # ------ [ Visa ] ------
        if(app('helper_service')->routes->has_custom_access('addon_department', ['visa_department'])){
            $addons_departments[] = [
                'type' => 'visa_department',
                'title' => 'Visa',
                'match' => 'Visa',
                'accordions' => null
            ];
        }

        # ------ [ Driving License (Dubai) ] ------
        if(app('helper_service')->routes->has_custom_access('addon_department', ['driving_license_dubai'])){
            $addons_departments[] = [
                'type' => 'driving_license_dubai',
                'title' => 'Driving License',
                'match' => 'Driving License Dubai',
                'accordions' => null
            ];
        }

        # ------ [ Driving License (Sharjah) ] ------
        if(app('helper_service')->routes->has_custom_access('addon_department', ['driving_license_sharjah'])){
            $addons_departments[] = [
                'type' => 'driving_license_sharjah',
                'title' => 'Driving License',
                'match' => 'Driving License Sharjah',
                'accordions' => null
            ];
        }

        # ------ [ RTA ] ------
        if(app('helper_service')->routes->has_custom_access('addon_department', ['rta_card'])){
            $addons_departments[] = [
                'type' => 'rta_card',
                'title' => 'RTA',
                'match' => 'RTA',
                'accordions' => null
            ];
        }

        return $addons_departments;
    }

}
