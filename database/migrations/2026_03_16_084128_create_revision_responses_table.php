<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('revision_responses', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_code', 50);
            $table->integer('revision_number');

            // The point-by-point data
            $table->string('item', 255)->nullable();
            $table->text('berc_recommendation');
            $table->text('researcher_response');
            $table->string('section_and_page', 255);
            $table->string('secretariat_comment', 255)->nullable();

            $table->timestamps();

            // Link back to the specific revision
            $table->foreign(['protocol_code', 'revision_number'])
                  ->references(['protocol_code', 'revision_number'])
                  ->on('research_application_revisions')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('revision_responses');
    }
};
