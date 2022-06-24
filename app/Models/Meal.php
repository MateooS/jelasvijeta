<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Ingredient;

class Meal extends Model implements TranslatableContract
{
    use Translatable;
    use HasFactory;
    use SoftDeletes;

    /* Specify that these are arrays as we will be storing tags' and
     * ingredients' ID's in arrays because multiple of them are allowed.
     */
    protected $casts = [
      'tag_ids' => 'array',
      'ingredient_ids' => 'array'
    ];

    //protected $fillable = ['title'];

    public $translatedAttributes = ['title'];

    
    /* Relationships */
    public function category() {
      return $this->hasOne(Category::class, ['category_id']);
    }

    public function tag() {
      return $this->hasMany(Tag::class, ['tag_ids']);
    }

    public function ingredient() {
      return $this->hasMany(Ingredient::class, ['ingredient_ids']);
    }
}
