<?php

namespace App\Models\Tenant;

use App\Helpers\NullRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use App\Traits\AutoIncreamentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Addon extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes;

    protected $appends=['readable_details'];

    /**
     * Get the addon_setting that owns the Addon
     */
    public function setting()
    {
        return $this->belongsTo(AddonsSetting::class, 'setting_id');
    }

    public function link()
    {
        return $this->morphTo(__FUNCTION__, 'source_model', 'source_id');
    }

    /**
     * Get the driver that owns the Addon
     */
    public function driver()
    {
        // if($this->source_type !== "driver" || !isset($this->source_id)){
        //     return new NullRelation();
        // }

        return $this->belongsTo(Driver::class, 'source_id');
    }

    /**
     * Get the vehicle that owns the Addon
     */
    public function vehicle()
    {
        // if($this->source_type !== "vehicle" || !isset($this->source_id)){
        //     return new NullRelation();
        // }
        return $this->belongsTo(Vehicle::class, 'source_id');
    }

    /**
     * Get the vehicle that owns the Addon
     */
    public function booking()
    {
        // if($this->source_type !== "vehicle" || !isset($this->source_id)){
        //     return new NullRelation();
        // }
        return $this->belongsTo(VehicleBooking::class, 'source_id');
    }

    /**
     * Get all of the deductions for the Addon
     */
    public function deductions()
    {
        return $this->hasMany(AddonDeduction::class, 'addon_id');
    }

    /**
     * Get all of the expenses for the Addon
     */
    public function expenses()
    {
        return $this->hasMany(AddonExpense::class);
    }


    /**
     * Breakdown of a addon
     *
     *
    */

    public function getBreakdownAttribute(){
        $this->loadMissing([
            'expenses' => function($query){
                $query->select('addon_id','given_date','amount', 'charge_amount', 'type', 'description','date');
            },
            'deductions' => function($query){
                $query->select('addon_id', 'date', 'amount','date');
            },
        ]);
        $total_price = $this->price + $this->expenses->whereNotNull('charge_amount')->sum('charge_amount');
        $total_expenses = $this->expenses->whereNotNull('amount')->sum('amount');
        $total_deductions = $this->deductions->whereNotNull('amount')->sum('amount');
        return (object)[
            'total_price' => $total_price,

            'expenses' => $this->expenses,
            'deductions'=> $this->deductions,

            'total_expenses' => $total_expenses,
            'total_deductions' => $total_deductions,

            'remaining' => $total_price - $total_deductions,

            'profit' => $this->price - $total_expenses
        ];
    }



    /**
     * Readable text of additional fields
     *
     *
    */

    public function getReadableDetailsAttribute(){

        if(isset($this->additional_details)){
            $textArr = [];
            foreach ($this->additional_details as $key => $value) {
                if(is_array($value)) $value = implode(" / ", $value);
                $textArr[] = Str::title(str_replace('-', ' ', $key)) . ': <span class="kt-font-bold">'.$value.'</span>';
            }

            return implode('<br />', $textArr);
        }

        return '';
    }
}
