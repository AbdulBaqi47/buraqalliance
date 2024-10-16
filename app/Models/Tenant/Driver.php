<?php

namespace App\Models\Tenant;

use App\Traits\{AutoIncreamentTrait, LogActivityTrait};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, AutoIncreamentTrait, LogActivityTrait, SoftDeletes;

    protected $appends=['full_name', 'missing_fields'];

    // Get all of the bookings for the Driver
    public function booking()
    {
        return $this->belongsTo(VehicleBooking::class);
    }

    public function visa_expenses()
    {
        return $this->hasMany(DriverVisaExpense::class);
    }

    /**
     * Get all of the addons for the Driver
     */
    public function addons()
    {
        return $this->hasMany(Addon::class, 'source_id')->where('source_type', 'driver');
    }

    /**
     * Get all of the booking_history for the Driver
     */
    public function booking_history()
    {
        return $this->hasMany(DriverBookingHistory::class, 'driver_id');
    }

    public function driveup_booking()
    {
        return $this->hasMany(Driveup_booking_driver::class, 'driver_id');
    }

     /**
     * Scope a query to only include perticular type of Drivers (Driver or Rider).
     */
    public function scopeIs($query, $namespace): void
    {
        if($namespace === 'all'){
            return;
        }
        $query->where('type', $namespace === 'driver' ? 'driver' : 'rider');
    }
    /**
     * Get active booking_history item for the Driver
     */
    public function getActiveBookingAttribute()
    {
        $this->loadMissing('booking_history');
        return $this->booking_history->where('status', 'active')->first();
    }

    // * Get all of the client_entities for the Driver
    public function client_entities()
    {
        return $this->hasMany(ClientEntities::class, 'source_id')->where('source_model', Driver::class);
    }

    /**
     * Get all of the sim_entities for the Driver
     */
    public function sim_entities()
    {
        return $this->hasMany(SimEntity::class, 'source_id')->where('source_model', Driver::class);
    }

    /**
     * Get all of the vehicle_entities for the Driver
     */
    public function vehicle_entities()
    {
        return $this->hasMany(VehicleEntity::class, 'source_id')->where('source_model', Driver::class);
    }
    /**
     * Get all of the passport_history for the Driver
     */
    public function passport_history()
    {
        return $this->hasMany(DriverPassport::class, 'driver_id');
    }

    /**
     * Full name of driver
     *  KL{id} {name}
     *
    */
    public function getFullNameAttribute()
    {
        # If driver has single name {no space between}
        # it will create issues in some view pages
        # since we used full_name[1] full_name[2] to fetch first 2 letters of name

        $tmpName = $this->name;
        if(!str_contains($tmpName, ' ')){
            $tmpName .= " ";
        }

        return "KL$this->id $tmpName";
    }

    /**
     * Full name of driver
     *  KL{id} {name}
     *
    */
    public function getMissingFieldsAttribute()
    {
        $fields = [];

        # License
        if(!isset($this->liscence_number)){
            $fields[] = [
                'addon' => 'license',
                'tag' => 'license_number',
                'text' => 'License Number',
                'restricted' => $this->is_license_skipped === true
            ];
        }
        if(!isset($this->liscence_pictures['front'])){
            $fields[] = [
                'addon' => 'license',
                'tag' => 'license_frontimg',
                'text' => 'License Front Image',
                'restricted' => $this->is_license_skipped === true
            ];
        }
        if(!isset($this->liscence_pictures['back'])){
            $fields[] = [
                'addon' => 'license',
                'tag' => 'license_backimg',
                'text' => 'License Back Image',
                'restricted' => $this->is_license_skipped === true
            ];
        }

        # VISA
        if(!isset($this->emirates_id_no)){
            $fields[] = [
                'addon' => 'visa',
                'tag' => 'emirates_id',
                'text' => 'Emirates ID No',
                'restricted' => $this->is_visa_skipped === true
            ];

        }
        if(!isset($this->emirates_id_pictures['front'])){
            $fields[] = [
                'addon' => 'visa',
                'tag' => 'emirates_frontimg',
                'text' => 'Emirates ID Front Image',
                'restricted' => $this->is_visa_skipped === true
            ];

        }
        if(!isset($this->emirates_id_pictures['back'])){
            $fields[] = [
                'addon' => 'visa',
                'tag' => 'emirates_backimg',
                'text' => 'Emirates ID Back Image',
                'restricted' => $this->is_visa_skipped === true
            ];

        }
        if(!isset($this->visa_pictures['front'])){
            $fields[] = [
                'addon' => 'visa',
                'tag' => 'visa_img',
                'text' => 'Visa Picture',
                'restricted' => $this->is_visa_skipped === true
            ];

        }
        if(!isset($this->emirates_id_expiry)){
            $fields[] = [
                'addon' => 'visa',
                'tag' => 'emirates_expiry',
                'text' => 'Emirates ID Expiry',
                'restricted' => $this->is_visa_skipped === true
            ];

        }
        if(!isset($this->visa_expiry)){
            $fields[] = [
                'addon' => 'visa',
                'tag' => 'visa_expiry',
                'text' => 'Visa Expiry',
                'restricted' => $this->is_visa_skipped === true
            ];

        }

        # RTA
        if(!isset($this->rta_permit_number)){
            $fields[] = [
                'addon' => 'rta',
                'tag' => 'rta_number',
                'text' => 'RTA Permit Number',
                'restricted' => $this->is_rta_skipped === true
            ];
        }
        if(!isset($this->rta_permit_pictures['front'])){
            $fields[] = [
                'addon' => 'rta',
                'tag' => 'rta_frontimg',
                'text' => 'RTA Permit Front Image',
                'restricted' => $this->is_rta_skipped === true
            ];
        }
        if(!isset($this->rta_permit_pictures['back'])){
            $fields[] = [
                'addon' => 'rta',
                'tag' => 'rta_backimg',
                'text' => 'RTA Permit Back Image',
                'restricted' => $this->is_rta_skipped === true
            ];
        }
        return $fields;
    }


    /**
     * Validate Driver & Check driver is completed
     * i.e. documents are uploaded
     *
    */
    public function isDocumentsUploaded() : bool {
        $valid = true;

        # ---------
        # Visa Docs
        # ---------
        if($valid && $this->has_visa === true){
            if(
                # Emirates ID
                !isset($this->emirates_id_no) ||
                !isset($this->emirates_id_expiry) ||
                !isset($this->emirates_id_pictures) ||

                # Visa
                !isset($this->visa_expiry) ||
                !isset($this->visa_pictures)
            ){
                $valid = false;
            }
        }

        # -------------
        # License Docs
        # -------------
        if($valid && $this->has_license === true){
            if(
                !isset($this->liscence_number) ||
                !isset($this->liscence_expiry) ||
                !isset($this->liscence_pictures)
            ){
                $valid = false;
            }
        }

        # -------------
        # RTA Docs
        # -------------
        if($valid && $this->has_rta === true){
            if(
                !isset($this->rta_permit_number) ||
                !isset($this->rta_permit_expiry) ||
                !isset($this->rta_permit_pictures)
            ){
                $valid = false;
            }
        }




        return $valid;
    }

}
