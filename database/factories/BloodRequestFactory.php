<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BloodRequestFactory extends Factory
{
    protected $model = \App\Models\BloodRequest::class;

    private const BLOOD_TYPES = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];
    private const STATUSES = ['Pending', 'Cancelled', 'Completed'];
    private const VERIFICATION_STATUSES = ['Pending', 'Approved', 'Rejected'];

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
            'blood_type' => fake()->randomElement(self::BLOOD_TYPES),
            'quantity' => fake()->numberBetween(1, 5),
            'date_time' => fake()->dateTimeBetween('now', '+1 year'),
            'exact_location' => fake()->streetAddress() . ($cityName ? ", $cityName" : "") . ', Nepal',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'contact_number' => '98' . fake()->randomNumber(8, true),
            'city' => $cityName,
            'state' => fake()->state(),
            'country' => 'Nepal',
            'user_id' => null, // assign via ->for(User)
            'status' => fake()->randomElement(self::STATUSES),
            'verification_status' => fake()->randomElement(self::VERIFICATION_STATUSES),
            'active_status' => fake()->boolean(80),
            'donated_by' => null,
            'donated_by_user' => null,
            'donated_by_blood_banks' => null,
            'verified_by' => null,
        ];
    }
}
