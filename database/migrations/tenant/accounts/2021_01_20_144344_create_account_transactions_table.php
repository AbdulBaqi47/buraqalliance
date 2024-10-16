<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->index('account_id');
            $table->string('type');
            $table->timestamp('time');
            $table->string('title');
            $table->text('description')->nullable();
            $table->index('tag');
            $table->index('amount');
            $table->index('real_amount');
            $table->string('bank_transaction_id')->nullable();
            $table->integer('transaction_by');
            $table->json('additional_details');
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
        Schema::dropIfExists('account_transactions');
    }
}
