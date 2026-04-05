<?php

namespace Database\Seeders\Master;

use App\Models\Master\Pelapor;
use Illuminate\Database\Seeder;

class PelaporSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pelapor::factory()
            ->count(50)
            ->create();
    }
}
