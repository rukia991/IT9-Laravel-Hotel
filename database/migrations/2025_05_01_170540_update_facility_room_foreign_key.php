<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('facility_room', function (Blueprint $table) {
            $table->dropForeign(['room_id']); // Drop the existing foreign key
            $table->foreign('room_id')
                  ->references('id')->on('rooms')
                  ->onDelete('cascade'); // Add cascade delete
        });
    }
    
    public function down()
    {
        Schema::table('facility_room', function (Blueprint $table) {
            $table->dropForeign(['room_id']); // Drop the cascade delete foreign key
            $table->foreign('room_id')
                  ->references('id')->on('rooms'); // Revert to the original foreign key
        });
    }
};
