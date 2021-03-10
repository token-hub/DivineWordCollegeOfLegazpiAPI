<?php

namespace Database\Factories;

use App\Models\Update;
use Illuminate\Database\Eloquent\Factories\Factory;

class UpdateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Update::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'subtitle' => $this->faker->sentence,
            'category' => 1,
            'from' => now(),
            'to' => now(),
            'updates' => $this->faker->paragraph,
        ];
    }
}
