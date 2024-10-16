<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\AutoIncreamentTrait;
use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Ledger extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes;

    protected $casts = [
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Tenant\User', 'props.by');
    }

    public function relations()
    {
        return $this->hasMany('App\Models\Tenant\Table_relation', 'ledger_id');
    }

    /**
     * Get the ledger's source.
     */
    public function source()
    {
        return $this->morphTo(__FUNCTION__, 'source_model', 'source_id');
    }


}
