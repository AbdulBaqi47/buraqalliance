<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use App\Traits\LogActivityTrait;

use App\Accounts\Handlers\AccountGateway;
use App\Accounts\Models\Account;
use App\Accounts\Models\Account_transaction;
use App\Traits\AutoIncreamentTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Installment extends Model
{
    use HasFactory,LogActivityTrait,AutoIncreamentTrait, SoftDeletes;


    protected $casts = [
        'charge_date' => 'date',
        'pay_date' => 'date',
    ];

    /**
     * Get the installment's source.
     */
    public function source()
    {
        return $this->morphTo(__FUNCTION__, 'source_model', 'source_id');
    }

    public function account() {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'by');
    }

}
