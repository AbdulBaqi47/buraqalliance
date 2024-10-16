<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use App\Traits\AutoIncreamentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Invoice extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes;

    protected $appends = ['display_name'];


    /**
     * Get the client attached to this invoice
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the items attached to this invoice
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    /**
     * Get the payment refs attached to this invoice
     */
    public function payment_refs()
    {
        return $this->belongsToMany(TransactionLedger::class);
    }

    /**
     * Get the invoice name.
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn () => '#' . $this->id,
        );
    }

    /**
     * Get the invoice payments.
     */
    protected function payments(): Attribute
    {
        return Attribute::make(
            get: function(){
                $this->loadMissing([
                    'payment_refs.payables.user'
                ]);

                $payments = collect([]);
                if(isset($this->payment_refs) && count($this->payment_refs) > 0){
                    $payments = $this
                    ->payment_refs
                    ->pluck('payables')
                    ->flatten()
                    ->values()
                    ->map(function($item){
                        $item->by = $item->user->name;
                        return $item;
                    })
                    ->toArray();


                    $payments = collect($payments)->select([
                        'id',
                        'title',
                        'status',
                        'amount',
                        'time',
                        'by',
                        'real_amount'
                    ]);
                }

                return $payments;
            }
        );
    }
}
