<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BloodRequestFactory extends Factory
{
    protected $model = \App\Models\BloodRequest::class;

    private const BLOOD_TYPES = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];
    private const STATUSES = ['Pending', 'Approved', 'Rejected'];

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
            'blood_type' => $this->faker->randomElement(self::BLOOD_TYPES),
            'quantity' => $this->faker->numberBetween(1, 5),
            'date_time' => $this->faker->dateTimeBetween('now', '+1 year'),
            'exact_location' => $this->faker->streetAddress() . ($cityName ? ", $cityName" : "") . ', Nepal',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'contact_number' => '98' . $this->faker->randomNumber(8, true),
            'city' => $cityName,
            'state' => $this->faker->state(),
            'country' => 'Nepal',
            'user_id' => null, // assign via ->for(User)
            'status' => $this->faker->randomElement(self::STATUSES),
            'active_status' => $this->faker->boolean(80),
            'donated_by' => null,
            'donated_by_user' => null,
            'donated_by_blood_banks' => null,
            'verified_by' => null,
        ];
    }
}
