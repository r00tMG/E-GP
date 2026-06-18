<?php

namespace App\Http\Controllers;

use App\Annonce;
use App\Category;
use App\Ingredient;
use App\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        #$annonces = Recipe::with('ingredientss')->with('categories')->orderBy('created_at', 'DESC')->get();
        $annonces = Annonce::with('gp')->get();
        #dd($annonces);
        return view('annonces.index',[
            'annonces' => $annonces
        ]);
    }

    public function create()
    {
        $ingredients = Ingredient::all();

        return view('recipes.create',[
            'ingredients' => $ingredients,
            'categories' => Category::all()

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'image' => 'required|file',
            'summary' => 'required|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|string|max:255',
            'ingredients.*.metric' => 'required|string|max:255',
            'ingredients.*.calories' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ];
        $validated = Validator::make($request->all(),$rules);
        $validatedData = $validated->getData();
        //dd($validatedData);
        if ($request->hasFile('image')) {
            if ($validatedData['image'] && Storage::exists($validatedData['image'])) {
                Storage::delete($validatedData['image']);
            }
            $validatedData['image'] = $request->file('image')->store('images');
        }
        //dd($request->all());
        $recipe = Recipe::create([
            'title' => $validatedData['title'],
            'image' => $validatedData['image'],
            'summary' => $validatedData['summary'],
            'instructions' => $request->instructions,
            'nutrition' => $request->nutrition,
            'video' => $request->video,
            'prepTime' => $request->prepTime,
            'cookTime' => $request->cookTime,
        ]);

        $ingredients = [];
        foreach ($validatedData['ingredients'] as $ingredientData) {
            $ingredient = Ingredient::firstOrCreate([
                'name' => $ingredientData['name'],
                //'quantity' => $ingredientData['quantity'],
                'metric' => $ingredientData['metric'],
                'calories' => $ingredientData['quantity']*$ingredientData['calories']
                ]);
            $ingredients[$ingredient->id] = ['quantity' => $ingredientData['quantity']];
        }
            //dd($ingredient);
        $recipe->ingredientss()->attach($ingredients);

        $recipe->categories()->attach($validatedData['categories']);

        return to_route('annonces.index')->with("success", "Recipe Added bu Success");
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        #dd($id);
        $annonce = Annonce::with('gp')->find($id);
        #dd($annonce);
        return view('annonces.show', [
            'annonce' => $annonce
        ], );
    }

    public function edit(string $id)
    {
        #dd($id);
        $annonce = Annonce::with('gp')->find($id);
        return view('annonces.edit', [
            'annonce' => $annonce
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'image' => 'required|file',
            'summary' => 'required|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|string|max:255',
            'ingredients.*.metric' => 'required|string|max:255',
            'ingredients.*.calories' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = implode('\\n', $errors);

            return response()->make("<script>
                alert('Bad request: $errorMessage');
                window.history.back();
            </script>");
        }

        $validatedData = $validator->validated();
        $recipe = Recipe::with('ingredientss')->with('categories')->find($id);

        logger('recipe with ingredients',['recipe'=>$recipe]);
        try {
            DB::beginTransaction();
            if ($request->hasFile('image')) {
                if ($recipe->image && Storage::exists($recipe->image)) {
                    Storage::delete($recipe->image);
                }
                $validatedData['image'] = $request->file('image')->store('images');
            } else {
                $validatedData['image'] = $recipe->image;
            }

            $recipe->update([
                'title' => $validatedData['title'],
                'image' => $validatedData['image'],
                'summary' => $validatedData['summary'],
            ]);
            logger('recipe ', ['recipe'=>$recipe]);
            $recipe->ingredientss()->detach();
            logger('recipe detached', ['recipe'=>$recipe]);

            $ingredients = [];

            foreach ($validatedData['ingredients'] as $ingredientData) {
                $ingredient = Ingredient::firstOrCreate([
                    'name' => $ingredientData['name'],
                    'quantity' => $ingredientData['quantity'],
                    'metric' => $ingredientData['metric'],
                    'calories' => $ingredientData['quantity']*$ingredientData['calories']
                        ]);

                $ingredients[$ingredient->id] = ['quantity' => $ingredientData['quantity']];
            }
            //dd($ingredients);
            $recipe->ingredientss()->attach($ingredients);

            $recipe->categories()->attach($validatedData['categories']);
            DB::commit();

            $recipe->load('ingredientss');
            $recipe->load('categories');

            logger('final recipe', ['recipe' => $recipe]);
            return to_route('annonces.index')->with('success', 'Your recipe have been updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
                $errorMessage =  $e->getMessage();
                return response()->make("<script>
                alert('Bad request: $errorMessage');
                window.history.back();
            </script>");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $recipe = Recipe::with('ingredientss')->with('categories')->find($id);

        if ($recipe === null) {
            $errorMessage = 'Recipe not found';
            return response()->make("<script>
                alert('Bad request: $errorMessage');
                window.history.back();
            </script>");
        }
        $recipe->delete();
        return to_route('annonces.index')->with('success','Your recipe have been deleted with successfully');

    }
}
