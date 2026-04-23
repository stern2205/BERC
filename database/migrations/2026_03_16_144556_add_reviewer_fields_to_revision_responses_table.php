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
        Schema::table('revision_responses', function (Blueprint $col) {
            // Reviewer IDs (Foreign Key compatible)
            $col->unsignedBigInteger('reviewer1_id')->nullable();
            $col->unsignedBigInteger('reviewer2_id')->nullable();
            $col->unsignedBigInteger('reviewer3_id')->nullable();

            // Reviewer Completion Status
            $col->boolean('reviewer1_done')->default(false);
            $col->boolean('reviewer2_done')->default(false);
            $col->boolean('reviewer3_done')->default(false);

            // Reviewer Action (Resolved or Action Required)
            // Using string for flexibility, e.g., 'resolved', 'action_required'
            $col->string('reviewer1_action')->nullable();
            $col->string('reviewer2_action')->nullable();
            $col->string('reviewer3_action')->nullable();

            // Synthesized Results
            $col->text('synthesized_comments')->nullable();
            $col->string('synthesized_comments_action')->nullable(); // resolved/action_required
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revision_responses', function (Blueprint $col) {
            $col->dropColumn([
                'reviewer1_id', 'reviewer2_id', 'reviewer3_id',
                'reviewer1_done', 'reviewer2_done', 'reviewer3_done',
                'reviewer1_action', 'reviewer2_action', 'reviewer3_action',
                'synthesized_comments', 'synthesized_comments_action'
            ]);
        });
    }
};
