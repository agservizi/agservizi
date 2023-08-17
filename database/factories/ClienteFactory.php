<?php

namespace Database\Factories;

use App\Models\Comune;
use Database\Seeders\CfPiRandom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                 'nome'=>$this->faker->firstName,
     'cognome'=>$this->faker->lastName,
     'email'=>$this->faker->unique()->safeEmail(),
     'email_verified_at'=>null,
     'password'=>null,
     'two_factor_secret'=>null,
     'two_factor_recovery_codes'=>null,
     'two_factor_confirmed_at'=>null,
     'remember_token'=>null,
     'ultimo_accesso'=>null,
     'telefono'=>$this->faker->phoneNumber(),
     'ruolo'=>null,

        ];
    }
}
