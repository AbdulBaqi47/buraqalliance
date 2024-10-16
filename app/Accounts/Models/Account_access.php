<?php

namespace App\Accounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;
use App\Accounts\Traits\AccountLogsTrait;

class Account_access extends Model
{
    use HasFactory, AccountLogsTrait;

    public function user()
    {
        return $this->belongsTo('App\Models\Tenant\User', 'user_id');
    }

    public function account()
    {
        return $this->belongsTo('App\Accounts\Models\Account', 'account_id');
    }
}
