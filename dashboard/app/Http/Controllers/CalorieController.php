<?php

namespace App\Http\Controllers;

use App\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CalorieController extends Controller
{
    public function calculatorCalorie($id)
    {
        $recipe = Recipe::with('ingredientss')->findOrFail($id);

        $totalCalories = 0;
        foreach ($recipe->ingredientss as $ingredient) {
            $caloriesForIngredient = ($ingredient->pivot->quantity / 100) * $ingredient->calories;
            $totalCalories += $caloriesForIngredient;
        }
        //$recipe->calories = $totalCalories;
        //$recipe->save();
        return $totalCalories;
    }

}
