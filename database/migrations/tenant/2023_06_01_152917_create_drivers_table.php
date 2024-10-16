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
        Schema::create('drivers', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
            $table->index('location');
            $table->index(
                'email',
                null,
                null,
                [
                    'sparse' => true,
                    'unique' => true,
                ]
            );
            $table->string('phone_number');
            $table->string('liscence_number')->nullable();
            $table->json('liscence_pictures')->nullable();
            $table->string('emirates_id_no')->nullable();
            $table->json('emirates_id_pictures')->nullable();
            $table->json('visa_pictures')->nullable();
            $table->string('passport_number');
            $table->json('passport_pictures');
            $table->string('profile_picture');
            $table->dateTime('passport_expiry');
            $table->dateTime('emirates_id_expiry')->nullable();
            $table->dateTime('visa_expiry')->nullable();
            $table->string('nationality');
            $table->boolean('is_pasport_collected')->default(false);
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
        Schema::dropIfExists('drivers');
    }
};
