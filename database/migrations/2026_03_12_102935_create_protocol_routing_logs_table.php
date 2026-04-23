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
        Schema::create('protocol_routing_logs', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_code'); // Ties it to the specific application
            $table->string('document_nature'); // e.g., 'Application Form', 'Decision Letter'

            // Storing strings here so even if a user is deleted, the logbook paper trail remains intact
            $table->string('from_name')->nullable(); // Who sent/submitted it
            $table->string('to_name')->nullable();   // Who is receiving it

            // Optional: Keep foreign keys if you want to link directly to the User models
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('to_user_id')->nullable();

            $table->text('remarks')->nullable(); // Any extra notes (optional)

            $table->timestamps(); // created_at will serve as the Date & Time of the log
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocol_routing_logs');
    }
};
