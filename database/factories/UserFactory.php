<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    private const BLOOD_GROUPS = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];
    private const ROLES = ['admin', 'user'];

    private const MAJOR_CITIES = [
        ['city' => 'Kathmandu', 'lat' => 27.7172, 'lng' => 85.3240, 'weight' => 0.3],
        ['city' => 'Pokhara',   'lat' => 28.2096, 'lng' => 83.9856, 'weight' => 0.2],
        ['city' => 'Dharan',    'lat' => 26.8121, 'lng' => 87.2830, 'weight' => 0.1],
        ['city' => 'Biratnagar','lat' => 26.4524, 'lng' => 87.2718, 'weight' => 0.1],
        ['city' => 'Bharatpur','lat' => 27.6806, 'lng' => 84.4330, 'weight' => 0.1],
    ];

    public function definition(): array
    {
        // Weighted city selection
        $rand = mt_rand() / mt_getrandmax();
        $accum = 0;
        $selectedCity = null;

        foreach (self::MAJOR_CITIES as $city) {
            $accum += $city['weight'];
            if ($rand <= $accum) {
                $selectedCity = $city;
                break;
            }
        }

        if ($selectedCity) {
            $latitude = $selectedCity['lat'] + $this->faker->randomFloat(5, -0.02, 0.02);
            $longitude = $selectedCity['lng'] + $this->faker->randomFloat(5, -0.02, 0.02);
            $cityName = $selectedCity['city'];
        } else {
            $latitude = $this->faker->latitude(26.3667, 30.4500);
            $longitude = $this->faker->longitude(80.0667, 88.2000);
            $cityName = null;
        }

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'address' => $this->faker->streetAddress() . ($cityName ? ", $cityName" : "") . ', Nepal',
            'role' => $this->faker->randomElement(self::ROLES),
            'dob' => $this->faker->dateTimeBetween('-90 years', '-10 years')->format('Y-m-d'),
            'phone_number' => '98' . $this->faker->randomNumber(8, true),
            'city' => $cityName,
            'country' => 'Nepal',
            'current_city' => $cityName ?? $this->faker->city(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'blood_group' => $this->faker->randomElement(self::BLOOD_GROUPS),
            'will_donate' => $this->faker->boolean(50),
            'verified_as_donor' => $this->faker->boolean(30),
            'last_donated' => $this->faker->dateTimeBetween('2000-01-01', 'now')->format('Y-m-d'),
            'password' => bcrypt('password'),
        ];
    }
}
