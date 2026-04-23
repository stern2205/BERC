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
        // 1. Add 'status' column to the main table
        Schema::table('research_applications', function (Blueprint $table) {
            // Default status is 'submitted'
            $table->string('status')->default('submitted')->after('user_id')->index();

            // Ensure protocol_code is unique/indexed so we can use it as a foreign key
            // (If you already have this indexed, you can comment this line out)
            // $table->index('protocol_code');
        });

        // 2. Create the history logs table
        Schema::create('research_application_logs', function (Blueprint $table) {
            $table->id();

            // LINK: Connect to the application via protocol_code
            $table->string('protocol_code');
            $table->foreign('protocol_code')
                  ->references('protocol_code')
                  ->on('research_applications')
                  ->onDelete('cascade');

            // LINK: Connect to the user who made the change
            $table->foreignId('user_id')->constrained('users');

            // DATA: The status change info
            $table->string('status');
            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop the logs table first (because it depends on the main table)
        Schema::dropIfExists('research_application_logs');

        // 2. Remove the status column from the main table
        Schema::table('research_applications', function (Blueprint $table) {
            $table->dropColumn('status');
            // $table->dropIndex(['protocol_code']); // Only needed if you added the index in up()
        });
    }
};
