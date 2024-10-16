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
        Schema::create('statement_ledger_items', function (Blueprint $table) {
            $table->index('statement_ledger_id');
            $table->string('title');
            $table->text('description');
            $table->index('tag');
            $table->index('type');
            $table->index('date');
            $table->index('month');
            $table->string('amount');
            $table->text('attachment')->nullable();
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
        Schema::dropIfExists('statement_ledger_items');
    }
};
