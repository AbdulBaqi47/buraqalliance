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
        Schema::create('vehicle_entities', function (Blueprint $table) {
            $table->id();
            $table->index('vehicle_id');
            $table->index('source_model');
            $table->index('source_id');
            $table->index('assign_date');
            $table->index('unassign_date');
            $table->json("vehicle_assign_assessment_picture");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_entities');
    }
};
