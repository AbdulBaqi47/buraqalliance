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
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->index('setting_id');
            $table->date('date');
            $table->integer('price');
            $table->integer('cost');
            $table->index('source_type'); // driver/vehicle
            $table->index('source_id')->default(null);
            $table->index('source_model');
            $table->index('status');
            $table->index('current_stage');
            $table->index('payment_status');
            $table->index('override_types');
            $table->json('additional_details')->nullable();
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
        Schema::dropIfExists('addons');
    }
};
