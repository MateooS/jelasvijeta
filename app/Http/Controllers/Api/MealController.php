<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MealResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Meal;
use App\Models\Category;
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
        'category'  => 'nullable|integer|numeric',
        'tags'      => 'sometimes|string',
        'with'      => 'sometimes|string|min:3|max:30',
        'diff_time' => 'sometimes|integer|numeric',
      ]);

      if ($validator->fails())
        return $validator->messages()->first();

      $i = 0;
      $tags = array();

      /**
       * Make sure that the input given in 'tags' is a number, when exploded by ','
       */
      if ($request['tags'])
        foreach (explode(',', $request['tags']) as $tag) {
          if (!intval($tag)) {
            echo "Tags have to be numbers, separate multiple of them with ','.";
            exit;
          }

          $tags[$i] = $tag;
          $i++;
        }
      

      /* TODO implement the 'languages' database table */

      /**
       * Get a page with per_page or 10 Meal items, and withQueryString for
       * links
       */
      $meals = Meal::latest()->paginate($request['per_page'] ?? 10)
        ->withQueryString();
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


      $i = 0;
      $categoryArray = array();
      /**
       * This is necessary if you don't want the 'translation' array to show up
       * and I already spent a long time trying to remove it with functions
       */
      foreach ($meals as $meal) {
        /* Translate each individual meal model */
        //$translatedMeal = $meal->translate($request['lang']);
        $translatedMeal = $meal->translate($request['lang']);
        $response['data'][$i]['id'] = $meal->id;
        $response['data'][$i]['title'] = $translatedMeal->title;
        $response['data'][$i]['description'] = $translatedMeal->description;

        $category = Category::find($meal->category_id);
        if ($category) {
          $categoryArray['id'] = $category->id;
          $categoryArray['title'] = $category->translate($request['lang'])->title;
          $categoryArray['slug'] = $category->slug;
        } else
          $categoryArray = null;
        $response['data'][$i]['category'] = $categoryArray;
        $i++;
      }

      return $response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function show(Meal $meal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function edit(Meal $meal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meal $meal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meal $meal)
    {
        //
    }
}
