<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('decision_letters', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_code');

            // The dropdown choice (approved, resubmit, reject)
            $table->string('decision_status');

            $table->date('letter_date');
            $table->string('proponent')->nullable();
            $table->string('designation')->nullable();
            $table->string('institution')->nullable();
            $table->string('address')->nullable();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->string('dear_name')->nullable();
            $table->date('support_date')->nullable();

            // Store array of documents as JSON
            $table->json('documents')->nullable();

            $table->string('signature_path')->nullable(); // For future signature uploads

            $table->timestamps();

            // Foreign key to research_applications table
            $table->foreign('protocol_code')
                  ->references('protocol_code')
                  ->on('research_applications')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('decision_letters');
    }
};
