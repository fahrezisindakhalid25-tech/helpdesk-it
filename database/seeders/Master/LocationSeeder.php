<?php

namespace Database\Seeders\Master;

use App\Models\Master\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::factory()
            ->count(50)
            ->create();
    }
}
