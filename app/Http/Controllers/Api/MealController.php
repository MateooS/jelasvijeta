<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MealResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Meal;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Ingredient;
use App\Models\Language;
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
        'lang'      => 'required|string|min:2|max:4',
        'per_page'  => 'sometimes|integer|numeric',
        'page'      => 'sometimes|integer|numeric',
        'category'  => 'nullable|string',
        'tags'      => 'sometimes|string',
        'with'      => 'sometimes|string|min:3|max:30',
        'diff_time' => 'sometimes|integer|numeric'
      ]);

      /* Handle the error messages that we don't */
      if ($validator->fails())
        return $validator->messages()->first();

      if (!Language::where('lang', $request['lang'])->exists()) {
        echo 'Lang has to be one of the following: ';
        foreach (Language::all('lang') as $lang)
          echo $lang['lang'].' ';
        exit;
      }

      /* Find out whether 'category' is an integer, 'null', or '!null' or not */
      $catIsNull = false;
      $catNotNull = false;
      if ($request['category'])
        if (strToLower($request['category']) == 'null')
          $catIsNull = true;
        else if (strToLower($request['category']) == '!null')
          $catNotNull = true;
        /* If it's not a integer */
        else if (!intval($request['category']))
          exit("Category ID has to be a number, null or !null.");

      $askedTags = '';

      /**
       * Make sure that the input given in 'tags' is a number, when exploded by ','
       */
      if ($request['tags'])
        foreach (explode(',', $request['tags']) as $tag) {
          if (!intval($tag))
            exit("Tags have to be numbers, separate multiple of them with ','.");

          $askedTags .= $tag.'%';
        }
      
      /* Check the 'with' query against columns allowed */
      $columnsAllowed = 'ingredients,category,tag';
      /* Get rid of empty spaces */
      $askingWith = str_replace(' ', '', $request['with']);
      
      $showIngrs = false;
      $showCats = false;
      $showTags = false;

      foreach (explode(',', $askingWith) as $with)
        /* Use stristri to handle both singular and plural word variations */
        if (stristr($with, 'ingredient'))
          $showIngrs = true;
        else if (stristr($with, 'categor'))
          $showCats = true;
        else if (stristr($with, 'tag'))
          $showTags = true;
        /* If $with is specified, but neither of the above */
        else if ($with)
          exit("With can only contain combination of the following values:
            'ingredients, category, tag'");

      if (isset($request['diff_time']))
        if ($request['diff_time'] <= 0)
          exit('Diff_time has to be greater than 0');

      /* Filter only meals which have the category id that was given */
      if (!isset($request['category']))
        $mealsBase = Meal::latest();
      else if ($catIsNull)
        $mealsBase = Meal::latest()->whereNull('category_id');
      else if ($catNotNull)
        $mealsBase = Meal::latest()->whereNotNull('category_id');
      else
        $mealsBase = Meal::latest()->where('category_id', $request['category']);
        
      /* TODO if diff_time is set - get by created_id */

      /**
       * Get a page with per_page or 10 Meal items, and withQueryString for
       * links
       */
       $meals = $mealsBase->withTrashed()->where('tag_ids', 'like', '%'.$askedTags)
        ->paginate($request['per_page'] ?? 10)->withQueryString();

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


      $ingredientsArray = $tagsArray = $categoryArray = array();
      $status = 'created';
      $i = 0;

      /**
       * This is necessary if you don't want the 'translation' array  and other
       * fields to show up and I already spent a long time trying to remove it
       * with functions
       */
      foreach ($meals as $meal) {
        if (isset($request['diff_time'])) {
          /* Determine which of the timestamps is latest */
          $createdAt = $meal->created_at;
          $updatedAt = $meal->updated_at;
          $deletedAt= $meal->deleted_at;
          /* Delete timestamp is bigger or equal to created and updated timestamps */
          if ($deletedAt >= $createdAt && $deletedAt >= $updatedAt)
            $status = 'deleted';
          else if ($updatedAt > $createdAt)
            $status = 'modified';
          else
            $status = 'created';
        }

        /* Translate each individual meal model */
        $translatedMeal = $meal->translate($request['lang']);
        $response['data'][$i]['id'] = $meal->id;
        $response['data'][$i]['title'] = $translatedMeal->title;
        $response['data'][$i]['description'] = $translatedMeal->description;
        $response['data'][$i]['status'] = $status;

        /* Get the category and it's values, if there is one for this meal */
        if ($showCats) {
          $category = Category::find($meal->category_id);
          if ($category) {
            $categoryArray['id'] = $category->id;
            $categoryArray['title'] = $category->translate($request['lang'])
              ->title;
            $categoryArray['slug'] = $category->slug;
          } else
            $categoryArray = null;
          $response['data'][$i]['category'] = $categoryArray;
        }

        if ($showTags) {
          /* Prepare arrays */
          $response['data'][$i]['tags'] = array();

          /* Get the tag IDs for this meal */
          $tags = $meal->tag_ids;
          $tag_ids = explode(',', $tags);

          /* Loop through all tags for this meal and add them to the array */
          for ($j = 0; $j<count($tag_ids); $j++) {
            $tag = Tag::find($tag_ids[$j]);
            $tagsArray[$j] = array();
            $tagsArray[$j]['id'] = $tag->id;
            $tagsArray[$j]['title'] = $tag->translate($request['lang'])->title;
            $tagsArray[$j]['slug'] = $tag->slug;
            array_push($response['data'][$i]['tags'], $tagsArray[$j]);
          }
        }

        if ($showIngrs) {
          $response['data'][$i]['ingredients'] = array();

          /* Get the ingredients IDs for this meal */
          $ingredients = $meal->ingredient_ids;
          $ingr_ids = explode(',', $ingredients);

          /* Loop through all ingredients for this meal and add them to the array */
          for ($j = 0; $j<count($ingr_ids); $j++) {
            $ingredient = Ingredient::find($ingr_ids[$j]);
            $ingrArray[$j] = array();
            $ingrArray[$j]['id'] = $ingredient->id;
            $ingrArray[$j]['title'] = $ingredient->translate($request['lang'])
              ->title;
            $ingrArray[$j]['slug'] = $ingredient->slug;
            array_push($response['data'][$i]['ingredients'], $ingrArray[$j]);
          }
        }

        $i++;
      }
      return $response;
    }
}
