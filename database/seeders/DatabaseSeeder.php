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
use App\Models\MealTags;

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
        for ($lastTagID = 11; $lastTagID < 16; $lastTagID++) {
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
        for ($lastIngrID = 51; $lastIngrID < 56; $lastIngrID++) {
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
        $tagCombArray[0] = array($tags[3]->id, $tags[0]->id, $tags[4]->id);
        $tagCombArray[1] = array($tags[1]->id, $tags[3]->id, $tags[2]->id);
        $tagCombArray[2] = array($tags[0]->id, $tags[1]->id, $tags[2]->id);
        $tagCombArray[3] = array($tags[4]->id, $tags[2]->id);
        $tagCombArray[4] = array($tags[2]->id);

        /* Create 5 new combinations of ingredients */
        $ingrCombArray[0] = array($ingredients[0]->id, $ingredients[1]->id,
            $ingredients[2]->id);
        $ingrCombArray[1] = array($ingredients[1]->id, $ingredients[3]->id,
            $ingredients[2]->id);
        $ingrCombArray[2] = array($ingredients[3]->id, $ingredients[0]->id,
            $ingredients[4]->id);
        $ingrCombArray[3] = array($ingredients[4]->id, $ingredients[2]->id);
        $ingrCombArray[4] = array($ingredients[2]->id);

        /* Generate 4 new categories and meals */
        for ($lastCatID = 1; $lastCatID < 5; $lastCatID++) {
            $lastCategory = Category::create([
                'en' => [
                    'title' => 'This is a category title in English.'
                ],
                'hr' => [
                    'title' => 'Ovo je ime kategorije na HRV jeziku.'
                ],

                'slug' => 'category-'.$lastCatID
            ]);

            $nextMeal = Meal::factory()->create([
                'category_id' => $lastCategory->id,
            ]);
            $nextMeal->tags()->attach($tagCombArray[$lastCatID-1]);
            $nextMeal->ingredients()->attach($ingrCombArray[$lastCatID-1]);
        }

        /* Create a meal that has no category */
        $meal = Meal::factory()->create();
        $meal->tags()->attach($tags[0]->id);
        $meal->ingredients()->attach($ingredients[0]->id);


        /* Seed languages */
        $this->call(LanguageSeeder::class);
        /* Seed changes to the Meal database table for diff_time testing */
        $this->call(ChangeMealsSeeder::class);
    }
}
