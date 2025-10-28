<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    private const BLOOD_GROUPS = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];

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
            $latitude = $selectedCity['lat'] + fake()->randomFloat(5, -0.02, 0.02);
            $longitude = $selectedCity['lng'] + fake()->randomFloat(5, -0.02, 0.02);
            $cityName = $selectedCity['city'];
        } else {
            $latitude = fake()->latitude(26.3667, 30.4500);
            $longitude = fake()->longitude(80.0667, 88.2000);
            $cityName = null;
        }

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'address' => fake()->streetAddress() . ($cityName ? ", $cityName" : "") . ', Nepal',
            'dob' => fake()->dateTimeBetween('-90 years', '-10 years')->format('Y-m-d'),
            'phone_number' => '98' . fake()->randomNumber(8, true),
            'city' => $cityName,
            'country' => 'Nepal',
            'current_city' => $cityName ?? fake()->city(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'blood_group' => fake()->randomElement(self::BLOOD_GROUPS),
            'will_donate' => fake()->boolean(50),
            'verified_as_donor' => fake()->boolean(30),
            'last_donated' => fake()->dateTimeBetween('2000-01-01', 'now')->format('Y-m-d'),
            'password' => Hash::make('password'),
        ];
    }

    // State for admin users
    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Admin ' . fake()->name(),
                'email' => 'admin.' . fake()->unique()->safeEmail(),
            ];
        });
    }

    // State for blood bank users
    public function bloodBank(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->company() . ' Blood Bank',
                'email' => 'bloodbank.' . fake()->unique()->safeEmail(),
            ];
        });
    }
}