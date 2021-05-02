<?php

namespace Database\Factories;

use App\Models\Photoshoot;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoshootFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Photoshoot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product' => $this->faker->firstName,
            'description' => $this->faker->text(40),
            'company' => $this->faker->company,
            'number_of_shots' => $this->faker->numberBetween(1,5)
        ];
    }
}
