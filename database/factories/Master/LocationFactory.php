<?php

namespace Database\Factories\Master;

use App\Models\Master\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => fake('en_US')->unique()->stateAbbr(),
        ];
    }
}
