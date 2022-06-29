<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Meal;

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
       * Update one of the Meals, and modify the timestamp to be greater than
       * other timestamps
       */
      Meal::find(3)->update([
        'tag_ids' => '11,13,12',
        'updated_at' => Meal::find(3)->updated_at+1
      ]);

      /* Soft delete, modify the timestamp to be greater than other timestamps */
      Meal::find(5)->delete([
        'deleted_at' => Meal::find(5)->deleted_at+1
      ]);

    }
}
