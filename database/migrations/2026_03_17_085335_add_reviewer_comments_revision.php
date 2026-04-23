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
    public function up(): void
    {
        Schema::table('revision_responses', function (Blueprint $table) {
            $table->text('reviewer1_remarks')->nullable();
            $table->text('reviewer2_remarks')->nullable();
            $table->text('reviewer3_remarks')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
