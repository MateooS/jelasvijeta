<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
      /* Get a sentence and remove the dot at the end so it looks nicer */
      $ENtitle = str_replace('.', '', $this->faker->sentence(3));
      $HRtitle = str_replace('.', '', $this->faker->sentence(3));
      return [
        'en' => [
          'title' => $ENtitle,
          'description' => $ENtitle.' is a mighty fine meal.'
        ],
        'hr' => [
          'title' => $HRtitle,
          'description' => $HRtitle.' je vrlo ukusno jelo.'
        ],
      ];
    }
}
