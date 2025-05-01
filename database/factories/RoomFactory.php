<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition()
    {
        return [
            'type_id' => \App\Models\Type::inRandomOrder()->first()->id ?? 1, // Default to 1 if no types exist
            'room_status_id' => \App\Models\RoomStatus::inRandomOrder()->first()->id ?? 1, // Default to 1 if no statuses exist
            'number' => $this->faker->unique()->numberBetween(100, 999),
            'capacity' => $this->faker->numberBetween(1, 4),
            'price' => $this->faker->randomFloat(2, 1000, 5000),
            'view' => $this->faker->sentence,
            'image' => 'https://picsum.photos/seed/room' . $this->faker->unique()->numberBetween(1, 9999) . '/400/300',
            'description' => $this->faker->paragraph, // Generates a random description
        ];
    }
}