<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Donation;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 40 donations with realistic distribution
        
        // 15 Approved past donations
        Donation::factory()
            ->count(15)
            ->approved()
            ->create();

        // 10 Pending upcoming donations
        Donation::factory()
            ->count(10)
            ->pending()
            ->create();

        // 5 Cancelled donations
        Donation::factory()
            ->count(5)
            ->cancelled()
            ->create();

        // 5 Emergency donations (higher quantity)
        Donation::factory()
            ->count(5)
            ->emergency()
            ->approved()
            ->create();

        // 5 Kathmandu valley specific donations
        Donation::factory()
            ->count(5)
            ->kathmanduValley()
            ->create();

        // Blood group distribution for better testing
        $bloodGroups = ['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-'];
        foreach ($bloodGroups as $bloodGroup) {
            Donation::factory()
                ->count(2)
                ->bloodGroup($bloodGroup)
                ->create();
        }

        $this->command->info('40 donations created successfully with Nepali data!');
        $this->command->info('Blood Group Distribution:');
        
        $bloodGroupCounts = Donation::select('blood_group')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('blood_group')
            ->pluck('count', 'blood_group')
            ->toArray();
            
        foreach ($bloodGroupCounts as $group => $count) {
            $this->command->info("  {$group}: {$count} donations");
        }

        $statusCounts = Donation::select('verification_status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('verification_status')
            ->pluck('count', 'verification_status')
            ->toArray();
            
        $this->command->info('Verification Status Distribution:');
        foreach ($statusCounts as $status => $count) {
            $this->command->info("  {$status}: {$count} donations");
        }
    }
}