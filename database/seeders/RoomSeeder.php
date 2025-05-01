<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Running RoomSeeder...\n";
    
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
        // Truncate the table
        Room::truncate();
    
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
        // Seed the table
        Room::factory(20)->create();
    
        echo "RoomSeeder completed.\n";
    }
}