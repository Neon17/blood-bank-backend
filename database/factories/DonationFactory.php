<?php

namespace Database\Factories;

use App\Helpers\NepaliDataHelper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cityData = NepaliDataHelper::getRandomCity();
        $donationDate = $this->faker->dateTimeBetween('-6 months', '+2 months');
        $isPastDonation = $donationDate < now();

        return [
            'quantity' => $this->faker->numberBetween(1, 2),
            'blood_group' => NepaliDataHelper::getRandomBloodGroup(),
            'date_time' => $donationDate,
            'exact_location' => NepaliDataHelper::generateNepaliAddress($cityData['city']),
            'contact_number' => NepaliDataHelper::generateNepaliPhoneNumber(),
            'contact_name' => NepaliDataHelper::generateNepaliName(),
            'contact_email' => $this->faker->optional(70)->safeEmail(),
            'city' => $cityData['city'],
            'state' => $cityData['province'],
            'country' => 'Nepal',
            'latitude' => $this->faker->randomFloat(8, $cityData['latitude'] - 0.05, $cityData['latitude'] + 0.05),
            'longitude' => $this->faker->randomFloat(8, $cityData['longitude'] - 0.05, $cityData['longitude'] + 0.05),
            'verification_status' => $isPastDonation ? 
                $this->faker->randomElement(['approved', 'cancelled']) : 
                $this->faker->randomElement(['pending', 'approved']),
            
            'blood_request_id' => null,
            'user_id' => null,
            'blood_bank_id' => null,
            'donation_program_id' => null,
        ];
    }

    /**
     * Generate realistic Nepali phone number
     */
    private function generateNepaliPhoneNumber(): string
    {
        $prefixes = ['+977', '977', '0'];
        $prefix = $this->faker->randomElement($prefixes);
        
        // Nepali mobile numbers typically start with 98, 97, 96
        $firstTwoDigits = $this->faker->randomElement(['98', '97', '96', '98']);
        $remainingDigits = $this->faker->numerify('#######'); // 7 more digits
        
        return $prefix . $firstTwoDigits . $remainingDigits;
    }

    /**
     * Generate realistic Nepali name
     */
    private function generateNepaliName(): string
    {
        $firstNames = [
            'Raj', 'Sita', 'Krishna', 'Gita', 'Bikash', 'Anita', 'Suresh', 'Mina',
            'Hari', 'Shanti', 'Ramesh', 'Sunita', 'Bimal', 'Laxmi', 'Narendra', 'Puja',
            'Santosh', 'Sabita', 'Dipak', 'Rita', 'Kiran', 'Bina', 'Arjun', 'Saraswati'
        ];
        
        $lastNames = [
            'Shrestha', 'Tamang', 'Rai', 'Gurung', 'Magar', 'Karki', 'Poudel', 'Bhandari',
            'Khadka', 'Limbu', 'Thapa', 'Chhetri', 'Basnet', 'Dahal', 'Pandey', 'Sharma'
        ];
        
        return $this->faker->randomElement($firstNames) . ' ' . $this->faker->randomElement($lastNames);
    }

    /**
     * Generate realistic Nepali location address
     */
    private function generateNepaliLocation(string $city): string
    {
        $locations = [
            'Near ' . $this->faker->randomElement(['Hospital', 'School', 'Temple', 'Bus Park', 'Chowk']) . ', ' . $this->faker->streetName(),
            'Ward No. ' . $this->faker->numberBetween(1, 32) . ', ' . $this->faker->streetName(),
            $this->faker->randomElement(['New Road', 'Old Baneshwor', 'Putalisadak', 'Thamel', 'Durbarmarg']) . ' Area',
            'Behind ' . $this->faker->randomElement(['Police Station', 'Bank', 'College', 'Market']) . ', ' . $city,
        ];
        
        return $this->faker->randomElement($locations);
    }

    /**
     * State for pending verification status
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'pending',
            'date_time' => $this->faker->dateTimeBetween('+1 day', '+2 months'),
        ]);
    }

    /**
     * State for approved verification status
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'approved',
            'date_time' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * State for cancelled verification status
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'cancelled',
            'date_time' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * State for specific blood group
     */
    public function bloodGroup(string $bloodGroup): static
    {
        return $this->state(fn (array $attributes) => [
            'blood_group' => $bloodGroup,
        ]);
    }

    /**
     * State for Kathmandu valley donations
     */
    public function kathmanduValley(): static
    {
        $kathmanduValleyCities = [
            ['city' => 'Kathmandu', 'province' => 'Bagmati', 'latitude' => 27.7172, 'longitude' => 85.3240],
            ['city' => 'Lalitpur', 'province' => 'Bagmati', 'latitude' => 27.6667, 'longitude' => 85.3167],
            ['city' => 'Bhaktapur', 'province' => 'Bagmati', 'latitude' => 27.6722, 'longitude' => 85.4278],
        ];
        
        $selectedCity = $this->faker->randomElement($kathmanduValleyCities);
        
        return $this->state(fn (array $attributes) => [
            'city' => $selectedCity['city'],
            'state' => $selectedCity['province'],
            'latitude' => $this->faker->randomFloat(8, $selectedCity['latitude'] - 0.03, $selectedCity['latitude'] + 0.03),
            'longitude' => $this->faker->randomFloat(8, $selectedCity['longitude'] - 0.03, $selectedCity['longitude'] + 0.03),
        ]);
    }

    /**
     * State for emergency blood requests (higher quantity)
     */
    public function emergency(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $this->faker->numberBetween(2, 3),
            'verification_status' => 'approved',
        ]);
    }
}