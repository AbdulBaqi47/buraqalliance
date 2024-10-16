<?php

namespace App\Accounts\Models;

use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;

use App\Accounts\Traits\AccountLogsTrait;
use App\Traits\AutoIncreamentTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Account_transaction extends Model
{
    use HasFactory,AccountLogsTrait, AutoIncreamentTrait, SoftDeletes;

    public function account()
    {
        return $this->belongsTo('App\Accounts\Models\Account', 'account_id');
    }

    public function links()
    {
        return $this->hasMany('App\Accounts\Models\Account_relation', 'transaction_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'transaction_by', '_id');
    }


}
