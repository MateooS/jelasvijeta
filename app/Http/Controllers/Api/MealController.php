<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MealResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HelperClasses\LanguageHelper;
use App\HelperClasses\MainHelper;

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
        LanguageHelper::checkLang($request);

        /* Filter out everything else - Meals, categories, tags, ingredients */
        return MainHelper::filterEverythingElse($request);
    }
}
