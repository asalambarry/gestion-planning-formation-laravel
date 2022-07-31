<?php

namespace Database\Seeders;

use App\Models\Planning;
use Illuminate\Database\Seeder;

class PlanningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Planning::factory()
            ->count(15)
            ->create();
    }
}
