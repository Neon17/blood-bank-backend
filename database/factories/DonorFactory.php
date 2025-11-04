<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DonorFactory extends Factory
{
    protected $model = \App\Models\Donor::class;

    private const BLOOD_TYPES = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];
    private const VERIFICATION_STATUSES = ['pending', 'approved', 'rejected'];

    // Major cities with weights for urban-focused generation
    private const MAJOR_CITIES = [
        ['city' => 'Kathmandu', 'lat' => 27.7172, 'lng' => 85.3240, 'weight' => 0.3],
        ['city' => 'Pokhara',   'lat' => 28.2096, 'lng' => 83.9856, 'weight' => 0.2],
        ['city' => 'Dharan',    'lat' => 26.8121, 'lng' => 87.2830, 'weight' => 0.1],
        ['city' => 'Biratnagar','lat' => 26.4524, 'lng' => 87.2718, 'weight' => 0.1],
        ['city' => 'Bharatpur','lat' => 27.6806, 'lng' => 84.4330, 'weight' => 0.1],
    ];

    public function definition(): array
    {
        // Weighted selection of city or random Nepal point
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
            // small random offset around city
            $latitude = $selectedCity['lat'] + fake()->randomFloat(5, -0.02, 0.02);
            $longitude = $selectedCity['lng'] + fake()->randomFloat(5, -0.02, 0.02);
            $cityName = $selectedCity['city'];
        } else {
            // random Nepal-wide point
            $latitude = fake()->latitude(26.3667, 30.4500);
            $longitude = fake()->longitude(80.0667, 88.2000);
            $cityName = null;
        }

        return [
            'user_id' => null, // assign via ->for(User) in seeder
            'contact_number' => '98' . fake()->randomNumber(8, true),
            'blood_group' => fake()->randomElement(self::BLOOD_TYPES),
            'address' => fake()->streetAddress() . ($cityName ? ", $cityName" : "") . ', Nepal',
            'date_of_birth' => fake()->dateTimeBetween('-90 years', '-18 years')->format('Y-m-d'), // Changed to -18 years minimum
            'weight' => fake()->randomFloat(2, 45, 100),
            'height' => fake()->randomFloat(2, 150, 200),
            'last_donated_date' => fake()->dateTimeBetween('2000-01-01', 'now')->format('Y-m-d'),
            'medical_conditions' => fake()->boolean(70) ? fake()->sentence() : null, // 70% chance to have medical conditions
            'current_medication' => fake()->boolean(30) ? fake()->sentence() : null, // 30% chance to be on medication
            'current_health_status' => fake()->randomElement(['Excellent', 'Good', 'Fair', 'Poor']),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'verification_status' => fake()->randomElement(self::VERIFICATION_STATUSES), // Removed optional() to always have a status
            'admin_message' => fake()->boolean(20) ? fake()->sentence() : null, // 20% chance to have admin message
            'city' => $cityName,
            'state' => null,
            'country' => 'Nepal',
            'eligible_to_donate' => fake()->boolean(80)
        ];
    }

    // Optional: Add states for specific verification statuses
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'verification_status' => 'pending',
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'verification_status' => 'approved',
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'verification_status' => 'rejected',
                'admin_message' => fake()->sentence(), // Usually rejected donors have an admin message
            ];
        });
    }
}