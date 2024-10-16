<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class AddonDeduction extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

    /**
     * Get the driver_addon that owns the AddonDeduction
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class, 'addon_id');
    }
}
