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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('vehicle_type_id');

            $table->index(
                'chassis_number',
                null,
                null,
                [
                    'sparse' => true,
                    'unique' => true,
                ]
            );
            $table->index(
                'engine_number',
                null,
                null,
                [
                    'sparse' => true,
                    'unique' => true,
                ]
            );
            $table->string("model");
            $table->string("year");
            $table->string("color");
            $table->string("state");
            $table->json("mulkiya_pictures");
            $table->date("mulkiya_expiry");
            $table->date("insurance_expiry");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('chassis_number');
            $table->dropColumn('engine_number');
            $table->dropColumn('model');
            $table->dropColumn('year');
            $table->dropColumn('color');
            $table->dropColumn('mulkiya_pictures');
            $table->dropColumn('mulkiya_expiry');
            $table->dropColumn('insurance_expiry');
        });
    }
};
