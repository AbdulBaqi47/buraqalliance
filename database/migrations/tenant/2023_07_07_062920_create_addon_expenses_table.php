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
        Schema::create('addon_expenses', function (Blueprint $table) {
            $table->id();
            $table->index('addon_id');
            $table->index('account_id');
            $table->index('type');
            $table->unsignedBigInteger('amount');
            $table->index('charge_amount');
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
        Schema::dropIfExists('addon_expenses');
    }
};
