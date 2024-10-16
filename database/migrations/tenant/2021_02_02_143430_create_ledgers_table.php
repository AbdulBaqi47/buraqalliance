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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->index('type'); #cr,dr
            $table->index('source_id');
            $table->index('source_model');
            $table->index('date');
            $table->index(
                'month',
                null,
                null,
                [
                    'sparse' => true,
                    'unique' => false,
                ]
            );
            $table->index('tag');
            $table->index('is_cash');
            $table->integer('amount');
            $table->json('props'); # for extra data
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
        Schema::dropIfExists('ledgers');
    }
};
