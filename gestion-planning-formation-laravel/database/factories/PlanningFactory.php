<?php

namespace Database\Factories;

use App\Models\Cours;
use App\Models\Planning;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanningFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Planning::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        $days = random_int(1, 7);

        return [
            'cours_id' => Cours::factory(),
            'date_debut' => now()->addDays($days)->setHours(7)->addHours(random_int(1, 3))->setMinutes(random_int(1, 60)),
            'date_fin' => now()->addDays($days)->setHours(7)->addHours(random_int(4, 8))->setMinutes(random_int(1, 60)),
        ];
    }

}
