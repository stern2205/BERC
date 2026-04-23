<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_forms', function (Blueprint $table) {
            $table->string('is_consent_necessary')
                  ->nullable()
                  ->after('status');

            $table->text('no_consent_explanation')
                  ->nullable()
                  ->after('is_consent_necessary');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_forms', function (Blueprint $table) {
            $table->dropColumn([
                'is_consent_necessary',
                'no_consent_explanation'
            ]);
        });
    }
};
