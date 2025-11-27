<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = Shop::all();
        $categories = Category::all();

        if ($shops->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('No shops or categories found. Please run ShopSeeder and CategorySeeder first.');
            return;
        }

        // Services for Clean & Fresh Laundry (Shop 1)
        $shop1 = $shops->first();
        Service::create([
            'shop_id' => $shop1->id,
            'category_id' => $categories->where('name', 'Wash & Fold')->first()->id,
            'service_name' => 'Regular Wash & Fold',
            'description' => 'Standard washing, drying, and folding service',
            'price' => 150.00,
        ]);

        Service::create([
            'shop_id' => $shop1->id,
            'category_id' => $categories->where('name', 'Dry Cleaning')->first()->id,
            'service_name' => 'Suit Dry Cleaning',
            'description' => 'Professional dry cleaning for suits and formal wear',
            'price' => 250.00,
        ]);

        Service::create([
            'shop_id' => $shop1->id,
            'category_id' => $categories->where('name', 'Express Service')->first()->id,
            'service_name' => 'Express Wash (Same Day)',
            'description' => 'Same-day express washing service',
            'price' => 200.00,
        ]);

        // Services for CleanPro Laundromat (Shop 2)
        if ($shops->count() > 1) {
            $shop2 = $shops->skip(1)->first();
            Service::create([
                'shop_id' => $shop2->id,
                'category_id' => $categories->where('name', 'Wash & Fold')->first()->id,
                'service_name' => 'Eco-Friendly Wash',
                'description' => 'Environmentally friendly washing with organic detergent',
                'price' => 180.00,
            ]);

            Service::create([
                'shop_id' => $shop2->id,
                'category_id' => $categories->where('name', 'Comforter Cleaning')->first()->id,
                'service_name' => 'Comforter Deep Clean',
                'description' => 'Deep cleaning service for comforters and blankets',
                'price' => 400.00,
            ]);

            Service::create([
                'shop_id' => $shop2->id,
                'category_id' => $categories->where('name', 'Iron & Press')->first()->id,
                'service_name' => 'Premium Ironing',
                'description' => 'Professional ironing and pressing service',
                'price' => 120.00,
            ]);
        }

        // Services for Fresh Wash Express (Shop 3)
        if ($shops->count() > 2) {
            $shop3 = $shops->skip(2)->first();
            Service::create([
                'shop_id' => $shop3->id,
                'category_id' => $categories->where('name', 'Express Service')->first()->id,
                'service_name' => 'Express Dry Clean',
                'description' => 'Same-day express dry cleaning',
                'price' => 300.00,
            ]);

            Service::create([
                'shop_id' => $shop3->id,
                'category_id' => $categories->where('name', 'Shoe Cleaning')->first()->id,
                'service_name' => 'Sneaker Restoration',
                'description' => 'Professional sneaker and shoe cleaning service',
                'price' => 230.00,
            ]);

            Service::create([
                'shop_id' => $shop3->id,
                'category_id' => $categories->where('name', 'Wash & Fold')->first()->id,
                'service_name' => 'Deluxe Wash Package',
                'description' => 'Premium washing with fabric softener and special care',
                'price' => 190.00,
            ]);
        }
    }
}
