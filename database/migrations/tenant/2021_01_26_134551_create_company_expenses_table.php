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
        Schema::create('company_expenses', function (Blueprint $table) {
            $table->id();
            $table->date('given_date');
            $table->date('month');
            $table->string('type');
            $table->text('description')->nullable();
            $table->integer('amount');
            $table->boolean('has_tax');
            $table->integer('tax_amount')->nullable();
            $table->integer('tax_id')->nullable();
            $table->text('tax_img')->nullable();
            $table->integer('by');
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
        Schema::dropIfExists('company_expenses');
    }
};
