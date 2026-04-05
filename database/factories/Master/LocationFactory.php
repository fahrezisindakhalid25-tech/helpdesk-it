<?php

namespace Database\Factories\Master;

use Illuminate\Database\Eloquent\Factories\Factory;


class LocationFactory extends Factory
{
    protected $model = \App\Models\Master\Location::class;

    public function definition(): array
    {
        return [
            'name' => fake('en_US')->unique()->stateAbbr(),
        ];
    }
}
