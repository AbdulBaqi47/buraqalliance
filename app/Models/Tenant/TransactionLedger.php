<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use App\Traits\LogActivityTrait;

use App\Accounts\Handlers\AccountGateway;
use App\Accounts\Models\Account_transaction;
use App\Traits\AutoIncreamentTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class TransactionLedger extends Model
{
    use HasFactory,LogActivityTrait,AutoIncreamentTrait, SoftDeletes;

    public function chargeables()
    {
        return $this->hasMany(TransactionLedgerDetails::class, 'tl_id');
    }

    public function payables()
    {
        return $this->hasMany(Account_transaction::class, 'additional_details.tl_id')->whereIn('tag', ['transaction_ledger', 'client_income', 'sim_bill']);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class);
    }

}
