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
        Schema::create('to_do_lists', function (Blueprint $table) {
            $table->id();
            $table->string('content');
            $table->string('status');
            $table->date('date');
            $table->bigInteger('user_id');
            $table->text('keterangan'); 
            $table->text('pesan')->nullable(); 
            $table->timestamps();

            // Optional: Add foreign key constraint if you have a users table
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('to_do_lists');
    }
};
