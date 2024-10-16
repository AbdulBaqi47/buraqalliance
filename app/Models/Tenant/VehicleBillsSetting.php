<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class VehicleBillsSetting extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

}
