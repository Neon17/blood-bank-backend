<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\BloodBank;
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
        // Create Admin with polymorphic user
        $admin = Admin::factory()->create([
            'role' => 'super_admin',
            'permissions' => json_encode(['all']),
            'status' => 'active',
        ]);

        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        // Associate admin with user polymorphically
        $adminUser->userable()->associate($admin);
        $adminUser->save();

        // Create Blood Banks with polymorphic users
        $bloodBanks = BloodBank::factory(5)->create();
        foreach ($bloodBanks as $bloodBank) {
            $bloodBankUser = User::factory()->bloodBank()->create();
            $bloodBankUser->userable()->associate($bloodBank);
            $bloodBankUser->save();
        }

        // Create regular user with donor profile
        $regularUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('12345678'),
            'verified_as_donor' => true,
        ]);

        $donor = Donor::factory()->approved()->create([
            'user_id' => $regularUser->id,
        ]);

        BloodRequest::factory(2)->create([
            'user_id' => $regularUser->id,
        ]);

        // Create more regular users (without userable association)
        User::factory(94)->create()->each(function ($user) {
            // 30% chance to have a donor profile
            if (fake()->boolean(30)) {
                $status = fake()->randomElement(['pending', 'approved', 'rejected']);
                
                $donor = Donor::factory()->create([
                    'user_id' => $user->id,
                    'verification_status' => $status,
                ]);
                
                // Update user's verified_as_donor status if approved
                if ($status === 'approved') {
                    $user->update(['verified_as_donor' => true]);
                }
            }

            // 20% chance to have blood requests
            if (fake()->boolean(20)) {
                BloodRequest::factory(fake()->numberBetween(1, 3))->create([
                    'user_id' => $user->id,
                ]);
            }
        });
    }
}