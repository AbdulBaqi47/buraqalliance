<?php

namespace App\Accounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;

class Account_log extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id', 'subject_model', 'causer_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Tenant\User', 'causer_id');
    }
}
