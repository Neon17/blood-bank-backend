<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    protected $model = \App\Models\Admin::class;

    public function definition(): array
    {
        return [
            'role' => fake()->randomElement(['super_admin', 'moderator', 'manager']),
            'permissions' => json_encode(['all']),
            'status' => 'active',
        ];
    }
}