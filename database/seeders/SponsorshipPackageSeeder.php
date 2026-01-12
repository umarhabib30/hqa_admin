<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SponsorshipPackageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sponsor_packages')->truncate();

        $now = Carbon::now();

        DB::table('sponsor_packages')->insert([
            [
                'id' => 1,
                'title' => 'Platinum Sponsor',
                'price_per_year' => 25000.00,
                'benefits' => json_encode([
                    '3-minute speaking engagement',
                    'Reserved VIP table',
                    'Promo booth / display space',
                    'Social media + website links',
                    'Sponsor banner ads throughout the event',
                    'Ability to distribute gifts, freebies, and coupons',
                    'Priority placement in all event materials',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'title' => 'Gold Sponsor',
                'price_per_year' => 10000.00,
                'benefits' => json_encode([
                    '1-minute speaking engagement',
                    'Prominent logo placement',
                    'Reserved seating',
                    'Opportunity for promotional materials',
                    'Website + social media visibility',
                    'Sponsor banner ads',
                    'Booth traffic boosters (giveaways/coupons)',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'title' => 'Premium Sponsor',
                'price_per_year' => 5000.00,
                'benefits' => json_encode([
                    'Logo display on event material',
                    'Reserved seating',
                    'Promotional material distribution',
                    'Sponsor banner highlight',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'title' => 'Silver Sponsor',
                'price_per_year' => 3000.00,
                'benefits' => json_encode([
                    'Logo featured on materials',
                    'Reserved seating',
                    'Promotional material distribution',
                    'Sponsor banner rotation',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
