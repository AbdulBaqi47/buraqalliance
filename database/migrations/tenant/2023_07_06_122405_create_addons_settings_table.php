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
        Schema::create('addons_settings', function (Blueprint $table) {
            $table->id();
            $table->index('title',null, null, ['unique' => true]);
            $table->unsignedBigInteger('amount');
            $table->index('source_type');
            $table->boolean('source_required')->default(false);
            $table->json('categories');
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
        Schema::dropIfExists('addons_settings');
    }
};
