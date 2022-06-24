<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Meal;

class Ingredient extends Model
{
    use HasFactory;

    
    /* Relationships */
    //public function meal() {
    //  return $this->belongsToMany(Meal::class, 'ingredient_ids');
    //}
}
