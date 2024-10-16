<?php

namespace App\Models\Tenant;

use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class DriverPassport extends Model
{
    use HasFactory,LogActivityTrait, SoftDeletes;

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
