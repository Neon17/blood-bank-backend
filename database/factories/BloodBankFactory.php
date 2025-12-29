<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BloodBankFactory extends Factory
{
    protected $model = \App\Models\BloodBank::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Blood Bank',
            'address' => fake()->address(),
            'amount_of_blood' => json_encode([
                'A+' => fake()->numberBetween(0, 50),
                'A-' => fake()->numberBetween(0, 30),
                'B+' => fake()->numberBetween(0, 50),
                'B-' => fake()->numberBetween(0, 30),
                'O+' => fake()->numberBetween(0, 50),
                'O-' => fake()->numberBetween(0, 30),
                'AB+' => fake()->numberBetween(0, 50),
                'AB-' => fake()->numberBetween(0, 30),
            ]),
            'phone_number' => '98' . fake()->randomNumber(8, true),
            'type' => fake()->randomElement(['hospital', 'independent', 'red_cross']),
        ];
    }
}