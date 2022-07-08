<?php

namespace App\HelperClasses;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Tag;
use App\Models\MealTags;

class TagHelper
{
    /**
     * Get tags array
     * 
     * @return array
     */
    public static function getTagsArray(
        Request $request,
        array $response,
        Meal $meal,
        int $i
    ): array {
        /* Prepare arrays */
        $response['data'][$i]['tags'] = array();

        /* Get the tag IDs for this meal */
        $tags = $meal->tags;

        /* Loop through all tags for this meal and add them to the array */
        for ($j = 0; $j < count($tags); $j++) {
            $tag = $tags[$j];
            $tagsArray[$j] = array();
            $tagsArray[$j]['id'] = $tag->id;
            $tagsArray[$j]['title'] = $tag->translate($request['lang'])->title;
            $tagsArray[$j]['slug'] = $tag->slug;
            array_push($response['data'][$i]['tags'], $tagsArray[$j]);
        }
        return $response;
    }

    /**
     * Make sure that the input given in 'tags' is a number, when exploded by ','
     * 
     * @return array
     */
    public static function getTags (Request $request): array
    {
        $askedTags = array();
        if ($request['tags']) {
            foreach (explode(',', $request['tags']) as $tag) {
                if (!intval($tag)) {
                    exit(
                        "Tags have to be numbers, separate multiple of them
                        with ','."
                    );
                }
                array_push($askedTags, $tag);
            }
        }
        return $askedTags;
    }
}
