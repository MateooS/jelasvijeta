<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Meal;
use App\Models\MealTags;

class ChangeMealsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Changes to show-off the 'status' in the API response
         */
        
        /**
         * Update one of the Meals - for each tag in meal_tags table where the
         * meal_id is 3 - update the tag_id to the next one in the $tagIDs
         */
        $tagIDs = array(11,13,12);
        $mealToModify = Meal::find(3);

        Meal::find(3)->tags()->detach();
        Meal::find(3)->tags()->attach($tagIDs);

        /* Update the timestamp to be greater than other timestamps by 1 sec */
        $mealToModify->update([
            'updated_at' => $mealToModify->updated_at+1
        ]);


        /**
         * Soft delete, modify the timestamp to be greater than other
         * timestamps
         */
        $mealToDelete = Meal::find(5);
        $mealToDelete->delete([
            'deleted_at' => $mealToDelete->deleted_at+1
        ]);
    }
}
