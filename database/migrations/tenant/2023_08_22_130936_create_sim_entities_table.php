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
        Schema::create('sim_entities', function (Blueprint $table) {
            $table->index('sim_id');
            $table->index('source_model');
            $table->index('source_id');
            $table->index('assign_date');
            $table->index('unassign_date');
            $table->index('contract_end_date');
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
        Schema::dropIfExists('sim_entities');
    }
};
