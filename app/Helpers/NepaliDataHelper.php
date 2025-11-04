<?php

namespace App\Helpers;

class NepaliDataHelper
{
    /**
     * Major Nepali cities with coordinates and provinces
     */
    public static function getNepaliCities(): array
    {
        return [
            ['city' => 'Kathmandu', 'province' => 'Bagmati', 'latitude' => 27.7172, 'longitude' => 85.3240],
            ['city' => 'Pokhara', 'province' => 'Gandaki', 'latitude' => 28.2096, 'longitude' => 83.9856],
            ['city' => 'Lalitpur', 'province' => 'Bagmati', 'latitude' => 27.6667, 'longitude' => 85.3167],
            ['city' => 'Bharatpur', 'province' => 'Bagmati', 'latitude' => 27.6833, 'longitude' => 84.4333],
            ['city' => 'Biratnagar', 'province' => 'Province 1', 'latitude' => 26.4834, 'longitude' => 87.2834],
            ['city' => 'Birgunj', 'province' => 'Madhesh', 'latitude' => 27.0000, 'longitude' => 84.8667],
            ['city' => 'Butwal', 'province' => 'Lumbini', 'latitude' => 27.7000, 'longitude' => 83.4500],
            ['city' => 'Dharan', 'province' => 'Province 1', 'latitude' => 26.8167, 'longitude' => 87.2833],
            ['city' => 'Nepalgunj', 'province' => 'Lumbini', 'latitude' => 28.0500, 'longitude' => 81.6167],
            ['city' => 'Itahari', 'province' => 'Province 1', 'latitude' => 26.6667, 'longitude' => 87.2833],
            ['city' => 'Hetauda', 'province' => 'Bagmati', 'latitude' => 27.4167, 'longitude' => 85.0333],
            ['city' => 'Bhaktapur', 'province' => 'Bagmati', 'latitude' => 27.6722, 'longitude' => 85.4278],
            ['city' => 'Janakpur', 'province' => 'Madhesh', 'latitude' => 26.7288, 'longitude' => 85.9342],
            ['city' => 'Dhangadhi', 'province' => 'Sudurpashchim', 'latitude' => 28.6950, 'longitude' => 80.5930],
            ['city' => 'Tikapur', 'province' => 'Sudurpashchim', 'latitude' => 28.5000, 'longitude' => 81.1333],
        ];
    }

    /**
     * Get blood group distribution based on Nepali population statistics
     */
    public static function getBloodGroupDistribution(): array
    {
        return [
            'O+' => 35.5,  // Most common in Nepal
            'B+' => 28.5,
            'A+' => 22.5,
            'AB+' => 7.5,
            'O-' => 2.5,
            'B-' => 1.5,
            'A-' => 1.0,
            'AB-' => 0.5,
        ];
    }

    /**
     * Generate realistic Nepali phone number
     */
    public static function generateNepaliPhoneNumber(): string
    {
        $prefixes = ['+977', '977', '0'];
        $prefix = $prefixes[array_rand($prefixes)];
        
        // Nepali mobile numbers typically start with 98, 97, 96
        $firstTwoDigits = ['98', '97', '96', '98'][array_rand(['98', '97', '96', '98'])];
        $remainingDigits = sprintf('%07d', mt_rand(0, 9999999));
        
        return $prefix . $firstTwoDigits . $remainingDigits;
    }

    /**
     * Generate realistic Nepali name
     */
    public static function generateNepaliName(): string
    {
        $firstNames = [
            'Raj', 'Sita', 'Krishna', 'Gita', 'Bikash', 'Anita', 'Suresh', 'Mina',
            'Hari', 'Shanti', 'Ramesh', 'Sunita', 'Bimal', 'Laxmi', 'Narendra', 'Puja',
            'Santosh', 'Sabita', 'Dipak', 'Rita', 'Kiran', 'Bina', 'Arjun', 'Saraswati',
            'Prakash', 'Maya', 'Gopal', 'Saru', 'Nabin', 'Radhika', 'Umesh', 'Kamala'
        ];
        
        $lastNames = [
            'Shrestha', 'Tamang', 'Rai', 'Gurung', 'Magar', 'Karki', 'Poudel', 'Bhandari',
            'Khadka', 'Limbu', 'Thapa', 'Chhetri', 'Basnet', 'Dahal', 'Pandey', 'Sharma',
            'Ghimire', 'Adhikari', 'Rana', 'Gurung', 'Sherpa', 'Maharjan', 'Bohara', 'Koirala'
        ];
        
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    /**
     * Generate realistic Nepali location address
     */
    public static function generateNepaliAddress(string $city): string
    {
        $templates = [
            "Ward No. {ward}, {area}, {city}",
            "Near {landmark}, {area}, {city}",
            "{area} Area, {city}",
            "Behind {place}, {street}, {city}",
        ];
        
        $template = $templates[array_rand($templates)];
        
        $placeholders = [
            '{ward}' => mt_rand(1, 32),
            '{area}' => self::getRandomArea($city),
            '{landmark}' => self::getRandomLandmark(),
            '{place}' => self::getRandomPlace(),
            '{street}' => self::getRandomStreet(),
            '{city}' => $city,
        ];
        
        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Get random weighted blood group based on distribution
     */
    public static function getRandomBloodGroup(): string
    {
        $distribution = self::getBloodGroupDistribution();
        $random = mt_rand(1, 1000) / 10; // Get random number between 0.1 and 100.0
        
        $cumulative = 0;
        foreach ($distribution as $group => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) {
                return $group;
            }
        }
        
        return 'O+'; // Fallback
    }

    /**
     * Get random Nepali city data
     */
    public static function getRandomCity(): array
    {
        $cities = self::getNepaliCities();
        return $cities[array_rand($cities)];
    }

    /**
     * Get cities by province
     */
    public static function getCitiesByProvince(string $province): array
    {
        return array_filter(self::getNepaliCities(), function($city) use ($province) {
            return $city['province'] === $province;
        });
    }

    /**
     * Helper methods for address generation
     */
    private static function getRandomArea(string $city): string
    {
        $areasByCity = [
            'Kathmandu' => ['Thamel', 'New Road', 'Putalisadak', 'Baneshwor', 'Kalimati', 'Koteshwor'],
            'Pokhara' => ['Lakeside', 'Damside', 'Matepani', 'Birauta', 'Bagar'],
            'Lalitpur' => ['Patan', 'Jawalakhel', 'Kumaripati', 'Satdobato', 'Ekantakuna'],
            'Biratnagar' => ['Roadways', 'Bhanu Chowk', 'Rani Bazar', 'Golchha Chowk'],
        ];
        
        return $areasByCity[$city][array_rand($areasByCity[$city] ?? ['Main Area'])] ?? 'Main Area';
    }

    private static function getRandomLandmark(): string
    {
        $landmarks = [
            'Hospital', 'School', 'Temple', 'Bus Park', 'Chowk', 'Police Station', 
            'Bank', 'College', 'Market', 'Park', 'Mall', 'Government Office'
        ];
        return $landmarks[array_rand($landmarks)];
    }

    private static function getRandomPlace(): string
    {
        $places = [
            'Police Station', 'Bank', 'Hospital', 'School', 'Temple', 'Market',
            'College', 'Hotel', 'Park', 'Bus Stop'
        ];
        return $places[array_rand($places)];
    }

    private static function getRandomStreet(): string
    {
        $streets = [
            'Main Road', 'Hospital Road', 'School Road', 'Market Street', 'Temple Road',
            'New Road', 'Old Road', 'Station Road', 'Park Street'
        ];
        return $streets[array_rand($streets)];
    }
}