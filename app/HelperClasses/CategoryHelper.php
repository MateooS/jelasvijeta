<?php

namespace App\HelperClasses;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\Meal;
use App\Models\Category;

class CategoryHelper
{
    /**
     * Check category, set $catIDStatus accordingly
     *
     * @return int
     */
    public static function getStatus(Request $request): int
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

    /**
     * Get category array
     * 
     * @return array
     */
    public static function getArray(
        Request $request,
        array $response,
        Meal $meal,
        int $i
    ): array {
        $category = $meal->category;
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
