<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MealResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /* Validate the query */
        $validator = Validator::make($request->all(), [
            'lang'        => 'required|string|min:2|max:4',
            'per_page'    => 'sometimes|integer|numeric',
            'page'        => 'sometimes|integer|numeric',
            'category'    => 'nullable|string',
            'tags'        => 'sometimes|string',
            'with'        => 'sometimes|string|min:3|max:30',
            'diff_time'   => 'sometimes|integer|numeric'
        ]);

        /* Handle the error messages that we don't */
        if ($validator->fails()) {
            return $validator->messages()->first();
        }

        /* Check if the language is one from the Languages database table */
        checkLang($request);

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
        $meals = getMeals($request);

        $response = createResponse($request, $meals);
        $i = 0;

        /**
         * This is necessary if you don't want the 'translation' array    and other
         * fields to show up and I already spent a long time trying to remove it
         * with functions
         */
        foreach ($meals as $meal) {
            /* Get Meal status */
            $status = getStatus($request, $meal);

            /* Translate each individual Meal model */
            $response = translateBase($request, $response, $meal, $status, $i);

            /* Get the category and it's values, if there is one for this meal */
            if ($showCats) {
                $response = getCatArray($request, $response, $meal, $i);
            }

            /* Get the tags and their values */
            if ($showTags) {
                $response = getTagsArray($request, $response, $meal, $i);
            }

            /* Get the ingredients and their values */
            if ($showIngrs) {
                $response = getIngredientsArray($request, $response, $meal, $i);
            }

            $i++;
        }

        return $response;
    }
}
