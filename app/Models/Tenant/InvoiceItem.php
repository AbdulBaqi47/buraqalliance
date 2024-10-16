<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

    /**
     * Get the invoice attached to this item
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

}
