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
        Schema::create('attendance_models', function (Blueprint $table) {
            $table->id();
            $table->string('in');
            $table->string('out')->nullable();
            $table->string('in_status')->nullable();  // Kolom untuk status IN
            $table->string('out_status')->nullable(); // Kolom untuk status OUT
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_models');
    }
};
