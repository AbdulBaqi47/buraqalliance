<?php

namespace App\Models\Tenant;

use App\Accounts\Traits\TableRelationTrait;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class StatementLedgerItem extends Model
{
    use HasFactory, LogActivityTrait, TableRelationTrait, SoftDeletes;

    protected $fillable = [
        'statement_ledger_id', 'title', 'description', 'type', 'date', 'amount', 'month', 'tag', 'attachment', 'driver_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];


    public function statement_ledger(){
        return $this->belongsTo(StatementLedger::class, 'statement_ledger_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    // Morph inverse relationship to the Ledger model.
    public function ledger() {
        return $this->morphOne(Ledger::class, 'source', 'source_model');
    }
}
