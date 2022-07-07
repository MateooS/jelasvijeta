<?php
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use App\Models\Language;
use App\Models\Meal;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Ingredient;

/**
 * Check diff_time, set $dateTime accordingly
 * 
 * @return lluminate\Http\Request 
 */
if (!function_exists('checkDiffTime')) {
    function getDiffTime(Request $request): Request
    {
        if (isset($request['diff_time'])) {
            if ($request['diff_time'] <= 0) {
                exit('Diff time has to be greater than 0');
            } else {
                $dateTime = date('Y-m-d h:i:s', $request['diff_time']);
            }
        } else {
            /* Display every item */
            $dateTime = date('Y-m-d h:i:s', 1);
        }
        return $dateTime;
    }
}

/**
 * Check if lang is in database
 */
if (!function_exists('checkLang')) {
    function checkLang(Request $request)
    {
        if (!Language::where('lang', $request['lang'])->exists()) {
            echo 'Lang has to be one of the following: ';
            foreach (Language::all('lang') as $lang) {
                echo $lang['lang'].' ';
            }
            exit;
        }
    }
}

/**
 * Check category, set $catIDStatus accordingly
 * 
 * @return int
 */
if (!function_exists('getCatStatus')) {
    function getCatStatus(Request $request): int
    {
        if ($request['category']) {
            if (strToLower($request['category']) == 'null') {
                return 1;
            } elseif (strToLower($request['category']) == '!null') {
                return 2;
            /* If it's not a int */
            } elseif (!intval($request['category'])) {
                exit("Category ID has to be a number, null or !null.");
            } else
                return 3;
        } else {
            return 0;
        }
    }
}

/**
 * Make sure that the input given in 'tags' is a number, when exploded by ','
 * 
 * @return string
 */
if (!function_exists('getTags')) {
    function getTags (Request $request): string
    {
        $askedTags = '';
        if ($request['tags']) {
            foreach (explode(',', $request['tags']) as $tag) {
                if (!intval($tag)) {
                    exit("Tags have to be numbers, separate multiple of them with ','.");
                }
                $askedTags .= $tag.'%';
            }
        }
        return $askedTags;
    }
}

/**
 * Get query parameter values if there are any, filter out what we don't need
 * 
 * @return Illuminate\Pagination\LengthAwarePaginator
 */
if (!function_exists('getMeals')) {
    function getMeals(Request $request)
    {
        /* Get the date and time */
        $dateTime = getDiffTime($request);

        /* Get tags */
        $askedTags = getTags($request);

        /**
         * Find out whether 'category' is an int, 'null', or '!null' or not
         * 0 - don't show cat
         * 1 - show NULL
         * 2 - show !NULL
         * 3 - show category_id
         */
        $catIDStatus = getCatStatus($request);

        /* Filter only meals which have the category id that was given */
        if ($catIDStatus == 0) {
            $mealsBase = Meal::latest();
        } elseif ($catIDStatus == 1) {
            $mealsBase = Meal::latest()->whereNull('category_id');
        } elseif ($catIDStatus == 2) {
            $mealsBase = Meal::latest()->whereNotNull('category_id');
        /* $catIDStatus = 3*/
        } else {
            $mealsBase = Meal::latest()->where(
                'category_id',
                $request['category'
            ]);
        }
            
        $meals = $mealsBase
            ->withTrashed()
            ->where('tag_ids', 'like', '%'.$askedTags);

        /* Where created_at, updated_at or deleted_at is greater than $dateTime */
        if ($dateTime != "1970-01-01 12:00:01") {
            $meals = $meals
            ->where('created_at', '>=', $dateTime)
            ->orWhere('updated_at', '>=', $dateTime) 
            ->orWhere('deleted_at', '>=', $dateTime);
        }

        /**
         * Get a page with per_page or 10 Meal items, and withQueryString for
         * links
         */
        $meals = $meals
         ->paginate($request['per_page'] ?? 10)
         ->withQueryString();
         
        return $meals;
    }
}

/**
 * Create the response array
 * 
 * @return array
 */
