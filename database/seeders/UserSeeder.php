<?php

namespace Database\Seeders;

use App\Models\Cours;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        User::factory()
            ->count(3)
            ->has(Cours::factory()->count(3))
            ->create([
                'formation_id' => Formation::factory()->create()->id,
                'type' => 'etudiant',
            ]);

        User::factory()
            ->count(3)
            ->has(Cours::factory()->count(3))
            ->create([
                'formation_id' => null,
                'type' => 'enseignant',
            ]);

        User::factory()
            ->count(3)
            ->create([
                'formation_id' => null,
                'type' => 'admin',
            ]);

        User::factory()
            ->count(3)
            ->create([
                'formation_id' => null,
                'type' => null,
            ]);

    }

}
