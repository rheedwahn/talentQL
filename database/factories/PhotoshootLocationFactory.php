<?php

namespace Database\Factories;

use App\Models\Model;
use App\Models\PhotoshootLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoshootLocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PhotoshootLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'address' => $this->faker->address
        ];
    }
}
