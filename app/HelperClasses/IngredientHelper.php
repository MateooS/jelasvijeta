<?php

namespace App\HelperClasses;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Ingredient;

class IngredientHelper
{
    /**
     * Get ingredients array
     * 
     * @return array
     */
    public static function getIngredientsArray(
        Request $request,
        array $response,
        Meal $meal,
        int $i
    ): array {
        $response['data'][$i]['ingredients'] = array();

        /* Get the ingredients for this meal */
        $ingredients = $meal->ingredients;

        /* Loop through all ingredients for this meal and add them to the array */
        for ($j = 0; $j < count($ingredients); $j++) {
            $ingredient = $ingredients[$j];
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
