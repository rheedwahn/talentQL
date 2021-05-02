<?php

namespace Database\Factories;

use App\Enums\AssetStatus;
use App\Models\PhotoshootAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoshootAssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PhotoshootAsset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'asset_link' => $this->faker->url,
            'thumbnail_link' => $this->faker->url,
            'status' => AssetStatus::PENDING
        ];
    }
}
