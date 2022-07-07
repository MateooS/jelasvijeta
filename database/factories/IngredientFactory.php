<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'en' => [
                'title' => 'Dummy title'
            ],
            'hr' => [
                'title' => 'Lazni titl'
            ],
            
                'slug' => $this->faker->slug()
        ];
    }
}
