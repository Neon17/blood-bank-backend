<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BloodRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        BloodRequest::factory()
            ->count(100)
            ->make() // don't save yet
            ->each(function ($request) use ($users) {
                $request->user()->associate($users->random());
                $request->save();
            });
    }
}
