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
        Schema::create('table_relations', function (Blueprint $table) {
            $table->id();
            $table->index('ledger_id');
            $table->string('source_id');
            $table->string('source_model');
            $table->string('tag');
            $table->boolean('is_real');
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
        Schema::dropIfExists('table_relations');
    }
};
