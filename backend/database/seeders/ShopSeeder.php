<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create shop owners
        $owner1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@laundryshop.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'phone' => '9876543210',
            'address' => '123 Main Street',
            'is_active' => true,
        ]);

        $owner2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@cleanpro.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'phone' => '9876543211',
            'address' => '456 Oak Avenue',
            'is_active' => true,
        ]);

        $owner3 = User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike@freshlaundry.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'phone' => '9876543212',
            'address' => '789 Pine Road',
            'is_active' => true,
        ]);

        // Create shops
        Shop::create([
            'owner_id' => $owner1->id,
            'shop_name' => 'Clean & Fresh Laundry',
            'description' => 'Your trusted neighborhood laundry service with 10 years of experience',
            'address' => '123 Main Street, Downtown',
            'contact_number' => '555-0101',
        ]);

        Shop::create([
            'owner_id' => $owner2->id,
            'shop_name' => 'CleanPro Laundromat',
            'description' => 'Professional cleaning services with eco-friendly products',
            'address' => '456 Oak Avenue, Uptown',
            'contact_number' => '555-0102',
        ]);

        Shop::create([
            'owner_id' => $owner3->id,
            'shop_name' => 'Fresh Wash Express',
            'description' => 'Fast and reliable laundry services with same-day delivery',
            'address' => '789 Pine Road, Midtown',
            'contact_number' => '555-0103',
        ]);

        // Create sample customer
        User::create([
            'name' => 'Sarah Williams',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '5551234567',
            'address' => '321 Elm Street',
            'is_active' => true,
        ]);
    }
}
