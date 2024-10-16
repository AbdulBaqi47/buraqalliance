<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class InvoicePayment extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function by()
    {
        return $this->belongsTo(User::class, 'props.by' );
    }
}
