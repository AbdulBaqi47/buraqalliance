<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_passports', function (Blueprint $table) {
            $table->id();
            $table->index('driver_id');
            $table->index('collected_at');
            $table->string('returned_at');
            $table->string('collect_description');
            $table->string('return_description');
            $table->json('attachments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_passports');
    }
};
