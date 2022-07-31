<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'login' => $this->faker->unique()->slug(2),
            'mdp' => Hash::make('123456'), // mdp
            'formation_id' => null,
            'type' => $this->faker->randomElement([null, 'admin', 'enseignant', 'etudiant']),
        ];
    }

}
