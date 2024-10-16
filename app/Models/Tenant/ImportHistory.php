<?php

namespace App\Models\Tenant;

use App\Helpers\NullRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class ImportHistory extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;


}
