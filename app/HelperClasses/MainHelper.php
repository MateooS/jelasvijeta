<?php

namespace App\HelperClasses;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MainHelper 
{
    public static function filterEverythingElse(Request $request)
    {
        /* Get rid of empty spaces */
        $askingWith = str_replace(' ', '', $request['with']);
        $showTags = false;
        $showCats = false;
        $showIngrs = false;
        foreach (explode(',', $askingWith) as $with) {
            /* Use stristri to handle both singular and plural word variations */
            if (stristr($with, 'ingredient')) {
                $showIngrs = true;
            } elseif (stristr($with, 'categor')) {
                $showCats = true;
            } elseif (stristr($with, 'tag')) {
                $showTags = true;
            /* If $with is specified, but neither of the above */
            } elseif ($with) {
                exit("With can only contain combination of the following values:
                    'ingredients, category, tag'");
            }
        }

        /**
         * This is a big function - it calls a few functions on it's own, and
         * returns a LengthAwarePaginator
         */
        $meals = MealHelper::getMeals($request);

        $response = MainHelper::createResponse($request, $meals);
        $i = 0;

        /**
         * This is necessary if you don't want the 'translation' array    and other
         * fields to show up and I already spent a long time trying to remove it
         * with functions
         */
        foreach ($meals as $meal) {
            /* Get Meal status */
            $status = DiffTimeHelper::getStatus($request, $meal);

            /* Translate each individual Meal model */
            $response = MealHelper::translateBase(
                $request,
                $response,
                $meal,
                $status,
                $i
            );

            /* Get the category and it's values, if there is one for this meal */
            if ($showCats) {
                $response = CategoryHelper::getArray(
                    $request,
                    $response,
                    $meal,
                    $i
                );
            }

            /* Get the tags and their values */
            if ($showTags) {
                $response = TagHelper::getTagsArray($request, $response, $meal, $i);
            }

            /* Get the ingredients and their values */
            if ($showIngrs) {
                $response = IngredientHelper::getIngredientsArray($request, $response, $meal, $i);
            }

            $i++;
        }

        return $response;
    }

    /**
     * Create the response array
     * 
     * @return array
     */
    private static function createResponse(
        Request $request,
        LengthAwarePaginator $meals
    ): array {
        $response = array();
        $response['links'] = $response['data'] = $response['meta'] = array();
        /**
         * Format everything nicely. I know it's ugly code-vise, but it gives
         * excellent control over the output
         */
        $response['meta']['currentPage'] = $meals->currentPage();
        $response['meta']['totalItems'] = $meals->total();
        $response['meta']['itemsPerPage'] = $meals->perPage();
        $response['meta']['totalPages'] = round(
            $meals->total()/
            $meals->perPage()
        );

        $response['links']['prev'] = $meals->previousPageUrl();
        $response['links']['next'] = $meals->nextPageUrl();
        $response['links']['self'] = $meals->url($request['page']);

        return $response;
    }
}
