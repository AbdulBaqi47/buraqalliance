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
        Schema::create('employee_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('type'); #cr,dr
            $table->string('tag');
            $table->string('title');
            $table->text('description');
            $table->date('month');
            $table->date('date');
            $table->string('user_id');
            $table->integer('amount');
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
        Schema::dropIfExists('employee_ledgers');
    }
};
