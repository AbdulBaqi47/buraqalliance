<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class StatementLedgerItemGroup extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

}
