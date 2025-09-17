<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    private const BLOOD_GROUPS = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];
    private const ROLES = ['admin', 'user'];

    public function definition(): array
    {
        $latitude = $this->faker->latitude(26.3667, 30.4500);
        $longitude = $this->faker->longitude(80.0667, 88.2000);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'address' => $this->faker->address() . ', Nepal',
            'role' => $this->faker->randomElement(self::ROLES),
            'dob' => $this->faker->dateTimeBetween('-90 years', '-10 years')->format('Y-m-d'),
            'phone_number' => '98' . $this->faker->randomNumber(8, true),
            'password' => bcrypt('password'), // default password
            'city' => $this->faker->city(),
            'country' => 'Nepal',
            'current_city' => $this->faker->city(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'blood_group' => $this->faker->randomElement(self::BLOOD_GROUPS),
            'will_donate' => $this->faker->boolean(50),
            'verified_as_donor' => $this->faker->boolean(30),
            'last_donated' => $this->faker->dateTimeBetween('2000-01-01', 'now')->format('Y-m-d'),
        ];
    }
}
