<?php

namespace Database\Factories\Master;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Master\Pelapor>
 */
class PelaporFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'NIK' => fake('id_ID')->unique()->nik(),
            'nama' => fake('id_ID')->name(),
            'email' => fake()->unique()->email(),
            'no_hp' => fake()->phoneNumber(),
        ];
    }
}
