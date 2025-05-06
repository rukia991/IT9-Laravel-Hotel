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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('guest_id');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->enum('status', ['Pending', 'Confirmed', 'Cancelled']);
            $table->timestamps();
        
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            // $table->foreign('guest_id')->references('id')->on('guests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
