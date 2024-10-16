<?php

namespace App\Accounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

use App\Accounts\Traits\AccountLogsTrait;

class Account_relation extends Model
{
    use HasFactory,AccountLogsTrait;

    public function transaction()
    {
        return $this->belongsTo('App\Accounts\Models\Account_transaction', 'transaction_id');
    }

    /**
     * Get the transaction relation's source.
     */
    public function source()
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_id');
    }
}
