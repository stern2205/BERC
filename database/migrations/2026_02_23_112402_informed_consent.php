<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Parent Table: Links to the Application via protocol_code
        Schema::create('icf_assessments', function (Blueprint $table) {
            $table->id();

            $table->string('protocol_code');
            $table->foreign('protocol_code')
                  ->references('protocol_code')
                  ->on('research_applications')
                  ->onDelete('cascade');

            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('draft'); // draft, submitted
            $table->unsignedBigInteger('reviewer_1_id')->nullable()->after('reviewer_id');
            $table->unsignedBigInteger('reviewer_2_id')->nullable()->after('reviewer_1_id');
            $table->unsignedBigInteger('reviewer_3_id')->nullable()->after('reviewer_2_id');
            $table->string('reviwer_1_done')->nullable();
            $table->string('reviwer_2_done')->nullable();
            $table->string('reviwer_3_done')->nullable();

            $table->timestamps();
        });

        // 2. Child Table: Stores the answers for each ICF criteria
        Schema::create('icf_assessment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('icf_assessment_id')
                  ->constrained('icf_assessments')
                  ->onDelete('cascade');

            // This will store question identifiers like '4.1', '4.2', etc.
            $table->string('question_number', 10);
            $table->string('remark')->nullable();     // Yes / No / N/A
            $table->string('line_page')->nullable();  // Where found in the document
            $table->text('reviewer_1_comments')->nullable();
            $table->text('reviewer_2_comments')->nullable();
            $table->text('reviewer_3_comments')->nullable();
            $table->text('reviewer_1_action_required')->nullable();
            $table->text('reviewer_2_action_required')->nullable();
            $table->text('reviewer_3_action_required')->nullable();
            $table->text('synthesized_comments')->nullable();
            $table->text('synthesized_comments_action_required')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('icf_assessment_items');
        Schema::dropIfExists('icf_assessments');
    }
};
