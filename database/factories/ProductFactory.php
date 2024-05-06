<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = fake()->unique()->name();
        $slug = Str::slug($title);

        $subCategories=[7,10];
        $subCatRandKey=array_rand($subCategories);

        $brands=[2,3,8,9,10];
        $brandRandKey=array_rand($brands);

        return [
            'title'=>$title,
            'slug'=>$slug,
            'category_id'=>48,
            'sub_category_id'=>$subCategories[$subCatRandKey],
            'brand_id'=>$brands[ $brandRandKey],
            'description'=>fake()->text(100),
            'price'=>rand(35000,100000),
            'sku'=>rand(1000,10000),
            'track_qty'=>'Yes',
            'qty'=>10,
            'is_featured'=>'Yes',
            'status'=>1,
        ];
    }
}
