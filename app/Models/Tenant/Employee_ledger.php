<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Employee_ledger extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

    protected $casts = [
        'month' => 'datetime',
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Tenant\User', 'user_id');
    }
}
