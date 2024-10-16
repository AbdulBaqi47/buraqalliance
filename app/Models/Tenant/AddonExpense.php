<?php

namespace App\Models\Tenant;

use App\Accounts\Models\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class AddonExpense extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

    /**
     * Get the Addon that owns the AddonExpense
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class, 'addon_id');
    }

    /**
     * Get the Account that owns the AddonExpense
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
