<?php

namespace Database\Seeders;

use App\Models\Bottle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BottleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bottle::factory()->count(50)->create();
    }
}
