<?php

namespace App\Http\Controllers;

use App\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('ingredients.index',[
            'ingredients' => Ingredient::orderBy('created_at','DESC')->get()
        ]);
    }

    public function create()
    {
        return view('ingredients.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'name' => ['required','string'],
            'quantity' => ['required','string'],
            'metric' => ['required', 'string'],
            'calories' => ['required','decimal']
        ]);

        $validatedData = $validated->getData();

        //dd($validatedData['name']);
        Ingredient::create([
            'name' => $validatedData['name'],
            'quantity' => $validatedData['quantity'],
            'metric' => $validatedData['metric'],
            'calories' => $validatedData['calories']
        ]);

        return to_route('ingredients.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        return \response()->json([
            'message' => 'Liste des ingrédients',
            'ingredient' => $ingredient
        ], Response::HTTP_OK);
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit',[
            'ingredient' => $ingredient
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = Validator::make($request->all(),[
            'name' => ['required','string'],
            'quantity' => ['required','string'],
            'metric' => ['required', 'string'],
            'calories' => ['required','decimal']
        ]);
        $ingredient = Ingredient::find($id);
        $ingredient->update($request->all());
        return to_route('ingredients.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return to_route('ingredients.index')->with('success', 'L\'ingredient a bien été supprimé');
    }

    public function getCalorie()
    {

    }
}
