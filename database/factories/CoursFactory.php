<?php

namespace Database\Factories;

use App\Models\Cours;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoursFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cours::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'intitule' => $this->faker->sentence(2),
            'user_id' => User::factory()->create(['type' => 'enseignant'])->id,
            'formation_id' => Formation::factory(),
        ];
    }

}
