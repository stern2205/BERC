<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. REVIEWERS TABLE
        // Logs all registered reviewers and their specific committee info
        Schema::create('reviewers', function (Blueprint $table) {
            $table->id();
            // Links to the main users table for login credentials
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['Panel Expert', 'Layperson', 'External Consultant']);
            $table->string('panel')->nullable(); // e.g., 'Panel I', 'Panel II'
            $table->string('specialization')->nullable(); // Used for specific consultant roles
            $table->integer('avg_review_time_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. ASSIGNED PROTOCOLS LOG (Pivot Table)
        // Tracks exactly which application goes to which reviewer, and their response
        Schema::create('application_reviewer', function (Blueprint $table) {
            $table->id();
            // Links to your existing applications table
            $table->foreignId('protocol_code')->constrained('research_applications')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained()->cascadeOnDelete();

            // Tracking the workflow state
            $table->enum('status', ['Pending', 'Accepted', 'Declined', 'Expired', 'Rejected'])->default('Pending');

            // Timestamp logs for the timeline
            $table->timestamp('date_assigned')->useCurrent();
            $table->timestamp('date_accepted')->nullable();
            $table->timestamp('date_declined')->nullable();
            $table->timestamp('date_expired')->nullable();
            $table->text('declined_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('application_reviewer');
        Schema::dropIfExists('reviewers');
    }
};
