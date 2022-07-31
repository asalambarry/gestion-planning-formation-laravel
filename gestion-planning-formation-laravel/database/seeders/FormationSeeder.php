<?php

namespace Database\Seeders;

use App\Models\Cours;
use App\Models\Formation;
use Illuminate\Database\Seeder;

class FormationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Formation::factory()
            ->count(10)
            ->has(Cours::factory()->count(3))
            ->create();
    }
}
