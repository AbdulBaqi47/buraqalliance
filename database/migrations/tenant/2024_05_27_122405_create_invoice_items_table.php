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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->index('invoice_id');
            $table->index('description');
            $table->integer('rate');
            $table->integer('qty');
            
            $table->integer('subtotal');

            $table->integer('tax_value');
            $table->integer('tax_type');
            $table->integer('tax_amount');
            
            $table->integer('total');

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
        Schema::dropIfExists('invoice_items');
    }
};
