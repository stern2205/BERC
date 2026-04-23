<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('revision_documents', function (Blueprint $table) {
            $table->id();
            // Link to the specific revision ID, not the protocol code
            $table->foreignId('revision_id')
                  ->constrained('research_application_revisions')
                  ->onDelete('cascade');

            // e.g., 'informed_consent', 'questionnaire', 'supplementary'
            $table->string('type', 100);

            // e.g., "Tagalog Translation" or "Minor Assent Form"
            $table->string('description', 255)->nullable();

            $table->string('file_path', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('revision_documents');
    }
};
