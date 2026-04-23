<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Landbank, PayPal
            $table->string('icon_label', 3)->nullable(); // e.g., LB, PP
            $table->string('account_number');
            $table->string('account_name');
            $table->string('logo_path')->nullable(); // Path to uploaded image
            $table->string('bg_color')->default('#213C71'); // Hex color code
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};
