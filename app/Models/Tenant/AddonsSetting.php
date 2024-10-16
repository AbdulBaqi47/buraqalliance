<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class AddonsSetting extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

    /**
     * Get all of the addons for the AddonsSetting
     */
    public function addons()
    {
        return $this->hasMany(Addon::class);
    }
}
