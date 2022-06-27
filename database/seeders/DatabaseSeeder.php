<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Meal;
use App\Models\Category;
use App\Models\Tag;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      $tags = array();
      $tagCombArray = array();

      /* Generate new tags */
      for ($lastTagID = 1; $lastTagID<6; $lastTagID++) {
        $tags[$lastTagID-1] = Tag::create([
          'en' => [
            'title' => 'This is a tag title in English.'
          ],
          'hr' => [
            'title' => 'Ovo je ime taga na HRV jeziku.'
          ],

          'slug' => 'tag-'.$lastTagID
        ]);
      }

      /* Create 5 new combinations of 3 tags each */
      $tagCombArray[0] = $tags[0]->id.','.$tags[1]->id.','.$tags[2]->id;
      $tagCombArray[1] = $tags[1]->id.','.$tags[3]->id.','.$tags[2]->id;
      $tagCombArray[2] = $tags[3]->id.','.$tags[0]->id.','.$tags[5]->id;
      $tagCombArray[3] = $tags[4]->id.','.$tags[2]->id.','.$tags[1]->id;
      $tagCombArray[4] = $tags[2]->id.','.$tags[1]->id.','.$tags[5]->id;

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
          'tag_ids' => $tagCombArray[$lastCatID-1]
        ]);
      }

      /* Create a meal that has no category */
      Meal::factory()->create([
        'tag_ids' => $tagCombArray[4]
      ]);
    }
}
