<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use App\Traits\AutoIncreamentTrait;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes;


    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
