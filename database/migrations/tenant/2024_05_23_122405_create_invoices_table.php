<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->index('client_id');
            $table->index('month');
            $table->index('date');
            $table->index('due_date');
            
            $table->integer('subtotal');
            
            $table->integer('discount_value');
            $table->string('discount_type');
            $table->integer('discount_amount');
            
            $table->integer('total');
            
            $table->index('status');

            $table->string('internal_notes');
            $table->string('invoice_notes');

            $table->index('by');
            
            $table->index('transaction_ledger_ids'); // Array of ids of payment refs

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
