<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Auth;

use App\Models\Tenant\Client;
use App\Accounts\Models\Account;
use App\Helpers\GoogleSheets;
use App\Imports\Helper\PreviewData;
use App\Imports\IncomesImport;
use App\Imports\InstallmentsImport;
use App\Imports\SimbillsImport;
use App\Imports\SimsImport;
use App\Imports\StatementLedgerItemsImport;
use App\Imports\TransactionLedger\TransactionLedgerImport;
use App\Imports\TransactionLedger\TransactionLedgerTrigger;
use App\Imports\VehicleBillsImport;
use App\Imports\VehicleLedgerItemsImport;
use App\Models\Tenant\ClientEntities;
use App\Models\Tenant\ImportHistory;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\TransactionLedger;
use App\Models\Tenant\VehicleBillsDetail;
use App\Models\Tenant\VehicleBillsSetting;
use App\Models\Tenant\VehicleBooking;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ImportController extends Controller
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
     * Import Page of Statement Ledger - Transaction
     *
    */
    public function showImportStatementLedgerForm(Request $request, $id,$config=null)
    {

        return view('Tenant.imports.statementledger.import', compact('config', 'id'));
    }

    /**
     * POST request of Vehicle Ledger - Transaction
     *
    */
    public function import_statement_ledger(Request $request)
    {

        if($request->get('source', 'local') === 'google_sheet'){
            # Fetch source from google sheet

            $request->validate([
                'googlesheet_speadsheetid' => 'required|max:255',
                'googlesheet_range' => 'required|max:255'
            ]);

            $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
            $range = $request->get('googlesheet_range', null);

            # Fetch data
            try {
                $sheets = new GoogleSheets($spreadsheetID, $range);
            }
            catch (\Google\Service\Exception $ex) {
                $errors = collect($ex->getErrors())
                ->map(function($item){
                    return "Google Sheet Error: ".$item['message'];
                })
                ->toArray();
                throw ValidationException::withMessages($errors);
            }
            catch (\Exception $ex) {
                throw ValidationException::withMessages([$ex->getMessage()]);
            }

            if($request->has('preview_only')){
                $values = $sheets->get();
                return response()->json([$values]);
            }

            $values = $sheets->get(true);

            $importObj = new StatementLedgerItemsImport();
            if (method_exists($importObj, 'rules')){
                # Do Validation

                $errors = [];

                foreach ($values as $key => $value) {
                    $validator = Validator::make($value->toArray(), $importObj->rules());

                    if($validator->fails()){
                        $single_errors = $validator->errors();

                        foreach ($single_errors->messages() as $name => $message) {
                            $errors[$name.'_'.$key] = "Error in row #".($key+1) . ". " . $message[0] . ' | Data: '.$value[$name];
                        }

                    }
                }

                if(count($errors) > 0){
                    throw ValidationException::withMessages($errors);
                }

            }

            $importObj->collection($values);

            return back()->with('message', "Import completed successfully!");
        }

        $request->validate([
            'attachment' => [
                'required',
                File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                    ->max(20 * 1024), // 20mb max
            ]
        ]);

        # ---------------------------
        #   Validate Headers
        # ---------------------------
        $headings = collect((new HeadingRowImport)->toArray($request->file('attachment')));

        if(count($headings) > 1){
            # Means workbook has multiple sheets, throw error
            throw ValidationException::withMessages([
                "#1" => "Multiple sheets found while only single sheet is allowed, please remove extra sheets from workbook"
            ]);
        }

        if($request->has('preview_only')){
            $preview_data = (new PreviewData)->toCollection($request->file('attachment'));
            return response()->json($preview_data);
        }

        Excel::import(new StatementLedgerItemsImport, $request->file('attachment'));

        // return 'ok';

        return back()->with('message', "Import completed successfully!");
    }

    /**
     * Import Page of Vehicle Bills
     *
    */
    public function showImportVehicleBillsForm(Request $request)
    {
        $bills_setting = VehicleBillsSetting::all();

        return view('Tenant.imports.vehiclebills.import', compact('bills_setting'));
    }

    /**
     * POST request of Vehicle Bills
     *
    */
    public function import_vehicle_bills(Request $request)
    {
        /**
         * # Global Validation + Source Identification
         */

         $request->validate([
            'month' => 'required',
            'bill_setting_id' => 'required|max:255',
        ]);

        $fromGoogleSheet = $request->get('source', 'local') === 'google_sheet';
        if(!$fromGoogleSheet){
            $file = $request->file('attachment');
        }

        $bill_setting_id = $request->get('bill_setting_id');
        $bill_setting = VehicleBillsSetting::find($bill_setting_id);

        if($request->has('headings_only') || $request->has('preview_only')){

            if($fromGoogleSheet){
                $request->validate([
                    'googlesheet_speadsheetid' => 'required|max:255',
                    'googlesheet_range' => 'required|max:255'
                ]);
            }
            else{

                $request->validate([
                    'attachment' => [
                        'required',
                        File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->max(20 * 1024), // 20mb max
                    ],
                ]);

            }

        }
        else{

            if($fromGoogleSheet){
                $request->validate([
                    'googlesheet_speadsheetid' => 'required|max:255',
                    'googlesheet_range' => 'required|max:255',
                    'account_id' => 'required|max:255',
                    'heading_mapper' => 'required|array'
                ]);
            }
            else{
                $request->validate([
                    'attachment' => [
                        'required',
                        File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->max(20 * 1024), // 20mb max
                    ],
                    'account_id' => 'required|max:255',
                    'heading_mapper' => 'required|array'
                ]);
            }
        }

        /**
         * # If Returning Data For Header Mapings
         */

        if($request->has('headings_only') && !$request->has('preview_only')){
            if($fromGoogleSheet){
                $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
                $range = $request->get('googlesheet_range', null);

                # Fetch data
                try {
                    $sheets = new GoogleSheets($spreadsheetID, str_contains($range, "!") ? $range : $range.'!1:1');
                }
                catch (\Google\Service\Exception $ex) {
                    $errors = collect($ex->getErrors())
                    ->map(function($item){
                        return "Google Sheet Error: ".$item['message'];
                    })
                    ->toArray();
                    throw ValidationException::withMessages($errors);
                }
                catch (\Exception $ex) {
                    throw ValidationException::withMessages([$ex->getMessage()]);
                }

                $headings = [[$sheets->headings()]];



            }
            else{
                # ---------------------------
                #   Validate Headers
                # ---------------------------
                HeadingRowFormatter::extend('tmp', function($value, $key) {
                    if(!isset($value) || trim($value) == '') return null;
                    return $value;
                });
                HeadingRowFormatter::default('tmp');
                $headings = (new HeadingRowImport)->toArray($file);

            }


            if(count($headings) > 1){
                # Means workbook has multiple sheets, throw error
                throw ValidationException::withMessages([
                    "#1" => "Multiple sheets found while only single sheet is allowed, please remove extra sheets from workbook"
                ]);
            }
            # this was called via ajax, so we will return in html view
            $headings = $headings[0][0];
            return view('Tenant.imports.vehiclebills.partial-headings', compact('headings', 'bill_setting'))->render();
        }
        /**
         * # Apply Validations For Preview And Importing
         */

        $mapper = collect($request->heading_mapper);

        # ----------------------------------------------------------
        # Custom validation to check if we have desired mapped items
        # ----------------------------------------------------------
        $requiredInMapper = [
            'plate',
            // 'date',
            // 'uuid',
            'charged_amount'
        ];
        $foundInMapper = [];

        // if(!$bill_setting->charged_is_spend){
        //     $requiredInMapper[] = 'spend_amount';
        // }

        foreach ($mapper as $mapper_item) {
            if( in_array($mapper_item['destination'], $requiredInMapper) ){
                $foundInMapper[] = $mapper_item['destination'];
            }
        }

        $errors = [];
        foreach ($requiredInMapper as $item) {
            if( !in_array($item, $foundInMapper) ){
                $errors[] = [
                    $item => "\"$item\" field is required to map to atleast 1 item"
                ];
            }
        }

        if(count($errors) > 0){
            throw ValidationException::withMessages($errors);
            return;
        }

        HeadingRowFormatter::reset();

        /**
         * Find duplicate records
         * TODO: On preview find the duplicate records in system and show them
        */

        if($request->has('preview_only')){

            $previewData = [];
            $importData = [];

            // Fetch Import Data from source (google/local)
            if($fromGoogleSheet){

                $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
                $range = $request->get('googlesheet_range', 'Sheet1');
                if(!(isset($range) && trim($range) != '')) $range = "Sheet1";

                # Fetch data
                try {
                    $sheets = new GoogleSheets($spreadsheetID, $range);
                }
                catch (\Google\Service\Exception $ex) {
                    $errors = collect($ex->getErrors())
                    ->map(function($item){
                        return "Google Sheet Error: ".$item['message'];
                    })
                    ->toArray();
                    throw ValidationException::withMessages($errors);
                }
                catch (\Exception $ex) {
                    throw ValidationException::withMessages([$ex->getMessage()]);
                }

                $importData = $sheets->get();
            }
            else{
                $importData = (new PreviewData)->toCollection($request->file('attachment'))->first();
            }

            # Fetch REFs using mapper
            $refs = $importData->map(function($row, $rowIndex) use ($mapper){
                $ref = null;

                foreach ($row as $index => $value) {
                    $mapItem = $mapper->where('title', $index)->first();
                    if(isset($mapItem)){
                        $destination = $mapItem['destination'];

                        if($destination === "uuid" && !isset($ref)){
                            $ref = $value;
                        }

                    }
                }

                if(isset($ref))return $ref;

                return null;

            })
            ->filter(fn($item) => isset($item))
            ->map(fn($item) => (string)$item)
            ->values();

            
            // Fetch duplicate records
            $dpRecords = VehicleBillsDetail::whereIn('ref', $refs)
            ->with('table_relations.ledger')
            ->get();

            // Map with import data so on preview duplicate records can be viewable
            $importData = $importData->map(function($row, $rowIndex) use ($dpRecords, $mapper){
                $ref = null;

                foreach ($row as $index => $value) {
                    $mapItem = $mapper->where('title', $index)->first();
                    if(isset($mapItem)){
                        $destination = $mapItem['destination'];

                        if($destination === "uuid" && !isset($ref)){
                            $ref = $value;
                        }

                    }
                }

                
                $dpRef = "";
                $dpRecord = $dpRecords->where('ref', $ref)->first();
                if(isset($dpRecord)){
                    $ledger = null;
                    # Try to find ledger via relations
                    $table_relation = $dpRecord->table_relations
                    ->first();

                    if(isset($table_relation)){
                        $ledger = $table_relation->ledger;
                    }

                    $dpRef = '
                        <p class="m-0">
                            <b>REF:</b>
                            <span>'.$dpRecord->ref.'</span>
                        </p>
                        <p class="m-0">
                            <b>Imported At:</b>
                            <span>'.Carbon::parse($dpRecord->created_at)->format('d F, Y').'</span>
                        </p>
                        <div class="m-0">
                            <b>Ledger ID:</b>
                            ' . (isset($ledger) ? '
                                <div class="d-inline-flex">
                                    <span>' . $ledger->id . '</span>
                                    <a class="kt-link kt-link-primary ml-2" target="_blank" href="' . route('tenant.admin.ledger.view') . '?value=' . Carbon::parse($ledger->date)->format('Y-m-d') . '&type=day&filter=all">View</a>
                                </div>
                            ' : '' ) . '
                        </div>
                    ';
                }

                return ['Duplicate Ref' => $dpRef , ...$row];

            });
            

            return response()->json([$importData]);

        }
        /**
         * # Import All Data After Everything
         */

        if($fromGoogleSheet){

            $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
            $range = $request->get('googlesheet_range', null);

            # Fetch data
            try {
                $sheets = new GoogleSheets($spreadsheetID, $range);
            }
            catch (\Google\Service\Exception $ex) {
                $errors = collect($ex->getErrors())
                ->map(function($item){
                    return "Google Sheet Error: ".$item['message'];
                })
                ->toArray();
                throw ValidationException::withMessages($errors);
            }
            catch (\Exception $ex) {
                throw ValidationException::withMessages([$ex->getMessage()]);
            }

            $values = $sheets->get()->map(function($v){
                return $v->values();
            });

            $importObj = new VehicleBillsImport($request);

            $importObj->collection($values);

        }
        else{
            Excel::import(new VehicleBillsImport($request), $file);
        }

        return back()->with('message', "Import completed successfully!");
    }


    /**
     * Import Page of Sims
     *
    */
    public function showImportSims(Request $request, $config=null)
    {

        return view('Tenant.imports.sim.import', compact('config'));
    }

    /**
     * POST request of Sims
     *
    */
    public function import_sims(Request $request)
    {

        if($request->get('source', 'local') === 'google_sheet'){
            # Fetch source from google sheet

            $request->validate([
                'googlesheet_speadsheetid' => 'required|max:255',
                'googlesheet_range' => 'required|max:255'
            ]);

            $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
            $range = $request->get('googlesheet_range', null);

            # Fetch data
            try {
                $sheets = new GoogleSheets($spreadsheetID, $range);
            }
            catch (\Google\Service\Exception $ex) {
                $errors = collect($ex->getErrors())
                ->map(function($item){
                    return "Google Sheet Error: ".$item['message'];
                })
                ->toArray();
                throw ValidationException::withMessages($errors);
            }
            catch (\Exception $ex) {
                throw ValidationException::withMessages([$ex->getMessage()]);
            }

            if($request->has('preview_only')){
                $values = $sheets->get();
                return response()->json([$values]);
            }

            $values = $sheets->get(true);

            $importObj = new SimsImport();
            if (method_exists($importObj, 'rules')){
                # Do Validation

                $errors = [];

                foreach ($values as $key => $value) {

                    $validator = Validator::make($value->toArray(), $importObj->rules());

                    if($validator->fails()){
                        $single_errors = $validator->errors();

                        foreach ($single_errors->messages() as $name => $message) {
                            $errors[$name.'_'.$key] = "Error in row #".($key+1) . ". " . $message[0] . ' | Data: '.$value[$name];
                        }

                    }
                }

                if(count($errors) > 0){
                    throw ValidationException::withMessages($errors);
                }

            }

            $importObj->collection($values);

            return back()->with('message', "Import completed successfully!");
        }

        $request->validate([
            'attachment' => [
                'required',
                File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                    ->max(20 * 1024), // 20mb max
            ]
        ]);

        # ---------------------------
        #   Validate Headers
        # ---------------------------
        $headings = collect((new HeadingRowImport)->toArray($request->file('attachment')));

        if(count($headings) > 1){
            # Means workbook has multiple sheets, throw error
            throw ValidationException::withMessages([
                "#1" => "Multiple sheets found while only single sheet is allowed, please remove extra sheets from workbook"
            ]);
        }

        if($request->has('preview_only')){
            $preview_data = (new PreviewData)->toCollection($request->file('attachment'));
            return response()->json($preview_data);
        }

        Excel::import(new SimsImport, $request->file('attachment'));

        return back()->with('message', "Import completed successfully!");


    }

    /**
     * Import Page of Sim Bills
     *
    */
    public function showImportSimbills(Request $request, $config=null)
    {

        $selected_account = Account::where('handle', 'bank-main-rak-bank')->select('_id')->limit(1)->get()->first();
        if(!isset($selected_account)){
            $selected_account = \App\Accounts\Handlers\AccountGateway::getSelectedAccount();
        }

        return view('Tenant.imports.simbill.import', compact('config', 'selected_account'));
    }

    /**
     * POST request of Sim Bills
     *
    */
    public function import_simbills(Request $request)
    {
        $fromGoogleSheet = $request->get('source', 'local') === 'google_sheet';


        if($request->has('headings_only') || $request->has('preview_only')){

            if($fromGoogleSheet){
                $request->validate([
                    'googlesheet_speadsheetid' => 'required|max:255',
                    'googlesheet_range' => 'nullable|max:255'
                ]);
            }
            else{

                $request->validate([
                    'attachment' => [
                        'required',
                        File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->max(20 * 1024), // 20mb max
                    ],
                ]);

            }

        }
        else{

            if($fromGoogleSheet){
                $request->validate([
                    'googlesheet_speadsheetid' => 'required|max:255',
                    'googlesheet_range' => 'nullable|max:255',
                    'account_id' => 'required|max:255',
                    'month' => 'required|date',
                    'heading_mapper' => 'required|array'
                ]);
            }
            else{

                $request->validate([
                    'attachment' => [
                        'required',
                        File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->max(20 * 1024), // 20mb max
                    ],
                    'account_id' => 'required|max:255',
                    'month' => 'required|date',
                    'heading_mapper' => 'required|array'
                ]);
            }
        }

        if($request->has('preview_only')){
            if($fromGoogleSheet){

                $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
                $range = $request->get('googlesheet_range', 'Sheet1');
                if(!(isset($range) && trim($range) != '')) $range = "Sheet1";

                # Fetch data
                try {
                    $sheets = new GoogleSheets($spreadsheetID, $range);
                }
                catch (\Google\Service\Exception $ex) {
                    $errors = collect($ex->getErrors())
                    ->map(function($item){
                        return "Google Sheet Error: ".$item['message'];
                    })
                    ->toArray();
                    throw ValidationException::withMessages($errors);
                }
                catch (\Exception $ex) {
                    throw ValidationException::withMessages([$ex->getMessage()]);
                }

                $values = $sheets->get();
                return response()->json([$values]);
            }
            else{
                $preview_data = (new PreviewData)->toCollection($request->file('attachment'));
                return response()->json($preview_data);
            }
        }




        if($fromGoogleSheet){

            $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
            $range = $request->get('googlesheet_range', 'Sheet1');
            if(!(isset($range) && trim($range) != '')) $range = "Sheet1";

            # Fetch data
            try {
                $sheets = new GoogleSheets($spreadsheetID, str_contains($range, "!") ? $range : $range.'!1:1');
            }
            catch (\Google\Service\Exception $ex) {
                $errors = collect($ex->getErrors())
                ->map(function($item){
                    return "Google Sheet Error: ".$item['message'];
                })
                ->toArray();
                throw ValidationException::withMessages($errors);
            }
            catch (\Exception $ex) {
                throw ValidationException::withMessages([$ex->getMessage()]);
            }

            $headings = collect($sheets->headings())
            ->map(function($h){
                return strtolower( Str::slug( $h ) );
            });

            $headings = [[ $headings ]];



        }
        else{

            $file = $request->file('attachment');

            # ---------------------------
            #   Validate Headers
            # ---------------------------
            HeadingRowFormatter::extend('tmp', function($value, $key) {
                if(!isset($value) || trim($value) == '') return null;
                return $value;
            });
            HeadingRowFormatter::default('tmp');
            $headings = (new HeadingRowImport)->toArray($file);

        }


        if(count($headings) > 1){
            # Means workbook has multiple sheets, throw error
            throw ValidationException::withMessages([
                "#1" => "Multiple sheets found while only single sheet is allowed, please remove extra sheets from workbook"
            ]);
        }
        if($request->has('headings_only')){

            # this was called via ajax, so we will return in html view
            $headings = $headings[0][0];
            return view('Tenant.imports.simbill.partial-headings', compact('headings'))->render();

        }

        $mapper = collect($request->heading_mapper);

        # ----------------------------------------------------------
        # Custom validation to check if we have desired mapped items
        # ----------------------------------------------------------
        $requiredInMapper = [
            'sim_number',
            'amount'
        ];
        $foundInMapper = [];

        foreach ($mapper as $mapper_item) {
            if( in_array($mapper_item['destination'], $requiredInMapper) ){
                $foundInMapper[] = $mapper_item['destination'];
            }
        }

        $errors = [];
        foreach ($requiredInMapper as $item) {
            if( !in_array($item, $foundInMapper) ){
                $errors[] = [
                    $item => "\"$item\" field is required to map to atleast 1 item"
                ];
            }
        }

        if(count($errors) > 0){
            throw ValidationException::withMessages($errors);
            return;
        }


        HeadingRowFormatter::reset();

        if($fromGoogleSheet){

            $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
            $range = $request->get('googlesheet_range', "Sheet1");
            if(!(isset($range) && trim($range) != '')) $range = "Sheet1";

            # Fetch data
            try {
                $sheets = new GoogleSheets($spreadsheetID, $range);
            }
            catch (\Google\Service\Exception $ex) {
                $errors = collect($ex->getErrors())
                ->map(function($item){
                    return "Google Sheet Error: ".$item['message'];
                })
                ->toArray();
                throw ValidationException::withMessages($errors);
            }
            catch (\Exception $ex) {
                throw ValidationException::withMessages([$ex->getMessage()]);
            }

            $values = $sheets->get()->map(function($v){
                return $v->values();
            });

            $importObj = new SimbillsImport($request);

            $importObj->collection($values);

        }
        else{

            Excel::import(new SimbillsImport($request), $file);
        }


        return back()->with('message', "Import completed successfully!");
    }

    /**
     * Import Page of Installments
     *
    */
    public function showImportInstallments(Request $request, $config=null)
    {

        return view('Tenant.imports.installment.import', compact('config'));
    }

    /**
     * POST request of Installments
     *
    */
    public function import_installments(Request $request)
    {
        if($request->get('source', 'local') === 'google_sheet')
        {
            # Fetch source from google sheet

            $request->validate([
                'googlesheet_speadsheetid' => 'required|max:255',
                'googlesheet_range' => 'required|max:255'
            ]);

            $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
            $range = $request->get('googlesheet_range', null);

            # Fetch data
            try {
                $sheets = new GoogleSheets($spreadsheetID, $range);
            }
            catch (\Google\Service\Exception $ex) {
                $errors = collect($ex->getErrors())
                ->map(function($item){
                    return "Google Sheet Error: ".$item['message'];
                })
                ->toArray();
                throw ValidationException::withMessages($errors);
            }
            catch (\Exception $ex) {
                throw ValidationException::withMessages([$ex->getMessage()]);
            }

            if($request->has('preview_only')){
                $values = $sheets->get();
                return response()->json([$values]);
            }

            $values = $sheets->get(true);

            $importObj = new InstallmentsImport();
            if (method_exists($importObj, 'rules')){
                # Do Validation

                $errors = [];

                foreach ($values as $key => $value) {

                    $validator = Validator::make($value->toArray(), $importObj->rules());

                    if($validator->fails()){
                        $single_errors = $validator->errors();

                        foreach ($single_errors->messages() as $name => $message) {
                            $errors[$name.'_'.$key] = "Error in row #".($key+1) . ". " . $message[0] . ' | Data: '.$value[$name];
                        }

                    }
                }

                if(count($errors) > 0){
                    throw ValidationException::withMessages($errors);
                }

            }

            $importObj->collection($values);

            return back()->with('message', "Import completed successfully!");
        }
        else
        {
            $request->validate([
                'attachment' => [
                    'required',
                    File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                        ->max(20 * 1024), // 20mb max
                ]
            ]);

            # ---------------------------
            #   Validate Headers
            # ---------------------------
            $headings = collect((new HeadingRowImport)->toArray($request->file('attachment')));

            if(count($headings) > 1){
                # Means workbook has multiple sheets, throw error
                throw ValidationException::withMessages([
                    "#1" => "Multiple sheets found while only single sheet is allowed, please remove extra sheets from workbook"
                ]);
            }

            if($request->has('preview_only')){
                $preview_data = (new PreviewData)->toCollection($request->file('attachment'));
                return response()->json($preview_data);
            }

            Excel::import(new InstallmentsImport, $request->file('attachment'));
            return back()->with('message', "Import completed successfully!");
        }
    }

    /**
     * Import Page of Installments
     *
    */
    public function showImportIncomes(Request $request, $config=null)
    {

        $clients = Client::where('status', 'active')->is('aggregator')->get();


        return view('Tenant.imports.income.import', compact('config', 'clients'));
    }

    /**
     * POST request of Installments
     *
    */
    public function import_incomes(Request $request)
    {
        /**
         * # Global Validation + Source Identification
         */

        $request->validate([
            'month' => 'required',
            'client_id' => 'required|max:255',
        ]);

        $fromGoogleSheet = $request->get('source', 'local') === 'google_sheet';
        if(!$fromGoogleSheet){
            $file = $request->file('attachment');
        }

        if($request->has('headings_only') || $request->has('preview_only')){

            if($fromGoogleSheet){
                $request->validate([
                    'googlesheet_speadsheetid' => 'required|max:255',
                    'googlesheet_range' => 'required|max:255'
                ]);
            }
            else{

                $request->validate([
                    'attachment' => [
                        'required',
                        File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->max(20 * 1024), // 20mb max
                    ],
                ]);

            }

        }
        else{

            if($fromGoogleSheet){
                $request->validate([
                    'googlesheet_speadsheetid' => 'required|max:255',
                    'googlesheet_range' => 'required|max:255',
                    'account_id' => 'required|max:255',
                    'client_id' => 'required|max:255',
                    'heading_mapper' => 'required|array'
                ]);
            }
            else{
                $request->validate([
                    'attachment' => [
                        'required',
                        File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->max(20 * 1024), // 20mb max
                    ],
                    'account_id' => 'required|max:255',
                    'client_id' => 'required|max:255',
                    'heading_mapper' => 'required|array'
                ]);
            }
        }

        /**
         * # If Returning Data For Header Mapings
         */

        if($request->has('headings_only') && !$request->has('preview_only')){
            if($fromGoogleSheet){
                $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
                $range = $request->get('googlesheet_range', null);

                # Fetch data
                try {
                    $sheets = new GoogleSheets($spreadsheetID, str_contains($range, "!") ? $range : $range.'!1:1');
                }
                catch (\Google\Service\Exception $ex) {
                    $errors = collect($ex->getErrors())
                    ->map(function($item){
                        return "Google Sheet Error: ".$item['message'];
                    })
                    ->toArray();
                    throw ValidationException::withMessages($errors);
                }
                catch (\Exception $ex) {
                    throw ValidationException::withMessages([$ex->getMessage()]);
                }

                $headings = [[$sheets->headings()]];



            }
            else{
                # ---------------------------
                #   Validate Headers
                # ---------------------------
                HeadingRowFormatter::extend('tmp', function($value, $key) {
                    if(!isset($value) || trim($value) == '') return null;
                    return $value;
                });
                HeadingRowFormatter::default('tmp');
                $headings = (new HeadingRowImport)->toArray($file);

            }


            if(count($headings) > 1){
                # Means workbook has multiple sheets, throw error
                throw ValidationException::withMessages([
                    "#1" => "Multiple sheets found while only single sheet is allowed, please remove extra sheets from workbook"
                ]);
            }
            # this was called via ajax, so we will return in html view
            $headings = $headings[0][0];
            return view('Tenant.imports.income.partial-headings', compact('headings'))->render();
        }
        /**
         * # Apply Validations For Preview And Importing
         */

        $mapper = collect($request->heading_mapper);

        # ----------------------------------------------------------
        # Custom validation to check if we have desired mapped items
        # ----------------------------------------------------------
        $requiredInMapper = [
            'refid',
            'company_earning',
            'driver_earning',
            'amount'
        ];
        $foundInMapper = [];

        foreach ($mapper as $mapper_item) {
            if( in_array($mapper_item['destination'], $requiredInMapper) ){
                $foundInMapper[] = $mapper_item['destination'];
            }
        }

        $errors = [];
        foreach ($requiredInMapper as $item) {
            if( !in_array($item, $foundInMapper) ){
                $errors[] = [
                    $item => "\"$item\" field is required to map to atleast 1 item"
                ];
            }
        }

        if(count($errors) > 0){
            throw ValidationException::withMessages($errors);
            return;
        }

        HeadingRowFormatter::reset();
        /**
         * # If Returning Data For Preview
         */

        if($request->has('preview_only')){
            
            $importData = [];

            $month = Carbon::parse($request->month)->startOfMonth();

            $end = null;
            if(isset($request->end_date) && $request->end_date !== ''){
                $end = Carbon::parse($request->end_date);
            }
            $start = null;
            if(isset($request->start_date) && $request->start_date !== ''){
                $start = Carbon::parse($request->start_date);
            }
    
            if(!isset($end) && !isset($start)){
                // Monthly payment
                $start = $month->copy()->startOfMonth();
                $end = $month->copy()->endOfMonth();
            }
    
            if(!isset($end)){
                $end = Carbon::parse($request->start_date);
            }

            #  Get Client Entities
            $client = Client::with([
                'entities' => function($query) use ($start, $end){
                    # Bind 2 where into 1
                    $query->where(function ($q) use ($start, $end) {
                        # This where will filter histories with "active" entities
                        $q->where(function ($q2) use ($end) {
                            $q2->where('assign_date', '<=', $end)->whereNull('unassign_date');
                        })
                            # This where will filter histories with "inactive" entities
                            ->orWhere(function ($q2) use ($start, $end) {
                                $q2->where('assign_date', '<=', $end)->where('unassign_date', '>=', $start)->whereNotNull('unassign_date');
                            });
                    });
                },
                'entities.source'
            ])
            ->find((int) $request->client_id);

            if(!isset($client)){
                throw ValidationException::withMessages(['client' => "Client Not Found Against {$request->client_id}"]);
            }

            // Fetch Import Data from source (google/local)
            if($fromGoogleSheet){

                $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
                $range = $request->get('googlesheet_range', 'Sheet1');
                if(!(isset($range) && trim($range) != '')) $range = "Sheet1";

                # Fetch data
                try {
                    $sheets = new GoogleSheets($spreadsheetID, $range);
                }
                catch (\Google\Service\Exception $ex) {
                    $errors = collect($ex->getErrors())
                    ->map(function($item){
                        return "Google Sheet Error: ".$item['message'];
                    })
                    ->toArray();
                    throw ValidationException::withMessages($errors);
                }
                catch (\Exception $ex) {
                    throw ValidationException::withMessages([$ex->getMessage()]);
                }

                $importData = $sheets->get();

            }
            else{
                $importData = (new PreviewData)->toCollection($request->file('attachment'))->first();

            }

            // Check if drivers found against fieds
            #   Process Loaded Data For Preview
            $importData = $importData->map(function($row, $rowIndex) use ($mapper, $client){
                $refid = null;

                foreach ($row as $index => $value) {
                    $mapItem = $mapper->where('title', $index)->first();
                    if(isset($mapItem)){
                        $destination = $mapItem['destination'];

                        if($destination === "refid" && !isset($refid)){
                            $refid = $value;
                        }

                    }
                }
                $driver = '<span class="text-danger">Not found</span>';
                $entity = $client->entities->where('refid', $refid)->first();
                if(isset($entity)){
                    $driver = '';
                }

                return ['System Driver' => $driver , ...$row];

            });
            $importData = $importData->sortByDesc('Syatem Driver')->values();

            return response()->json([$importData]);
        }
        /**
         * # Import All Data After Everything
         */

        if($fromGoogleSheet){

            $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
            $range = $request->get('googlesheet_range', null);

            # Fetch data
            try {
                $sheets = new GoogleSheets($spreadsheetID, $range);
            }
            catch (\Google\Service\Exception $ex) {
                $errors = collect($ex->getErrors())
                ->map(function($item){
                    return "Google Sheet Error: ".$item['message'];
                })
                ->toArray();
                throw ValidationException::withMessages($errors);
            }
            catch (\Exception $ex) {
                throw ValidationException::withMessages([$ex->getMessage()]);
            }

            $values = $sheets->get()->map(function($v){
                return $v->values();
            });

            $importObj = new IncomesImport($request);

            $importObj->collection($values);

        }
        else{
            Excel::import(new IncomesImport($request), $file);
        }

        return back()->with('message', "Import completed successfully!");
    }


    /**
     * Import Page of Transaction Ledger
     *
    */
    public function showImportTransactionForm(Request $request, $id,$config=null)
    {

        $titles = TransactionLedger::select('title')
        ->where('tag', 'ledger')
        ->get()
        ->map(function($item){
            $item->title = strtolower($item->title);
            return $item;
        })
        ->keyBy('title')
        ->keys()
        ->map(function($item){
            return ucfirst($item);
        });

        return view('Tenant.imports.transactionledger.import', compact('config', 'id', 'titles'));
    }

    /**
     * POST request of Transaction Ledger
     *
    */
    public function import_transactions(Request $request)
    {
        $fromGoogleSheet = $request->get('source', 'local') === 'google_sheet';
        // Perform Validations
        if($fromGoogleSheet){

            if($request->has('preview_only')){
                $request->validate([
                    'googlesheet_speadsheetid' => 'required|max:255',
                    'googlesheet_range_payable' => 'required|max:255',
                    'googlesheet_range_chargeable' => 'required|max:255'
                ]);
            }
            else{

                $request->validate([
                    'googlesheet_speadsheetid' => 'required|max:255',
                    'googlesheet_range_payable' => 'required|max:255',
                    'googlesheet_range_chargeable' => 'required|max:255',
                    'title' => 'required|max:255',
                    'month' => 'required|max:255',
                    'given_date' => 'required|max:255',
                ]);

            }
            $spreadsheetID = $request->get('googlesheet_speadsheetid', null);
            $range = [$request->get('googlesheet_range_payable'),$request->get('googlesheet_range_chargeable')];
            # Fetch data
            try {
                $sheets = new GoogleSheets($spreadsheetID, $range);
            }
            catch (\Google\Service\Exception $ex) {
                $errors = collect($ex->getErrors())
                ->map(function($item){
                    return "Google Sheet Error: ".$item['message'];
                })
                ->toArray();
                throw ValidationException::withMessages($errors);
            }
            catch (\Exception $ex) {
                throw ValidationException::withMessages([$ex->getMessage()]);
            }

            $values = $sheets->get(true);
            $chargeable_values = $values[$request->get('googlesheet_range_chargeable')];

            $payable_values = $values[$request->get('googlesheet_range_payable')];

            if($request->has('preview_only')) return response()->json([$chargeable_values, $payable_values]);
        }
        else{
            if($request->has('preview_only')){
                $request->validate([
                    'attachment' => [
                        'required',
                        File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->max(20 * 1024), // 20mb max
                    ],
                ]);

                $preview_data = (new PreviewData)->toCollection($request->file('attachment'));
                return response()->json($preview_data);
            }

            $request->validate([
                'attachment' => [
                    'required',
                    File::types(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                        ->max(20 * 1024), // 20mb max
                ],
                'title' => 'required|max:255',
                'month' => 'required|max:255',
                'given_date' => 'required|max:255',
            ]);
        }
        // Process Data
        $transactionLedger = new TransactionLedger;
        $transactionLedger->title = $request->get('title');
        $transactionLedger->given_date = Carbon::parse($request->get('given_date'))->format('Y-m-d');
        $transactionLedger->month = Carbon::parse($request->get('month'))->startOfMonth()->format('Y-m-d');
        $transactionLedger->amount = 0;
        $transactionLedger->description = $request->get('description');
        $transactionLedger->by = Auth::user()->id;
        $transactionLedger->tag = "ledger";
        $transactionLedger->save();

        $ledgers = [];
        if($fromGoogleSheet){
            $importObj = new TransactionLedgerTrigger($transactionLedger, $ledgers);

            $payableObj = $importObj->sheets()['payable'];
            $chargeableObj = $importObj->sheets()['chargeable'];
            $payableObj->collection($payable_values);
            $chargeableObj->collection($chargeable_values);

        }
        else Excel::import(new TransactionLedgerTrigger($transactionLedger, $ledgers), $request->file('attachment'));

        // return 'ok';

        return back()->with('message', "Import completed successfully!");
    }



    /**
     * DELETE request of Delete the transaction
     *
    */
    public function delete_import_history($id)
    {
        $history = ImportHistory::findOrFail($id);

        $ledgers = [];

        if(isset($history->record_relations) && count($history->record_relations) > 0){

            # Delete each ledger
            foreach ($history->record_relations as $record_relation) {
                if($record_relation['model'] === Ledger::class){

                    # This is a ledger, delete it via its own function so everything will be delete too

                    $ledgerId = $record_relation['id'];

                    $exists = Ledger::where('id', $ledgerId)->exists();
                    if(!$exists){
                        # Already Deleted

                        $ledgers[] = [
                            'status' => 2,
                            'message' => "Already deleted"
                        ];

                        continue;
                    }
                    $response = (new LedgerController)->delete($ledgerId)->getData();

                    $response->model = $record_relation['model'];
                    $response->id = $record_relation['id'];
                    $ledgers[] = $response;

                }
                else{
                    # In some cases where ledger is not defined, we need to just delete the single entry
                    $model = ($record_relation['model'])::find($record_relation['id']);
                    if(isset($model)){
                        $ledgers[] = [
                            'model' => $record_relation['model'],
                            'id' => $record_relation['id'],
                            'status' => 1,
                            'feed' => [$model->toArray()]
                        ];
                        $model->delete();
                    }
                }
            }

            $history->deleted_feed = $ledgers;
            $history->update();

            $history->delete();
        }
        else{
            $history->deleted_feed = null;
            $history->delete();
        }

        return response()->json([
            "status" => 1,
            'ledgers' => $ledgers
        ]);
    }



}
