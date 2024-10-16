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
        Schema::create('vehicle_bills_settings', function (Blueprint $table) {
            $table->id();
            $table->index('title',null, null, ['unique' => true]);
            $table->index('grouped'); // Boolean
            $table->index('charged_is_spend'); // Boolean - If the spend amount is same as charged
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
        Schema::dropIfExists('vehicle_bills_settings');
    }
};
