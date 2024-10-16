<?php

namespace App\Accounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;

use App\Accounts\Traits\AccountLogsTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, AccountLogsTrait, SoftDeletes;

    public function transactions()
    {
        return $this->hasMany('App\Accounts\Models\Account_transaction', 'account_id');
    }

    public function user_access()
    {
        return $this->hasMany('App\Accounts\Models\Account_access', 'account_id');
    }



    /**
     * create/append the balance attribute
     *
     *
    */
    protected $appends=['balance'];
    public function getBalanceAttribute()
    {
        # calculate balance of this account

        # FOR NOW: Render balance only on accounts where "status" col found
        # Since this attribute rendering is bad approach and initiate N+1 queries
        # but we cannot remove this since it will be many changes
        if(!isset($this->status))return 0;

        return \App\Accounts\Handlers\AccountGateway::getAccountBalance($this);
    }
}
