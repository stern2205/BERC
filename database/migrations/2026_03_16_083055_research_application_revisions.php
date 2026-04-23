<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('research_application_revisions', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_code', 50);

            // 1, 2, or 3
            $table->integer('revision_number');
            $table->string('status')->default('submitted');

            // Add any other revision-specific notes/metadata here
            $table->text('researcher_revision_notes')->nullable();
            $table->text('secretariat_comment')->nullable();

            $table->timestamps();

            $table->foreign('protocol_code')
                  ->references('protocol_code')
                  ->on('research_applications')
                  ->onDelete('cascade');

            $table->unique(['protocol_code', 'revision_number']);
        });

        DB::statement('ALTER TABLE research_application_revisions ADD CONSTRAINT max_revisions CHECK (revision_number <= 3)');
    }

    public function down()
    {
        Schema::dropIfExists('research_application_revisions');
    }
};
