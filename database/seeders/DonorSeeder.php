<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        $users->each(function ($user) {
            if (mt_rand(1, 100) <= 40) {
                Donor::factory()->for($user)->create();
            }
        });
    }
}
