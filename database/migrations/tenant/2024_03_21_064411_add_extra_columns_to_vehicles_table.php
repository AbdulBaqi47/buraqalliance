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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->index('location');
            $table->index('have_bagbox');
            $table->index('branding_type');
            $table->string("insurance_company");
            $table->date("insurance_issue_date");
            $table->date("insurance_paper_issue_date");
            $table->date("insurance_paper_expiry_date");
            $table->json("insurance_paper_attachment");
            $table->date("advertisement_issue_date");
            $table->date("advertisement_expiry_date");
            $table->json("advertisement_attachment");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            //
        });
    }
};
