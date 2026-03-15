<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing global gateways to avoid duplicates
        PaymentGateway::whereNull('user_id')->delete();

        // Seed SonicPesa Gateway (no API keys - each user sets their own)
        PaymentGateway::create([
            'name'         => 'sonicpesa',
            'display_name' => 'SonicPesa',
            'is_active'    => true,
            'description'  => 'SonicPesa Payment Gateway - USSD payments for Tanzania',
        ]);

        // Seed Snippe Gateway (no API keys - each user sets their own)
        PaymentGateway::create([
            'name'         => 'snippe',
            'display_name' => 'Snippe',
            'is_active'    => false,
            'description'  => 'Snippe Payment Gateway - Mobile money payments',
        ]);

        // Seed FastLipa Gateway (no API keys - each user sets their own)
        PaymentGateway::create([
            'name'         => 'fastlipa',
            'display_name' => 'FastLipa',
            'is_active'    => false,
            'description'  => 'FastLipa Payment Gateway - Fast mobile money payments for Tanzania',
        ]);
    }
}
