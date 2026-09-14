<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        // Demo gateway that auto-approves payments (used for testing).
        PaymentGateway::firstOrCreate(
            ['slug' => 'demo'],
            [
                'name' => 'Demo Gateway',
                'slug' => 'demo',
                'merchant_id' => 'DEMO0001',
                'access_code' => 'demo-access-code',
                'working_key' => 'demo-working-key',
                'endpoint' => url('/api/payment/demo/return'),
                'is_default' => true,
                'is_active' => true,
            ]
        );

        // Referral-style CCAvenue gateway (admin should edit credentials in admin panel).
        PaymentGateway::firstOrCreate(
            ['slug' => 'ccavenue'],
            [
                'name' => 'CCAvenue',
                'slug' => 'ccavenue',
                'merchant_id' => '',
                'access_code' => '',
                'working_key' => '',
                'endpoint' => 'https://secure.ccavenue.ae',
                'is_default' => false,
                'is_active' => false,
            ]
        );
    }
}