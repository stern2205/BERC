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
        Schema::create('research_applications', function (Blueprint $table) {
            $table->id(); // id
            $table->unsignedBigInteger('user_id'); // user who submitted the application
            $table->year('year'); // year
            $table->string('type_of_research', 50);
            $table->string('protocol_code', 50)->unique();
            $table->string('study_site', 255);
            $table->string('name_of_researcher', 255);
            $table->string('co_researchers', 255)->nullable();
            $table->string('research_title', 500);
            $table->string('tel_no', 50)->nullable();
            $table->string('mobile_no', 50)->nullable();
            $table->string('fax_no', 50)->nullable();
            $table->string('email', 255);
            $table->string('institution', 255);
            $table->string('institution_address', 500);
            $table->string('type_of_study', 100)->nullable();
            $table->string('type_of_study_others', 255)->nullable();
            $table->string('type_of_study1', 100)->nullable();
            $table->string('source_of_funding', 100)->nullable();
            $table->string('source_of_funding_others', 255)->nullable();
            $table->date('study_start_date')->nullable();
            $table->date('study_end_date')->nullable();
            $table->integer('study_participants')->nullable();
            $table->boolean('technical_review')->default(false);
            $table->string('tracking_number', 100)->nullable();
            $table->boolean('has_been_submitted_to_another_berc')->default(false);
            $table->text('brief_description')->nullable();
            $table->string('review_classification', 100)->nullable();
            $table->unsignedBigInteger('reviewer1_assigned')->nullable();
            $table->unsignedBigInteger('reviewer2_assigned')->nullable();
            $table->string('external_consultant', 255)->nullable();
            $table->string('e_signature', 560)->nullable();
            $table->string('version', 20)->nullable();
            $table->timestamps();

            // Optional: foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewer1_assigned')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reviewer2_assigned')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_applications');
    }
};
