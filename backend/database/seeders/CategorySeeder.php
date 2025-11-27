<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Wash & Fold',
                'description' => 'Basic washing and folding service',
            ],
            [
                'name' => 'Dry Cleaning',
                'description' => 'Professional dry cleaning service',
            ],
            [
                'name' => 'Iron & Press',
                'description' => 'Ironing and pressing service',
            ],
            [
                'name' => 'Express Service',
                'description' => 'Same-day express laundry service',
            ],
            [
                'name' => 'Comforter Cleaning',
                'description' => 'Specialized cleaning for comforters and bedding',
            ],
            [
                'name' => 'Shoe Cleaning',
                'description' => 'Professional shoe and sneaker cleaning',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
