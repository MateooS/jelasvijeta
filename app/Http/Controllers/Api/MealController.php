<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MealResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Meal;
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
      /* TODO probably should be in a separate file */
      $validator = Validator::make($request->all(), [
        'lang' =>      'required|string|min:2|max:4',
        'per_page' =>  'sometimes|integer|numeric',
        'page' =>      'sometimes|integer|numeric',
        'category' =>  'nullable|integer|numeric',
        'tags.*' =>    'sometimes|integer|numeric',
        'with' =>      'sometimes|string|min:3|max:30',
        'diff_time' => 'sometimes|integer|numeric',
      ]);

      $i = 0;
      $response = array();

      if ($request['tags']) {
        foreach (explode(',', $request['tags']) as $tag) {
          if (!intval($tag))
            dd("Invalid format of tags, it has to be numbers separated by ','");
          /* TODO do something */

          $response['tags'][$i] = $tag;
          $i++;
        }
      }
      
      if ($validator->fails())
        return $validator->messages()->first();

      dd($request->all());

      /* Change locale to a given lang, if doesn't exist fallback to EN */
      App::setLocale($request['lang']);

      $meals = Meal::latest()->paginate($request['per_page'] ?? 10);
      return MealResource::collection($response);
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
