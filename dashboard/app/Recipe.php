<?php

namespace App;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredientss()
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('quantity');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
