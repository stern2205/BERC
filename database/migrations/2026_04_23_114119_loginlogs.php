<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();

            // Foreign key to users table
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->text('user_agent')->nullable();

            $table->timestamp('logged_in_at')->nullable();
            $table->timestamp('logged_out_at')->nullable();

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
