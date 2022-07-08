<?php

namespace App\HelperClasses;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Meal;
use App\Models\MealTags;

class MealHelper
{
    /**
     * Get query parameter values if there are any, filter out what we don't
     * need
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getMeals(Request $request): LengthAwarePaginator
    {
        /* Get the date and time */
        $dateTime = DiffTimeHelper::getDiffTime($request);

        /* Get tags */
        $askedTags = TagHelper::getTags($request);

        /**
         * Find out whether 'category' is an int, 'null', or '!null' or not
         * 0 - don't filter with category_id
         * 1 - show NULL
         * 2 - show !NULL
         * 3 - show category_id
         */
        $catIDStatus = CategoryHelper::getStatus($request);

        /* Filter only meals which have the category id that was given */
        $meals = Meal::latest();
        if ($catIDStatus == 0) {
            $meals = $meals;
        } elseif ($catIDStatus == 1) {
            $meals->whereNull('category_id');
        } elseif ($catIDStatus == 2) {
            $meals->whereNotNull('category_id');
        /* $catIDStatus = 3*/
        } else {
            $meals->where(
                'category_id',
                $request['category']
            );
        }


        /**
         * If 'tags' query parameter specified - filter by IDs that have those
         * tags
         */
        if ($askedTags !== '') {
            $mealIDs = array();

            $filteredMeals = MealTags::where('tag_id', 'like', $askedTags)
                ->get();

            /* Extract the meal_ids and put them in an array */
            foreach ($filteredMeals as $filteredMeal) {
                array_push($mealIDs, $filteredMeal->meal_id);
            }

            $meals->withTrashed()->whereIn('id', $mealIDs);
        }

        /**
         * Where created_at, updated_at or deleted_at is greater than $dateTime
         */
        if ($dateTime != "1970-01-01 12:00:01") {
            $meals
                ->where('created_at', '>=', '%'.$dateTime.'%')
                ->orWhere('updated_at', '>=', '%'.$dateTime.'%') 
                ->orWhere('deleted_at', '>=', '%'.$dateTime.'%');
        }

        /**
         * Get a page with per_page or 10 Meal items, and withQueryString for
         * links
         */
        return $meals
            ->paginate($request['per_page'] ?? 10)
            ->withQueryString();
    }

    /**
     * Get response with translated base scheme
     * 
     * @return array
     */
    public static function translateBase(
        Request $request,
        array $response,
        Meal $meal,
        string $status,
        int $i
    ): array {
        /* Translate each individual meal model */
        $translatedMeal = $meal->translate($request['lang']);
        $response['data'][$i]['id'] = $meal->id;
        $response['data'][$i]['title'] = $translatedMeal->title;
        $response['data'][$i]['description'] = $translatedMeal->description;
        $response['data'][$i]['status'] = $status;

        return $response;
    }
}
