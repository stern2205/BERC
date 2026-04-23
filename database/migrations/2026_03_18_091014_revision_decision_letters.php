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
        Schema::create('revision_decision_letters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('protocol_code');
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
            $table->json('documents')->nullable();

            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('instructions')->nullable();

            $table->string('signature_path')->nullable();
            $table->string('approval_status')->nullable();
            $table->string('version_number');

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revision_decision_letters');
    }
};