if (!function_exists('createResponse')) {
    function createResponse(Request $request, LengthAwarePaginator $meals): array
    {
        $response = array();
        $response['links'] = $response['data'] = $response['meta'] = array();
        /**
         * Format everything nicely. I know it's ugly code-vise, but it gives
         * excellent control over the output
         */
        $response['meta']['currentPage'] = $meals->currentPage();
        $response['meta']['totalItems'] = $meals->total();
        $response['meta']['itemsPerPage'] = $meals->perPage();
        $response['meta']['totalPages'] = round($meals->total()/$meals->perPage());

        $response['links']['prev'] = $meals->previousPageUrl();
        $response['links']['next'] = $meals->nextPageUrl();
        $response['links']['self'] = $meals->url($request['page']);

        return $response;
    }
}

/**
 * Get meal's timestamps and set the status accordingly
 * 
 * @return string
 */
if (!function_exists('getStatus')) {
    function getStatus(Request $request, Model $meal): string
    {
        if (isset($request['diff_time'])) {
            /* Determine which of the timestamps is latest */
            $createdAt = $meal->created_at;
            $updatedAt = $meal->updated_at;
            $deletedAt= $meal->deleted_at;
            /**
             * Delete timestamp is bigger or equal to created and updated
             * timestamps */
            if ($deletedAt >= $createdAt && $deletedAt >= $updatedAt) {
                $status = 'deleted';
            } elseif ($updatedAt > $createdAt) {
                $status = 'modified';
            } else {
                $status = 'created';
            }
        } else {
            $status = 'created';
        }
        return $status;
    }
}

/**
 * Get response with translated base scheme
 * 
 * @return array
 */
if (!function_exists('translateBase')) {
    function translateBase(
        Request $request,
        array $response,
        Model $meal,
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

/**
 * Get category array
 * 
 * @return array
 */
if (!function_exists('getCatArray')) {
    function getCatArray(
        Request $request,
        array $response,
        Model $meal,
        int $i
    ): array {
        $category = Category::find($meal->category_id);
        $categoryArray = array();

        if ($category) {
            $categoryArray['id'] = $category->id;
            $categoryArray['title'] = $category->translate($request['lang'])
                ->title;
            $categoryArray['slug'] = $category->slug;
        } else {
            $categoryArray = null;
        }

        $response['data'][$i]['category'] = $categoryArray;
        return $response;
    }
}

/**
 * Get tags array
 * 
 * @return array
 */
if (!function_exists('getTagsArray')) {
    function getTagsArray(
        Request $request,
        array $response,
        Model $meal,
        int $i
    ): array {
        /* Prepare arrays */
        $response['data'][$i]['tags'] = array();

        /* Get the tag IDs for this meal */
        $tags = $meal->tag_ids;
        $tag_ids = explode(',', $tags);

        /* Loop through all tags for this meal and add them to the array */
        for ($j = 0; $j < count($tag_ids); $j++) {
            $tag = Tag::find($tag_ids[$j]);
            $tagsArray[$j] = array();
            $tagsArray[$j]['id'] = $tag->id;
            $tagsArray[$j]['title'] = $tag->translate($request['lang'])->title;
            $tagsArray[$j]['slug'] = $tag->slug;
            array_push($response['data'][$i]['tags'], $tagsArray[$j]);
        }
        return $response;
    }
}

/**
 * Get ingredients array
 * 
 * @return array
 */
if (!function_exists('getIngredientsArray')) {
    function getIngredientsArray(
        Request $request,
        array $response,
        Model $meal,
        int $i
    ): array {
        $response['data'][$i]['ingredients'] = array();

        /* Get the ingredients IDs for this meal */
        $ingredients = $meal->ingredient_ids;
        $ingr_ids = explode(',', $ingredients);

        /* Loop through all ingredients for this meal and add them to the array */
        for ($j = 0; $j < count($ingr_ids); $j++) {
            $ingredient = Ingredient::find($ingr_ids[$j]);
            $ingrArray[$j] = array();
            $ingrArray[$j]['id'] = $ingredient->id;
            $ingrArray[$j]['title'] = $ingredient->translate($request['lang'])
                ->title;
            $ingrArray[$j]['slug'] = $ingredient->slug;
            array_push($response['data'][$i]['ingredients'], $ingrArray[$j]);
        }
        return $response;
    }
}
