<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviewers', function (Blueprint $table) {
            // Add the nullable reason column
            $table->text('reason')->nullable()->after('type')
                  ->comment('Used exclusively for External Consultant requests.');
        });

        // Add a Check Constraint to ensure ONLY External Consultants have a reason
        // (If the type is Panel Expert or Layperson, the reason must be NULL)
        DB::statement("
            ALTER TABLE reviewers
            ADD CONSTRAINT reviewers_reason_check
            CHECK (
                (type = 'External Consultant') OR (reason IS NULL)
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint first, then the column
        DB::statement("ALTER TABLE reviewers DROP CONSTRAINT reviewers_reason_check");

        Schema::table('reviewers', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
