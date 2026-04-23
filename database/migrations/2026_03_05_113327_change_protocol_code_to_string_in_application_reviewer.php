<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Safely drop the old FK only if it exists
        DB::statement('ALTER TABLE application_reviewer DROP CONSTRAINT IF EXISTS application_reviewer_research_application_id_foreign');

        // Also drop protocol_code FK first if it already exists, to avoid duplicate constraint issues
        DB::statement('ALTER TABLE application_reviewer DROP CONSTRAINT IF EXISTS application_reviewer_protocol_code_foreign');

        // Change the column type to match research_applications.protocol_code
        DB::statement('ALTER TABLE application_reviewer ALTER COLUMN protocol_code TYPE VARCHAR(50) USING protocol_code::varchar');

        // Re-add FK to research_applications.protocol_code
        Schema::table('application_reviewer', function (Blueprint $table) {
            $table->foreign('protocol_code')
                ->references('protocol_code')
                ->on('research_applications')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE application_reviewer DROP CONSTRAINT IF EXISTS application_reviewer_protocol_code_foreign');

        DB::statement('ALTER TABLE application_reviewer ALTER COLUMN protocol_code TYPE BIGINT USING protocol_code::bigint');
    }
};
