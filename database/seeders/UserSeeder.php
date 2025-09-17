<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'user',
            'verified_as_donor' => true, // ensure this user is a donor
        ]);

        $donor = Donor::factory()->for($user)->create([
            'verification_status' => 'approved',
        ]);

        BloodRequest::factory(2)->for($user)->create();

        User::factory(98)->create();
    }
}
