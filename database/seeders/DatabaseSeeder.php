<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\ChangeMealsSeeder;
use App\Models\Meal;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Ingredient;
use App\Models\Language;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      $ingredients = $tags = array();
      $ingrCombArray = $tagCombArray = array();

      Tag::factory(10)->create();
      Ingredient::factory(50)->create();

      /* Generate new tags */
      for ($lastTagID = 11; $lastTagID<16; $lastTagID++) {
        $tags[$lastTagID-11] = Tag::create([
          'en' => [
            'title' => 'This is a tag title in English.'
          ],
          'hr' => [
            'title' => 'Ovo je ime taga na HRV jeziku.'
          ],

          'slug' => 'tag-'.$lastTagID
        ]);
      }

      /* Generate new ingredients*/
      for ($lastIngrID = 51; $lastIngrID<56; $lastIngrID++) {
        $ingredients[$lastIngrID-51] = Ingredient::create([
          'en' => [
            'title' => 'This is a ingredient title in English.'
          ],
          'hr' => [
            'title' => 'Ovo je ime sastojka na HRV jeziku.'
          ],

          'slug' => 'ingredient-'.$lastIngrID
        ]);
      }

      /* Create 5 new combinations of tags */
      $tagCombArray[0] = $tags[3]->id.','.$tags[0]->id.','.$tags[4]->id;
      $tagCombArray[1] = $tags[1]->id.','.$tags[3]->id.','.$tags[2]->id;
      $tagCombArray[2] = $tags[0]->id.','.$tags[1]->id.','.$tags[2]->id;
      $tagCombArray[3] = $tags[4]->id.','.$tags[2]->id;
      $tagCombArray[4] = $tags[2]->id;

      /* Create 5 new combinations of ingredients */
      $ingrCombArray[0] = $ingredients[0]->id.','.$ingredients[1]->id.','.
        $ingredients[2]->id;
      $ingrCombArray[1] = $ingredients[1]->id.','.$ingredients[3]->id.','.
        $ingredients[2]->id;
      $ingrCombArray[2] = $ingredients[3]->id.','.$ingredients[0]->id.','.
        $ingredients[4]->id;
      $ingrCombArray[3] = $ingredients[4]->id.','.$ingredients[2]->id;
      $ingrCombArray[4] = $ingredients[2]->id;

      /* Generate 4 new categories and meals */
      for ($lastCatID = 1; $lastCatID<5; $lastCatID++) {
        $lastCategory = Category::create([
          'en' => [
            'title' => 'This is a category title in English.'
          ],
          'hr' => [
            'title' => 'Ovo je ime kategorije na HRV jeziku.'
          ],

          'slug' => 'category-'.$lastCatID
        ]);

        Meal::factory()->create([
          'category_id' => $lastCategory->id,
          'tag_ids' => $tagCombArray[$lastCatID-1],
          'ingredient_ids' => $ingrCombArray[$lastCatID-1]
        ]);
      }

      /* Create a meal that has no category */
      Meal::factory()->create([
        'tag_ids' => $tagCombArray[4],
          'ingredient_ids' => $ingrCombArray[4]
      ]);

      /* Seed languages */
      $this->call(LanguageSeeder::class);
      $this->call(ChangeMealsSeeder::class);
    }
}
