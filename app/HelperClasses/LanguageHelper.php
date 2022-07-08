<?php

namespace App\HelperClasses;

use Illuminate\Http\Request;
use App\Models\Language;

class LanguageHelper
{
    /**
     * Check if lang is in database
     */
    public static function checkLang(Request $request)
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
