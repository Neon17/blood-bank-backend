<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DonorFactory extends Factory
{
    protected $model = \App\Models\Donor::class;

    private const BLOOD_TYPES = ['A+', 'A-', 'O+', 'O-', 'B+', 'B-', 'AB+', 'AB-'];
    private const VERIFICATION_STATUSES = ['pending', 'approved', 'wrong'];

    public function definition(): array
    {
        $latitude = $this->faker->latitude(26.3667, 30.4500);
        $longitude = $this->faker->longitude(80.0667, 88.2000);

        return [
            'user_id' => null, // assign via ->for(User) in seeder
            'contact_number' => '98' . $this->faker->randomNumber(8, true),
            'blood_type' => $this->faker->randomElement(self::BLOOD_TYPES),
            'address' => $this->faker->address() . ', Nepal',
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
        ];
    }
}
