<?php

namespace Database\Seeders;

use App\Models\CampaignType;
use Illuminate\Database\Seeder;

class CampaignTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'slug' => 'growthhire',
                'name' => 'GrowthHire',
                'coins_price' => 25,
                'scr_price' => 500,
                'duration_days' => 15,
                'est_reach_min' => 5000,
                'est_reach_max' => 8000,
                'features' => [
                    'Top position in search results',
                    'Highlighted with special badge',
                    '3x more visibility',
                    'Mobile app featured section',
                ],
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'slug' => 'smarthire',
                'name' => 'SmartHire',
                'coins_price' => 45,
                'scr_price' => 900,
                'duration_days' => 30,
                'est_reach_min' => 12000,
                'est_reach_max' => 18000,
                'features' => [
                    'Premium placement across platform',
                    'Email notifications to candidates',
                    '5x more visibility',
                    'Social media promotion',
                    'Analytics dashboard',
                ],
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'powerhire',
                'name' => 'PowerHire',
                'coins_price' => 60,
                'scr_price' => 1200,
                'duration_days' => 30,
                'est_reach_min' => 25000,
                'est_reach_max' => 35000,
                'features' => [
                    'Maximum visibility everywhere',
                    'Homepage banner placement',
                    'Email & push notifications',
                    'Social media campaigns',
                    '10x more visibility',
                    'Dedicated account support',
                ],
                'is_popular' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($types as $data) {
            CampaignType::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
