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
        Schema::create('vehicle_bills_details', function (Blueprint $table) {
            $table->id();
            $table->index('bill_setting_id');
            $table->index('vehicle_id');
            $table->index('date');
            $table->index('month');
            $table->index('uuid');
            $table->index('ref'); // Unique REF which handles duplications
            $table->string('charge_amount');
            $table->string('spend_amount');
            $table->string('description');
            $table->json('raw'); // RAW columns in key-pair values
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
        Schema::dropIfExists('vehicle_bills_details');
    }
};
