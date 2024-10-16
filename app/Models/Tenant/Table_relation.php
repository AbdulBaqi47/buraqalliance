<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Table_relation extends Model
{
    use HasFactory, SoftDeletes;

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }
    protected $with = ['source']; # So tranaction key is available on every invoice object
    public function source(){
        return $this->morphTo(__FUNCTION__, 'source_model', 'source_id');
    }
}
