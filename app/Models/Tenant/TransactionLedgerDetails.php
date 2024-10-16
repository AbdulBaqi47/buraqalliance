<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogActivityTrait;

use App\Accounts\Handlers\AccountGateway;
use App\Accounts\Traits\TableRelationTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class TransactionLedgerDetails extends Model
{
    use HasFactory, LogActivityTrait, TableRelationTrait, SoftDeletes;

    public function transaction_ledger()
    {
        return $this->belongsTo(TransactionLedger::class, 'tl_id');
    }

    /**
     * Get the TransactionLedgerDetails's source.
     */
    public function source()
    {
        return $this->morphTo(__FUNCTION__, 'source_model', 'source_id');
    }


}
