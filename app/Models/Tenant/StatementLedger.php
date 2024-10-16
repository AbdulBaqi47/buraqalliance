<?php

namespace App\Models\Tenant;

use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class StatementLedger extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

    protected $fillable = [
        'linked_to', 'linked_id'
    ];


    public function booking()
    {
        return $this->belongsTo(VehicleBooking::class, 'vehicle_booking_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\Tenant\StatementLedgerItem', 'statement_ledger_id');
    }

    public function addItem(object $item)
    {
        $ledger_item = new StatementLedgerItem;
        $ledger_item->statement_ledger_id = $this->_id;
        $ledger_item->title = $item->title;
        $ledger_item->description = $item->description??"";
        $ledger_item->type = $item->type;
        $ledger_item->tag = $item->tag;
        $ledger_item->date = $item->date;
        $ledger_item->month = $item->month;
        $ledger_item->amount = $item->amount;
        $ledger_item->attachment = $item->attachment??null;
        $ledger_item->channel = $item->channel??"app";
        $ledger_item->group = $item->group??null;
        $ledger_item->additional_details = $item->additional_details??null;

        $ledger_item->save();

        $this->items = [$ledger_item];

        return $ledger_item;
    }

    public function updateItem($itemId, object $item)
    {
        $ledger_item = StatementLedgerItem::findOrFail($itemId);
        if(isset($item->statement_ledger_id)){
            $ledger_item->statement_ledger_id = $item->statement_ledger_id;
        }
        if(isset($item->title)) $ledger_item->title = $item->title;
        if(isset($item->description)) $ledger_item->description = $item->description??"";
        if(isset($item->type)) $ledger_item->type = $item->type;
        if(isset($item->tag)) $ledger_item->tag = $item->tag;
        if(isset($item->date)) $ledger_item->date = $item->date;
        if(isset($item->month)) $ledger_item->month = $item->month;
        if(isset($item->amount)) $ledger_item->amount = $item->amount;
        if(array_key_exists('attachment', (array)$item)) $ledger_item->attachment = $item->attachment??null;
        if(isset($item->channel)) $ledger_item->channel = $item->channel??"app";

        $ledger_item->update();

        $this->items = [$ledger_item];

        return $ledger_item;
    }

    /**
     * Scope a query to only include perticular namespace.
     */
    public function scopeForNamespace($query, $namespace): void
    {
        switch ($namespace) {
            case 'company':
                $query->where('linked_to', 'company');
                break;
            case 'driver':
                $query->where('linked_to', 'driver');
                break;

            default:
                break;
        }
    }

    /**
     * Scope a query to only include id perticular namespace.
     */
    public function scopeOfNamespace($query, $namespace, $id): void
    {
        switch ($namespace) {
            case 'company':
                $query->where('linked_to', 'company')->where('linked_id', (int)$id);
                break;
            case 'driver':
                $query->where('linked_to', 'driver')->where('linked_id', (int)$id);
                break;

            default:
                break;
        }
    }

    /**
     * This will get $this, if not found, will create it
     *
     * @param string $linked_to driver or company
     * @param string $linked_id driver_id
    */
    public static function getLedger(string $linked_to, int $linked_id)
    {
        $ledger = self::where('linked_to', $linked_to)->where('linked_id', $linked_id)->limit(1)->first();
        if(isset($ledger))return $ledger;
        else{
            $ledger = new self;
            $ledger->linked_to = $linked_to;
            $ledger->linked_id = $linked_id;
            $ledger->save();
        }

        return $ledger;

    }
}
