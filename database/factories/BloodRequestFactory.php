<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class BloodRequestFactory extends Factory
{
    protected $model = \App\Models\BloodRequest::class;

    private const BLOOD_TYPES = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];
    private const STATUSES = ['Pending', 'Approved', 'Rejected'];

    public function definition(): array
    {
        $latitude = $this->faker->latitude(26.3667, 30.4500);
        $longitude = $this->faker->longitude(80.0667, 88.2000);

        return [
            'blood_type' => $this->faker->randomElement(self::BLOOD_TYPES),
            'quantity' => $this->faker->numberBetween(1, 5),
            'date_time' => $this->faker->dateTimeBetween('now', '+1 year'),
            'exact_location' => $this->faker->address() . ', Nepal',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'contact_number' => '98' . $this->faker->randomNumber(8, true),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => 'Nepal',
            'user_id' => null, // assign via ->for(User)
            'status' => $this->faker->randomElement(self::STATUSES),
            'active_status' => $this->faker->boolean(80), // 80% active
            'donated_by' => null,
            'donated_by_user' => null,
            'donated_by_blood_banks' => null,
            'verified_by' => null,
        ];
    }
}
