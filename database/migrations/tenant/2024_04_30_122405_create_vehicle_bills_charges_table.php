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
        Schema::create('vehicle_bills_charges', function (Blueprint $table) {
            $table->id();
            $table->index('bill_setting_id');
            $table->index('vehicle_id');
            $table->index('driver_id');
            $table->index('date');
            $table->index('month');
            $table->index('uuid');
            $table->string('amount');
            $table->string('description');
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
        Schema::dropIfExists('vehicle_bills_charges');
    }
};
