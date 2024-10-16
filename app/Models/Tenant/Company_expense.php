<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Company_expense extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;


}
