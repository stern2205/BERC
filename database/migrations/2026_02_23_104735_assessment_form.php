<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. The Parent Table
        Schema::create('assessment_forms', function (Blueprint $table) {
            $table->id();

            // Link using the protocol_code string instead of the ID
            $table->string('protocol_code');
            $table->foreign('protocol_code')
                  ->references('protocol_code')
                  ->on('research_applications')
                  ->onDelete('cascade');

            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('draft');

            $table->unsignedBigInteger('reviewer_1_id')->nullable()->after('reviewer_id');
            $table->unsignedBigInteger('reviewer_2_id')->nullable()->after('reviewer_1_id');
            $table->unsignedBigInteger('reviewer_3_id')->nullable()->after('reviewer_2_id');
            $table->string('reviewer_1_done')->nullable();
            $table->string('reviewer_2_done')->nullable();
            $table->string('reviewer_3_done')->nullable();
            $table->timestamps();
        });

        // 2. The Child Table (Questions 1.1 to 3.9)
        Schema::create('assessment_form_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_form_id')
                  ->constrained('assessment_forms')
                  ->onDelete('cascade');

            $table->string('question_number', 10); // '1.1', '2.4', etc.
            $table->string('remark')->nullable();
            $table->string('line_page')->nullable();
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
        Schema::dropIfExists('assessment_form_items');
        Schema::dropIfExists('assessment_forms');
    }
};
