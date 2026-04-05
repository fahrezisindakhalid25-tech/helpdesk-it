<?php

namespace Database\Seeders;

use Database\Seeders\Master\CategorySeeder;
use Database\Seeders\Master\LocationSeeder;
use Database\Seeders\Master\PelaporSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            CategorySeeder::class,
            PelaporSeeder::class,
        ]);
    }
}
