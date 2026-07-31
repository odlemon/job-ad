<?php

namespace Database\Seeders;

use App\Models\CoinPackage;
use Illuminate\Database\Seeder;

class CoinPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter Pack',
                'coins_amount' => 50,
                'price' => 250,
                'currency' => 'SCR',
                'description' => 'Perfect for small businesses starting their recruitment journey',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'name' => 'Business Pack',
                'coins_amount' => 150,
                'price' => 600,
                'currency' => 'SCR',
                'description' => 'Best value for growing companies with regular hiring needs',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional Pack',
                'coins_amount' => 300,
                'price' => 1200,
                'currency' => 'SCR',
                'description' => 'Ideal for active recruiters and medium-sized organizations',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise Pack',
                'coins_amount' => 1000,
                'price' => 3500,
                'currency' => 'SCR',
                'description' => 'Maximum value package for large organizations',
                'status' => 'active',
                'sort_order' => 4,
            ],
        ];

        foreach ($packages as $data) {
            CoinPackage::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
