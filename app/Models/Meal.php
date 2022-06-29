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

    protected $casts = [
      'created_at' => 'timestamp',
      'updated_at' => 'timestamp',
      'deleted_at' => 'timestamp'
    ];

    protected $fillable = ['category_id', 'tag_ids'];

    public $translatedAttributes = ['title'];

    
    /* Relationships */
    public function category() {
      return $this->hasOne(Category::class, ['category_id']);
    }

    public function tags() {
      return $this->hasMany(Tag::class, ['tag_ids']);
    }

    public function ingredient() {
      return $this->hasMany(Ingredient::class, ['ingredient_ids']);
    }
}
