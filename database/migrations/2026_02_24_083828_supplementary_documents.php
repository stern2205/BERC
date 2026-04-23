<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplementary_documents', function (Blueprint $table) {
            $table->id();

            // Foreign Key linking to Research Applications
            $table->string('protocol_code');
            $table->foreign('protocol_code')
                  ->references('protocol_code')
                  ->on('research_applications')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // 'special_populations' or 'others'
            $table->string('type');

            // Description (e.g., "MOU with LGU" or specific permit name)
            $table->string('description')->nullable();

            // The file path
            $table->string('file_path');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplementary_documents');
    }
};
