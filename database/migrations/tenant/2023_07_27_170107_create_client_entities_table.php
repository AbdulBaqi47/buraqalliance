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
        Schema::create('client_entities', function (Blueprint $table) {
            $table->index('client_id');

            $table->index('source_id');
            $table->string('source_model');

            $table->index(
                'refid',
                null,
                null,
                [
                    'sparse' => true,
                ]
            );

            $table->date('assign_date');
            $table->date('unassign_date')->nullable(); # If this is null, means entity is not history

            $table->text('notes')->nullable();
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
        Schema::dropIfExists('client_entities');
    }
};
