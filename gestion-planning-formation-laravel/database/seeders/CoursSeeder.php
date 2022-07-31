<?php

namespace Database\Seeders;

use App\Models\Cours;
use App\Models\Planning;
use Illuminate\Database\Seeder;

class CoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        Cours::factory()
            ->count(10)
            ->has(Planning::factory()->count(4))
            ->create();
    }
}
