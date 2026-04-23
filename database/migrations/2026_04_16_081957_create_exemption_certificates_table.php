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
        Schema::create('exemption_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_code')->index(); // To link back to research_applications
            $table->date('date_issued');
            $table->string('investigator_name');
            $table->text('study_title');
            $table->string('berc_code')->unique();

            // Dynamic Signatory
            $table->string('chairperson_name');

            // Footer Tracking
            $table->string('tracking_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exemption_certificates');
    }
};
