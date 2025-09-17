<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DonorFactory extends Factory
{
    protected $model = \App\Models\Donor::class;

    private const BLOOD_TYPES = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];
    private const VERIFICATION_STATUSES = ['pending', 'approved', 'wrong'];

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
            $latitude = $selectedCity['lat'] + $this->faker->randomFloat(5, -0.02, 0.02);
            $longitude = $selectedCity['lng'] + $this->faker->randomFloat(5, -0.02, 0.02);
            $cityName = $selectedCity['city'];
        } else {
            // random Nepal-wide point
            $latitude = $this->faker->latitude(26.3667, 30.4500);
            $longitude = $this->faker->longitude(80.0667, 88.2000);
            $cityName = null;
        }

        return [
            'user_id' => null, // assign via ->for(User) in seeder
            'contact_number' => '98' . $this->faker->randomNumber(8, true),
            'blood_type' => $this->faker->randomElement(self::BLOOD_TYPES),
            'address' => $this->faker->streetAddress() . ($cityName ? ", $cityName" : "") . ', Nepal',
            'date_of_birth' => $this->faker->dateTimeBetween('-90 years', '-10 years')->format('Y-m-d'),
            'weight' => $this->faker->randomFloat(2, 45, 100),
            'height' => $this->faker->randomFloat(2, 150, 200),
            'last_donated_date' => $this->faker->dateTimeBetween('2000-01-01', 'now')->format('Y-m-d'),
            'medical_conditions' => $this->faker->sentence(),
            'current_medication' => $this->faker->sentence(),
            'current_health_status' => $this->faker->sentence(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'verification_status' => $this->faker->optional()->randomElement(self::VERIFICATION_STATUSES),
            'admin_message' => $this->faker->optional()->sentence(),
            'city' => $cityName,
        ];
    }
}
