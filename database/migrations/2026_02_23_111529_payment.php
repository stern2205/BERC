<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Link to the application via protocol_code
            $table->string('protocol_code');
            $table->foreign('protocol_code')
                  ->references('protocol_code')
                  ->on('research_applications')
                  ->onDelete('cascade');

            $table->string('payment_method'); // e.g., GCash, Bank Transfer, Over-the-counter
            $table->decimal('amount_paid', 10, 2); // Handles up to 99,999,999.99
            $table->string('reference_number')->unique();
            $table->string('proof_of_payment_path'); // Path to the uploaded image/PDF

            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
