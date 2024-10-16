<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Log extends Model
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
