<?php

namespace App\Models\Tenant;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogActivityTrait;

class Employee_route extends Model
{
    use HasFactory, LogActivityTrait;

    public function employee()
    {
        return $this->belongsTo('App\Models\Tenant\User', 'employee_id');
    }
}
