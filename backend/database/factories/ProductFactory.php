<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Reference;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $categoryId = Reference::where('type', 'category')
            ->inRandomOrder()
            ->value('id');

        $unitId = Reference::where('type', 'unit')
            ->inRandomOrder()
            ->value('id');

        $brandId = Reference::where('type', 'brand')
            ->inRandomOrder()
            ->value('id');

        return [
            'name' => fake()->unique()->words(3, true),
            'barcode' => fake()->unique()->ean13(),
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'brand_id' => $brandId,
            'image' => 'default.png',
            'min_quantity' => fake()->numberBetween(0, 20),
            'description' => fake()->optional()->sentence(),
            'status' => true,
        ];
    }
}