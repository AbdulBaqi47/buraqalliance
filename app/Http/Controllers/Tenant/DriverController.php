<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\{Addon, AddonsSetting, Driver, DriverBookingHistory, Vehicle, VehicleType,DriverPassport, VehicleBooking, VehicleLedger, VehicleLedgerItem};
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\{File,Request, Response};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tenant_auth');
    }


    // Show All Drivers
    public function showDrivers($type)
    {
        return view('Tenant.drivers.view',compact('type'));
    }

    /**
     * View Page of Vehicles - Vehicle
     *
    */
    public function ViewDrivers()
    {
        return $this->showDrivers('driver');
    }

    /**
     * View Page of clients - Supplier
     *
    */
    public function ViewRiders()
    {
        return $this->showDrivers('rider');
    }

    // Form For Creating New Driver
    public function addDrivers(Request $request)
    {
        $request->validate([
            'type' => ['required','in:driver,rider']
        ]);

        $addon_types = AddonsSetting::select('id', 'title', 'amount', 'types')
        ->where('source_type', 'driver')
        ->whereIn('title', ["Visa", "Driving License Dubai", "Driving License Sharjah", "RTA"])
        ->get();

        $addon_settings = [];
        foreach ($addon_types as $addon_type) {
            $addon_settings[$addon_type->title] = $addon_type->toArray();
        }

        if(!isset($addon_settings['Visa'])){
            throw new Exception("Please create addon setting for Visa to access this page.");
        }

        // return $addon_types;

        return view('Tenant.drivers.create_new', [ 'addon_types' => $addon_types, 'addon_settings' => $addon_settings, 'type' => $request->type]);
        // return view('Tenant.drivers.create', [ 'vehicles' => $vehicles, 'bookings' => $bookings]);
    }

    // Storing New Driver Into Database
    public function storeDriver(Request $request)
    {
        # ----------------------------
        #     Basic Validation
        # ----------------------------

        $request->validate([
            // Basic
            'name' => ['required', 'string'],
            'type' => 'required',
            'location' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:drivers,email'],
            'full_phone' => ['nullable','numeric'],

            // To handle duplicate drivers
            'passport_number' => ['required', 'unique:drivers,passport_number'],

            // Images
            'profile_picture' =>  ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'liscence_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'liscence_pictures_back' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'emirates_id_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'emirates_id_pictures_back' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'passport_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'visa_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
        ]);

        $errors = [];


        # ----------------------------
        #       Saving Driver
        # ----------------------------

        $payloadDateFormat = "F d, Y";
        $payloadAddon = $request->get('addon', []);

        $driver = new Driver;

        /* --------------- */
        /*  Basic Details  */
        /* --------------- */
        $driver->name = $request->get('name', null);
        $driver->location = $request->get('location', null);
        $driver->type = $request->get('type', null);
        $driver->email = $request->get('email', null);
        $driver->date_of_birth = isset($request->date_of_birth) ? Carbon::createFromFormat($payloadDateFormat, $request->date_of_birth)->format('Y-m-d') : null;
        $driver->phone_number = $request->get('full_phone', null);
        $driver->is_pasport_collected = $request->has('is_pasport_collected');
        $driver->passport_number = $request->get('passport_number', null);
        $driver->passport_expiry = isset($request->passport_expiry) ? Carbon::createFromFormat($payloadDateFormat, $request->passport_expiry)->format('Y-m-d') : null;
        $driver->additional_details = $request->get('additional_details', null);
        $driver->nationality = $request->get('nationality', null);
        $driver->profile_picture = $request->hasFile('profile_picture') ? Storage::putfile('drivers/profile_picture', $request->file('profile_picture')) : null;

        if($request->hasFile('passport_pictures_front')){
            $driver->passport_pictures = [
                'front' => Storage::putfile('drivers/passport_pictures', $request->file('passport_pictures_front'))
            ];
        }
        else{
            $driver->passport_pictures = null;
        }

        $driver->emirates_id_no = null;
        $driver->emirates_id_expiry = null;
        $driver->emirates_id_pictures = null;

        $driver->visa_expiry = null;
        $driver->visa_pictures = null;

        $driver->liscence_number = null;
        $driver->liscence_expiry = null;
        $driver->liscence_pictures = null;

        $driver->rta_permit_number = null;
        $driver->rta_permit_expiry = null;
        $driver->rta_permit_pictures = null;


        # Addons base variables
        # Based on this, addon will be created if required
        $VisaAddonPayload = null;
        $LicenseAddonPayload = null;
        $RTAAddonPayload = null;

        /* --------------- */
        /*  Visa Details  */
        /* --------------- */
        $driver->has_visa = $request->has('has_visa');
        $driver->is_visa_skipped = $request->has('is_visa_skipped');
        if($request->has('has_visa')){
            # Driver already have visa, save visa details

            // --> Emirates ID
            $driver->emirates_id_no = $request->get('emirates_id_no', null);
            $driver->emirates_id_expiry = isset($request->emirates_id_expiry) ? Carbon::createFromFormat($payloadDateFormat, $request->emirates_id_expiry)->format('Y-m-d') : null;

            if($request->hasFile('emirates_id_pictures_front') || $request->hasFile('emirates_id_pictures_back')){
                $driver->emirates_id_pictures = [
                    'front' => $request->hasFile('emirates_id_pictures_front') ? Storage::putfile('drivers/emirates_id_pictures', $request->file('emirates_id_pictures_front')) : null,
                    'back' => $request->hasFile('emirates_id_pictures_back') ? Storage::putfile('drivers/emirates_id_pictures', $request->file('emirates_id_pictures_back')) : null,
                ];
            }
            else{
                $driver->emirates_id_pictures = null;
            }

            // --> VISA
            $driver->visa_expiry = isset($request->visa_expiry) ? Carbon::createFromFormat($payloadDateFormat, $request->visa_expiry)->format('Y-m-d') : null;
            if($request->hasFile('visa_pictures_front')){
                $driver->visa_pictures = [
                    'front' => $request->hasFile('visa_pictures_front') ? Storage::putfile('drivers/visa_pictures', $request->file('visa_pictures_front')) : null,
                ];
            }
            else{
                $driver->visa_pictures = null;
            }


        }
        else{
            # Create addon for visa

            if(isset($payloadAddon['Visa'])){
                $VisaAddonPayload = $payloadAddon['Visa'];
            }

        }

        /* ----------------- */
        /*  License Details  */
        /* ----------------- */
        $driver->has_license = $request->has('has_license');
        $driver->is_license_skipped = $request->has('is_license_skipped');
        if($request->has('has_license')){
            # Driver already have license, save details

            // --> License
            $driver->liscence_number = $request->get('liscence_number', null);
            $driver->liscence_expiry = isset($request->liscence_expiry) ? Carbon::createFromFormat($payloadDateFormat, $request->liscence_expiry)->format('Y-m-d') : null;

            if($request->hasFile('liscence_pictures_front') || $request->hasFile('liscence_pictures_back')){
                $driver->liscence_pictures = [
                    'front' => $request->hasFile('liscence_pictures_front') ? Storage::putfile('drivers/liscence_pictures', $request->file('liscence_pictures_front')) : null,
                    'back' => $request->hasFile('liscence_pictures_back') ? Storage::putfile('drivers/liscence_pictures', $request->file('liscence_pictures_back')) : null,
                ];
            }
            else{
                $driver->liscence_pictures = null;
            }


        }
        else{
            # Create addon for license

            $license_selection = $request->get('license_selection', null);
            if($license_selection === "sharjah"){
                $LicenseAddonPayload = $payloadAddon['Driving License Sharjah']??null;
            }
            else{
                $LicenseAddonPayload = $payloadAddon['Driving License Dubai']??null;
            }
        }

        /* ----------------- */
        /*    RTA Details    */
        /* ----------------- */
        $driver->has_rta = $request->has('has_rta');
        $driver->is_rta_skipped = $request->has('is_rta_skipped');
        if($request->has('has_rta')){
            # Driver already have license, save details

            // --> RTA Permit
            $driver->rta_permit_number = $request->get('rta_permit_number', null);
            $driver->rta_permit_expiry = isset($request->rta_permit_expiry) ? Carbon::createFromFormat($payloadDateFormat, $request->rta_permit_expiry)->format('Y-m-d') : null;

            if($request->hasFile('rta_permit_pictures_front') || $request->hasFile('rta_permit_pictures_back')){
                $driver->rta_permit_pictures = [
                    'front' => $request->hasFile('rta_permit_pictures_front') ? Storage::putfile('drivers/rta_permit_pictures', $request->file('rta_permit_pictures_front')) : null,
                    'back' => $request->hasFile('rta_permit_pictures_back') ? Storage::putfile('drivers/rta_permit_pictures', $request->file('rta_permit_pictures_back')) : null,
                ];
            }
            else{
                $driver->rta_permit_pictures = null;
            }

        }
        else{
            # Create addon for RTA

            if(isset($payloadAddon['RTA'])){
                $RTAAddonPayload = $payloadAddon['RTA'];
            }

        }

        # Current status of driver
        # This is dynamic based on addons
        $driver->status = "initiated";

        # ---------------- [ Save Driver ] ---------------- #
        $driver->save();


        /* ----------------- */
        /*       ADDONS      */
        /* ----------------- */

        $addons_list = [
            'visa' => false,
            'license' => false,
            'rta' => false,
        ];

        # --> ADDON: Visa
        if(isset($VisaAddonPayload)){

            $setting_id = $VisaAddonPayload['id'];

            $addon = new Addon;
            $addon->payment_status = 'pending';
            $addon->setting_id = $setting_id;
            $addon->date = Carbon::now()->format('Y-m-d');
            $addon->price = (float)$VisaAddonPayload['price']??0;
            $addon->cost = 0;
            $addon->source_type = 'driver';
            $addon->source_model = get_class($driver);
            $addon->source_id = $driver->id;
            $addon->additional_details = $VisaAddonPayload['additional_details']??null;
            $addon->status = 'pending_to_start';
            $addon->current_stage = null;

            if(isset($VisaAddonPayload['override_setting'])){
                # Override default setting
                $addon->override_types = collect($VisaAddonPayload['overrides'])
                ->map(function($type){
                    return [
                        'title' => $type['title'],
                        'display_title' => $type['display_title'],
                        'amount' => isset($type['amount']) ? (float) $type['amount'] : null,
                        'charge' => isset($type['charge']) ? true : false,
                    ];
                })
                ->toArray();
            }

            $addon->save();

            $addons_list['visa'] = true;
        }

        # --> ADDON: LICENSE
        if(isset($LicenseAddonPayload)){
            $setting_id = $LicenseAddonPayload['id'];

            $addon = new Addon;
            $addon->payment_status = 'pending';
            $addon->setting_id = $setting_id;
            $addon->date = Carbon::now()->format('Y-m-d');
            $addon->price = (float)$LicenseAddonPayload['price']??0;
            $addon->cost = 0;
            $addon->source_type = 'driver';
            $addon->source_model = get_class($driver);
            $addon->source_id = $driver->id;
            $addon->additional_details = $LicenseAddonPayload['additional_details']??null;
            $addon->status = 'initiated';
            $addon->current_stage = null;

            if(!$addons_list['visa']){
                // Visa not added, move addon to toDo
                $addon->status = 'pending_to_start';
            }

            if(isset($LicenseAddonPayload['override_setting'])){
                # Override default setting
                $addon->override_types = collect($LicenseAddonPayload['overrides'])
                ->map(function($type){
                    return [
                        'title' => $type['title'],
                        'display_title' => $type['display_title'],
                        'amount' => isset($type['amount']) ? (float) $type['amount'] : null,
                        'charge' => isset($type['charge']) ? true : false,
                    ];
                })
                ->toArray();
            }

            $addon->save();

            $addons_list['license'] = true;
        }

        # --> ADDON: RTA
        if(isset($RTAAddonPayload)){

            $setting_id = $RTAAddonPayload['id'];

            $addon = new Addon;
            $addon->payment_status = 'pending';
            $addon->setting_id = $setting_id;
            $addon->date = Carbon::now()->format('Y-m-d');
            $addon->price = (float)$RTAAddonPayload['price']??0;
            $addon->cost = 0;
            $addon->source_type = 'driver';
            $addon->source_model = get_class($driver);
            $addon->source_id = $driver->id;
            $addon->additional_details = $RTAAddonPayload['additional_details']??null;
            $addon->status = 'initiated';
            $addon->current_stage = null;

            if(!$addons_list['visa'] && !$addons_list['license']){
                // Visa & License not added, move addon to toDo
                $addon->status = 'pending_to_start';
            }

            if(isset($RTAAddonPayload['override_setting'])){
                # Override default setting
                $addon->override_types = collect($RTAAddonPayload['overrides'])
                ->map(function($type){
                    return [
                        'title' => $type['title'],
                        'display_title' => $type['display_title'],
                        'amount' => isset($type['amount']) ? (float) $type['amount'] : null,
                        'charge' => isset($type['charge']) ? true : false,
                    ];
                })
                ->toArray();
            }

            $addon->save();

            $addons_list['rta'] = true;
        }



        if(count($errors) > 0){
            throw ValidationException::withMessages($errors);
            return;
        }

        return response()->json([
            'status' => 1,
            'driver_id' => $driver->id
        ]);

    }

    // Show Form For Editing Specific Driver
    public function showEditDriver(int $driver_id)
    {
        $driver = Driver::findOrFail($driver_id);

        return view('Tenant.drivers.edit', ['driver' => $driver]);
    }

    // Update Driver From Database
    public function updateDriver(Request $request, int $driver)
    {
        $driver = Driver::findOrFail($driver);
        $request->validate([
            'name' => ['required', 'string'],
            'email' => [
                'required', 'email', function ($attribute, $value, $fail) use ($driver) {
                    $existingValue = Driver::where('email', $value)
                        ->where('id', '!=', $driver->id)
                        ->exists();

                    if ($existingValue) {
                        $fail("The $attribute has already been taken.");
                    }
                },
            ],
            'full_phone' => ['nullable', 'numeric'],
            'location' => ['required', 'string'],
            'profile_picture' =>  ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'liscence_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'liscence_pictures_back' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'emirates_id_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'emirates_id_pictures_back' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'passport_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'visa_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'rta_permit_pictures_front' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],
            'rta_permit_pictures_back' => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff', 'max:4096'],

            'passport_number' => [
                'required',
                function ($attribute, $value, $fail) use ($driver) {
                    $existingValue = Driver::where('passport_number', $value)
                        ->where('id', '!=', $driver->id)
                        ->exists();

                    if ($existingValue) {
                        $fail("The $attribute has already been taken.");
                    }
                }
            ],
        ]);
        $driver->is_pasport_collected = false;
        if ($request->has('is_pasport_collected')) {
            $driver->is_pasport_collected = true;
        }
        // Update driver information from the request
        $driver->name = $request->input('name');
        $driver->email = $request->input('email');
        $driver->type = $request->get('type', null);
        $driver->location = $request->input('location');
        $driver->date_of_birth = isset($request->date_of_birth) ? Carbon::parse($request->date_of_birth)->format('Y-m-d') : null;
        $driver->phone_number = $request->input('full_phone');
        $driver->liscence_number = $request->input('liscence_number');
        $driver->liscence_expiry = isset($request->liscence_expiry) ? Carbon::parse($request->liscence_expiry)->format('Y-m-d') : null;
        $driver->emirates_id_no = $request->input('emirates_id_no');
        $driver->emirates_id_expiry = isset($request->emirates_id_expiry) ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : null;
        $driver->passport_number = $request->input('passport_number');
        $driver->passport_expiry = isset($request->passport_expiry) ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : null;
        $driver->visa_expiry = isset($request->visa_expiry) ? Carbon::parse($request->visa_expiry)->format('Y-m-d') : null;
        $driver->rta_permit_number = $request->input('rta_permit_number');
        $driver->rta_permit_expiry = isset($request->rta_permit_expiry) ? Carbon::parse($request->rta_permit_expiry)->format('Y-m-d') : null;
        $driver->nationality = $request->input('nationality');
        $driver->additional_details = $request->input('additional_details');
        // Update profile picture
        if ($request->hasFile('profile_picture')) {
            $profilePicture = Storage::putfile('drivers/profile_picture', $request->file('profile_picture'));
            $driver->profile_picture = $profilePicture;
        } else {
            $driver->profile_picture = null;
        }

        // Update license pictures
        $liscence_pictures = $driver->liscence_pictures;
        if ($request->hasFile('liscence_pictures_front')) {
            $liscencePicturesFront = Storage::putfile('drivers/liscence_pictures', $request->file('liscence_pictures_front'));
            $liscence_pictures['front'] = $liscencePicturesFront;
        } else {
            $liscence_pictures['front'] = null;
        }

        if ($request->hasFile('liscence_pictures_back')) {
            $liscencePicturesBack = Storage::putfile('drivers/liscence_pictures', $request->file('liscence_pictures_back'));
            $liscence_pictures['back'] = $liscencePicturesBack;
        } else {
            $liscence_pictures['back'] = null;
        }
        $driver->liscence_pictures = $liscence_pictures;

        // Update Emirates ID pictures
        $emirates_id_pictures = $driver->emirates_id_pictures;
        if ($request->hasFile('emirates_id_pictures_front')) {
            $emiratesIdPicturesFront = Storage::putfile('drivers/emirates_id_pictures', $request->file('emirates_id_pictures_front'));
            $emirates_id_pictures['front'] = $emiratesIdPicturesFront;
        } else {
            $emirates_id_pictures['front'] = null;
        }

        if ($request->hasFile('emirates_id_pictures_back')) {
            $emiratesIdPicturesBack = Storage::putfile('drivers/emirates_id_pictures', $request->file('emirates_id_pictures_back'));
            $emirates_id_pictures['back'] = $emiratesIdPicturesBack;
        } else {
            $emirates_id_pictures['back'] = null;
        }
        $driver->emirates_id_pictures = $emirates_id_pictures;

        // Update passport pictures
        $passport_pictures = $driver->passport_pictures;
        if ($request->hasFile('passport_pictures_front')) {
            $passportPicturesFront = Storage::putfile('drivers/passport_pictures', $request->file('passport_pictures_front'));
            $passport_pictures['front'] = $passportPicturesFront;
        } else {
            $passport_pictures['front'] = null;
        }
        $driver->passport_pictures = $passport_pictures;

        // Update visa pictures
        $visa_pictures = $driver->visa_pictures;
        if ($request->hasFile('visa_pictures_front')) {
            $visaPicturesFront = Storage::putfile('drivers/visa_pictures', $request->file('visa_pictures_front'));
            $visa_pictures['front'] = $visaPicturesFront;
        } else {
            $visa_pictures['front'] = null;
        }
        $driver->visa_pictures = $visa_pictures;

        // Update RTA permit pictures
        $rta_permit_pictures = $driver->rta_permit_pictures;
        if ($request->hasFile('rta_permit_pictures_front')) {
            $rtaPermitPicturesFront = Storage::putfile('drivers/rta_permit_pictures', $request->file('rta_permit_pictures_front'));
            $rta_permit_pictures['front'] = $rtaPermitPicturesFront;
        } else {
            $rta_permit_pictures['front'] = null;
        }

        if ($request->hasFile('rta_permit_pictures_back')) {
            $rtaPermitPicturesBack = Storage::putfile('drivers/rta_permit_pictures', $request->file('rta_permit_pictures_back'));
            $rta_permit_pictures['back'] = $rtaPermitPicturesBack;
        } else {
            $rta_permit_pictures['back'] = null;
        }
        $driver->rta_permit_pictures = $rta_permit_pictures;
        // Save the updated driver information
        $driver->save();
        return redirect(route('tenant.admin.drivers.viewDetails', $driver->id))->with('message', 'Record Updated Successfully.');
    }

    // Delete Driver From Database
    public function delete(int $driver_id)
    {
        $driver = Driver::find($driver_id);
        if ($driver) {
            $driver->delete();
            return response()->json(['message' => 'Record Deleted Successfully.', 'status' => 204]);
        }
        return response()->json(['message' => 'Driver Deletion Failed', 'status' => 404]);
    }

    // Show Single Driver Full Details
    public function viewDriverDetails(Request $request, int $driver_id)
    {
        $driver = Driver::where('id', $driver_id)->with(['booking'])->first();
        return view('Tenant.drivers.details.view', compact('driver'));
    }
    public function changeStatusView(int $driver_id){
        $driver = Driver::findOrFail($driver_id);
        $status = $driver->status ?? '';
        $driverOptions = json_encode([
            [
                'id'=>'license_under_processing',
                'text' => 'License Under Processing',
                'selected' => $status === 'license_under_processing',
            ],
            [
                'id'=>'rta_under_processing',
                'text' => 'RTA Under Processing',
                'selected' => $status === 'rta_under_processing',
            ],
            [
                'id'=>'available',
                'text' => 'Available',
                'selected' => $status === 'available',
            ],
        ]);
        return view('Tenant.drivers.actions.change_driver_status',compact('driverOptions','driver'));
    }
    public function changeStatusAction(Request $request, int $driver_id){
        $driver = Driver::findOrFail($driver_id);
        $driver->status = $request->status;
        $driver->save();
        return response()->json(['status' => 1]);
    }
    public function changeBookingView(int $driver_id){
        $driver = Driver::findOrFail($driver_id);
        # Fetch All Ledgers Soo That We Won't Need To Run Multiple Queries
        $all_ledgers = VehicleLedger::with([
            'items' => function ($query) use ($driver) {
                $query->where('driver_id', $driver->id);
            }
        ])
        ->whereHas('items', function ($query) use ($driver) {
            $query->where('driver_id', $driver->id);
        })->get();
        # Filter ledgers for current booking
        $vehicle_ledgers = $all_ledgers->where('vehicle_booking_id','=', $driver->booking_id);
        # We Only Need items from all these ledgers soo pluck and flatten to form an array of all items
        $vehicle_ledger = $vehicle_ledgers->pluck('items')->flatten();
        # Filter Data For Old Bookings From All Ledger Entries
        $vehicle_ledger_old = $all_ledgers
        ->where('vehicle_booking_id', '!=', $driver->booking_id)
        ->flatMap(function($value){  # Used FlatMap There to embed booking_id into each item
            return $value->items->map(function($item) use ($value){
                $item['booking_id'] = $value->vehicle_booking_id;
                return $item;
            });
        });
        # Fetch All Bookings For Select Options View Purpose
        $bookings = VehicleBooking::with([
                'vehicle',
                'investor' => function($query){
                    $query->select('id', 'name');
                },
                'drivers' => function($query){
                    $query->select('id', 'booking_id', 'name');
                }
            ])
        ->get()
        ->map(function ($item) use ($driver) {
            $prefix = $item->status === 'open' ? "B#$item->id" : "V#$item->id / {$item?->vehicle?->plate}";
            $drivers = $item->drivers->map(function($driver){
                return $driver->full_name;
            })
            ->values()
            ->toArray();

            $driverList = '';
            if(count($drivers) > 0){
                $driverList = ' (Drivers: '.(implode(' | ', $drivers)).')';
            }
            return [
                'id' => $item->id,
                'text' => $prefix. ' / '.$item->investor->name . $driverList,
                'selected' => $driver->booking_id === $item->id
            ];
        });
        return view('Tenant.drivers.actions.change_driver_booking',compact('driver','bookings','vehicle_ledger','vehicle_ledger_old'));
    }
    public function changeBookingAction(Request $request, int $driver_id){
        $request->validate([
            'booking_id' => ['required', 'numeric'],
            'assign_date' => ['required']
        ]);
        // Data Cleaning & Collecting
        $new_booking_id = (int) $request->booking_id;
        $selected_ledgers = $request->selected_ledgers;
        // Find The Driver To Be Updated
        $driver = Driver::with('booking_history')->findOrFail($driver_id);
        // Get Selected Ledger Entries need To be Moved
        if(isset($selected_ledgers) && count($selected_ledgers) > 0){
            $ledger_items = VehicleLedgerItem::whereIn('_id',$selected_ledgers);
            $new_ledger_id = VehicleLedger::where('vehicle_booking_id','=',$new_booking_id)->pluck('_id')->first();
            // Bulk Update Filtered Ledger Items and Assign To Ledger of new Booking
            $ledger_items->update([
                'statement_ledger_id' => $new_ledger_id
            ]);
        }
        // Update Booking History
        $driver_booking_history = new DriverBookingHistory();
        $driver_booking_history->driver_id = $driver->id;
        $driver_booking_history->booking_id = $new_booking_id;
        $driver_booking_history->assign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
        // if active Booking history Found
        if(isset($driver->active_booking)){
            // Make Previous History item Inactive
            $active_history_item = $driver->active_booking;
            $active_history_item->unassign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
            $active_history_item->save();
        }
        $driver_booking_history->unassign_date = null;
        $driver_booking_history->save();
        // Update Driver Booking ID
        $driver->booking_id = $new_booking_id;
        $driver->save();
        return response()->json([
            'status' => 1
        ]);
    }

    public function viewBookingHistory(int $driver_id) {
        $driver = Driver::with([
            'booking_history' => function($query) {
                $query->orderByDesc('assign_date');
            },
            'booking_history.vehicle_booking.vehicle',
        ])->findOrFail($driver_id);

        return view('Tenant.drivers.actions.viewBookingHistory', compact('driver'));
    }

    public function viewAccountStatement(int $driver_id)
    {
        $driver = Driver::findOrFail($driver_id);
        $namespace = 'booking';
        $id = $driver->booking_id;
        return view('Tenant.drivers.statement.view', compact('driver', 'namespace', 'id'));
    }


    public function deleteBookingHistoryItem($item_id) {
        $history_item = DriverBookingHistory::findOrFail($item_id);
        $history_item->delete();
        return redirect()->back()->with('message','Driver Booking History Item Deleted Successfully');
    }

    public function actionEditBookingHistory(Request $request, $item_id) {
        $request->validate([
            'save_all' => ['required', 'in:true,false'],
            'assign_date' => ['required']
        ]);
        $history_item = DriverBookingHistory::findOrFail($item_id);
        $save_unassign_date = $request->save_all === 'true';
        if($save_unassign_date && $request->has('unassign_date')){
            $history_item->unassign_date = Carbon::parse($request->unassign_date)->format('Y-m-d');
        }
        $history_item->assign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
        $history_item->save();
        return response()->json(["status" => 1]);
    }
     // Show Driver Passport Collection Details
     public function showDriverPassports()
     {
        return view('Tenant.drivers.passports.passport_management');
     }
     public function changeDriverPassportStatus(Request $request, int $id){
        $driver = Driver::with('passport_history')->findOrFail($id);
        $status = $request->get('type');
        $show_collect_date = false;
        if($status === 'return'){
            $any_unreturned = $driver->passport_history->whereNull('returned_at')->first();
            $show_collect_date = !(isset($any_unreturned));
        }
        return view('Tenant.drivers.passports.change_driver_passport_status', compact('driver', 'status', 'show_collect_date'));
     }
     public function changeDriverPassportStatusAction(Request $request, int $id){
        $driver = Driver::with('passport_history')->findOrFail($id);
        $returned = $driver->passport_history->whereNull('returned_at')->first();
        $collection_date_validator = ($request->status === 'return' && !isset($returned)) ? 'required':'nullable';
        $request->validate([
            'status' => ['required', 'in:collect,return'],
            'date' => ['required'],
            'description' => ['string', 'nullable'],
            'collection_date' => [ $collection_date_validator ]
        ]);
        // initialize new history item
        $history_item = new DriverPassport;
        $history_item->collected_at = null;
        $history_item->returned_at = null;
        $history_item->attachments = [];
        $history_item->return_description = '';
        $history_item->collect_description = '';
        //  if any relevent history item found then set to that in case of return
        if($request->status === 'return' && isset($returned)) $history_item = $returned;
        if($request->status === 'return'){
            // if any history item not found and we are returning passport then update collection date too
            if($request->status === 'return' && !isset($returned)){
                $history_item->collected_at = Carbon::parse($request->collection_date)->format('Y-m-d');
            }
            $history_item->driver_id = $driver->id;
            $history_item->returned_at = Carbon::parse($request->date)->format('Y-m-d');
            $history_item->return_description = $request->description??'';
        }else{
            $history_item->driver_id = $driver->id;
            $history_item->collected_at = Carbon::parse($request->date)->format('Y-m-d');
            $history_item->returned_at = null;
            $history_item->collect_description = $request->description??'';
        }
        $attachments = [];
        if($request->hasFile('attachment')){
            foreach($request->files->get('attachment') as $attachment){
                $path = Storage::putFile('drivers/attachments', new File($attachment->getPathname()));
                $data = [
                    'name' => $attachment->getClientOriginalName(),
                    'path' => $path
                ];
                $attachments[] = $data;
            }
            $history_item->attachments =$attachments;
        }
        $history_item->save();
        $driver->is_pasport_collected = $request->status === 'collect';
        $driver->save();
        return response()->json(["status" => 1]);
     }
     public function viewPassportHistory(int $driver_id) {
        $driver = Driver::with('passport_history')->findOrFail($driver_id);
        return view('Tenant.drivers.passports.viewPassportHistory', compact('driver'));
     }
     public function editPassportHistoryDates($id) {
        $history = DriverPassport::with('driver')->findOrFail($id);
        return view('Tenant.drivers.passports.change_driver_passport_dates', compact('history'));
     }
     public function editPassportHistoryDatesAction(Request $request, $id) {
        $request->validate([
            'collected_at' => ['required']
        ]);
        $history = DriverPassport::with('driver')->findOrFail($id);
        $history->collected_at = Carbon::parse($request->collected_at)->format('Y-m-d');
        if($request->has('returned_at')){
            $history->returned_at = Carbon::parse($request->returned_at)->format('Y-m-d');
        }
        $history->save();
        return response()->json(["status" => 1]);
     }
}
