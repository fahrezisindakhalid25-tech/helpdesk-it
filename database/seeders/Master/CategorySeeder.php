<?php

namespace Database\Seeders\Master;

use App\Models\Master\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Infrastruktur Jaringan']);
        Category::create(['name' => 'Aplikasi pendukung']);
    }
}
