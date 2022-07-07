<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements TranslatableContract
{
    use Translatable;
    use HasFactory;

    protected $fillable = ['slug'];
    public $translatedAttributes = ['title'];

    /* Relationships */
    public function meal() {
        return $this->hasMany(Meal::class);
    }
}
