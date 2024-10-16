<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Client;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\InvoiceItem;
use App\Models\Tenant\TransactionLedger;
use Illuminate\Http\Request;
use App\Models\Tenant\VehicleBillsSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:tenant_auth');
    }

    #-------------------------
    #     BASE METHODS
    #-------------------------
    
    /**
     * View Page
     *
    */
    public function viewInvoices()
    {
        return view('Tenant.invoice.view');
    }

    /**
     * Create Page of invoice
     *
    */
    public function showInvoiceForm($config=null)
    {

        $invoice_id = isset($config) ? $config->invoice->id : null;
        // Fetch all payment reference
        $payment_refs = TransactionLedger::where('tag', 'income')
        ->select(
            'id',
            'title',
            'amount',
            'invoices_ids'
        )
        // Payments of edited invoice or no invoice attached
        ->whereDoesntHave('invoices', fn($query) => $query->where('id', '!=', $invoice_id) )
        ->get();

        $clients = Client::select('id', 'name', 'source')->is('aggregator')->get();

        return view('Tenant.invoice.create', compact('config', 'clients', 'payment_refs'));
    }

    /**
     * POST request of creating the invoice
     *
    */
    public function create_invoice(Request $request)
    {
        $request->validate([
            'client_id' => 'required|max:255',
            'month' => 'required|max:255',
            'date' => 'required|max:255',
            'due_date' => 'required|max:255',
            'items' => 'required|array',
        ]);

        # ----------------
        #     Payload
        # ----------------

        $payment_ref_ids =  isset($request->payment_ref_ids) ? collect($request->payment_ref_ids)->map(fn($id) =>(int)$id)->values()->toArray() : [];
        $client_id = (int)$request->client_id;
        $invoice_notes = $request->invoice_notes;
        $internal_notes = $request->internal_notes;
        $month = Carbon::parse($request->month)->format('Y-m-d');
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $due_date = Carbon::parse($request->due_date)->format('Y-m-d');
        $items = $request->items;
        $subtotal = (float)$request->subtotal;
        $total = (float)$request->total;
        $discount = [
            'discount_value'=>isset($request->discount_value)?(float)$request->discount_value:0,
            'discount_type'=>$request->discount_type,
            'discount_amount'=>(float)$request->discount_amount,
        ];

        #current time of client
        $localUtcOffset = request()->cookie('localUtcOffset');
        if(isset($localUtcOffset))$localUtcOffset=request()->cookie('localUtcOffset');
        else $localUtcOffset=000;
        $time = Carbon::now()->utcOffset($localUtcOffset)->toAtomString();

        # --------------------
        #   Creating Invoice
        # --------------------
        $invoice = new Invoice;
        $invoice->client_id = $client_id;
        $invoice->month = $month;
        $invoice->date = $date;
        $invoice->due_date = $due_date;
        
        $invoice->subtotal = $subtotal;
        
        $invoice->discount_value = $discount['discount_value'];
        $invoice->discount_type = $discount['discount_type'];
        $invoice->discount_amount = $discount['discount_amount'];
    
        $invoice->total = $total;

        $invoice->invoice_notes = $invoice_notes;
        $invoice->internal_notes = $internal_notes;

        $invoice->by = Auth::user()->id;

        $invoice->save();

        foreach ($items as $item) {
            $item=(object)$item;

            $item_subtotal = round($item->rate * $item->qty, 2);
            $item_tax_value = (float) $item->tax_value;
            $item_tax_type = 'percentage'; // maybe we give option for "fixed" or "percentage"
            $item_tax_amount = 0;
            if($item_tax_value > 0){
                if($item_tax_type === 'percentage') $item_tax_amount = ($item_tax_value * $item_subtotal) / 100;
                else $item_tax_amount = $item_tax_value;
            }
            if($item->qty>0 && $item->rate>0 && isset($item->description)){

                // -----------------
                // Add Invoice Item
                // -----------------
                $invoiceItem = new InvoiceItem;
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->description = $item->description;
                $invoiceItem->rate = (float)$item->rate;
                $invoiceItem->qty = (float)$item->qty;
                $invoiceItem->subtotal = $item_subtotal;
                $invoiceItem->tax_value = $item_tax_value;
                $invoiceItem->tax_type = $item_tax_type;
                $invoiceItem->tax_amount = $item_tax_amount;
                $invoiceItem->total = $item_subtotal + $item_tax_amount;
                $invoiceItem->save();

            }
        }

        # ----------------
        #   Attach Refs 
        # ----------------
        if(count($payment_ref_ids) > 0){
            $invoice->payment_refs()->attach($payment_ref_ids);
        }

        return response()->json([
            'status' => 1,
            'invoice_id' => $invoice->display_name
        ]);

    }

    public function getRelatedInvoices($client_id, Request $request) 
    {
        $invoice_id=null;

        # Check if request has invoice_id, we need to exclude current invoice
        if($request->has('invoice_id')){
            $invoice_id = (int)$request->invoice_id;
        }
        $client_id = (int)$client_id;

        $invoices = Invoice::with([
            'client' => fn($query) => $query->select('id', 'name')
        ])
        ->where('client_id', $client_id)
        ->where('id', '!=', $invoice_id) # Exclude current job
        ->orderByDesc('updated_at')
        ->select(
            'id',
            'client_id',
            'month',
            'total'
        )
        ->limit(5) // recent n invoice
        ->get();

        return response()->json($invoices);
        
    }

    public function showEditInvoiceForm($id) 
    {

        $invoice = Invoice::with([
            'client',
            'items',
            'payment_refs.payables'
        ])
        ->findOrFail((int)$id)
        ->append('payments');

        $invoice->actions=[
            'status'=>1,
        ];

        # Call the load function
        return $this->showInvoiceForm((object)[
            'invoice'=>$invoice,
            'action'=>'edit'
        ]);
        
    }

    /**
     * POST request of editing the invoice
     *
    */
    public function edit_invoice(Request $request)
    {
        // Before validation, cast the integer values, otherwise validation may not work as expected
        $request->merge(['invoice_id' => (int)$request->invoice_id]);


        $request->validate([
            'invoice_id' => 'required|integer|exists:invoices,id', 
            'client_id' => 'required|max:255',
            'month' => 'required|max:255',
            'date' => 'required|max:255',
            'due_date' => 'required|max:255',
            'items' => 'required|array',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        
        // return $request->all();

        # ----------------
        #     Payload
        # ----------------

        $payment_ref_ids =  isset($request->payment_ref_ids) ? collect($request->payment_ref_ids)->map(fn($id) =>(int)$id)->values()->toArray() : [];
        $client_id = (int)$request->client_id;
        $invoice_notes = $request->invoice_notes;
        $internal_notes = $request->internal_notes;
        $month = Carbon::parse($request->month)->format('Y-m-d');
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $due_date = Carbon::parse($request->due_date)->format('Y-m-d');
        $items = $request->items;
        $subtotal = (float)$request->subtotal;
        $total = (float)$request->total;
        $discount = [
            'discount_value'=>isset($request->discount_value)?(float)$request->discount_value:0,
            'discount_type'=>$request->discount_type,
            'discount_amount'=>(float)$request->discount_amount,
        ];

        #current time of client
        $localUtcOffset = request()->cookie('localUtcOffset');
        if(isset($localUtcOffset))$localUtcOffset=request()->cookie('localUtcOffset');
        else $localUtcOffset=000;
        $time = Carbon::now()->utcOffset($localUtcOffset)->toAtomString();

        # --------------------
        #   Creating Invoice
        # --------------------
        $invoice->client_id = $client_id;
        $invoice->month = $month;
        $invoice->date = $date;
        $invoice->due_date = $due_date;
        
        $invoice->subtotal = $subtotal;
        
        $invoice->discount_value = $discount['discount_value'];
        $invoice->discount_type = $discount['discount_type'];
        $invoice->discount_amount = $discount['discount_amount'];
    
        $invoice->total = $total;

        $invoice->invoice_notes = $invoice_notes;
        $invoice->internal_notes = $internal_notes;

        $invoice->update();

        $itemIdsUpdated = [];

        foreach ($items as $item) {
            $item=(object)$item;

            $item_subtotal = round($item->rate * $item->qty, 2);
            $item_tax_value = (float) $item->tax_value;
            $item_tax_type = 'percentage'; // maybe we give option for "fixed" or "percentage"
            $item_tax_amount = 0;
            if($item_tax_value > 0){
                if($item_tax_type === 'percentage') $item_tax_amount = ($item_tax_value * $item_subtotal) / 100;
                else $item_tax_amount = $item_tax_value;
            }
            if($item->qty>0 && $item->rate>0 && isset($item->description)){

                // -----------------
                // Add Invoice Item
                // -----------------
                $invoiceItem = new InvoiceItem;
                if(isset($item->id)){
                    $invoiceItem = InvoiceItem::findOrFail($item->id);
                }
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->description = $item->description;
                $invoiceItem->rate = (float)$item->rate;
                $invoiceItem->qty = (float)$item->qty;
                $invoiceItem->subtotal = $item_subtotal;
                $invoiceItem->tax_value = $item_tax_value;
                $invoiceItem->tax_type = $item_tax_type;
                $invoiceItem->tax_amount = $item_tax_amount;
                $invoiceItem->total = $item_subtotal + $item_tax_amount;
                $invoiceItem->save();

                
                # Append id to skip this from deleting
                $itemIdsUpdated[] = $invoiceItem->id;

            }
        }

        // Remove invoice items that are not in payload
        $invoice->items()->whereNotIn('_id', $itemIdsUpdated)->delete();

        # ----------------
        #   Attach Refs 
        # ----------------

        $invoice->payment_refs()->sync($payment_ref_ids);

        return response()->json([
            'status' => 2,
            'invoice_id' => $invoice->display_name
        ]);

    }

    public function delete_invoice(int $id) 
    {

        $invoice = Invoice::findOrFail($id);

        # Unlink payment refs
        $invoice->payment_refs()->sync([]);

        # Delete the invoice along with items
        $invoice->items()->delete();
        $invoice->delete();


        return response()->json([
            'status' => 1
        ]);
        
    }


}
