<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\CalorieController;
use App\Http\Controllers\Controller;
use App\Ingredient;
use App\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
/**
 * @group Recipe Management
 *
 * APIs for managing annonces.
 */
class RecipeController extends Controller
{


    /**
     * Get all annonces.
     *
     * This endpoint retrieves a list of all annonces in the database.
     *
     * @response {
     * "status": 200,
     * "message": "Liste des recettes",
     * "annonces": [
     * {
     * "id": 1,
     * "title": "Spaghetti Bolognese",
     * "image": "path/to/image.jpg",
     * "summary": "A classic Italian pasta dish.",
     * "ingredients": [
     * {
     * "name": "Spaghetti",
     * "quantity": "200g"
     * },
     * {
     * "name": "Ground Beef",
     * "quantity": "100g"
     * }
     * ]
     * }
     * ]
     * }
     */
    public function index()
    {
        $recipes = Recipe::with('ingredientss')->with('categories')->orderBy('created_at', 'DESC')->get();
        #dd($annonces);
        $services = Artisan::output();
        if(App::isDownForMaintenance())
        {
            return \response()->json([
                'status' => Response::HTTP_SERVICE_UNAVAILABLE,
                'message' => 'Le service est pas indisponible',
                'services' => $services
            ]);
        }
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Liste des recettes',
            'annonces' => $recipes

        ],Response::HTTP_OK);
    }


    /**
     * Store a newly created recipe.
     *
     * This endpoint allows users to create a new recipe.
     *
     * @bodyParam title string required The title of the recipe. Example: "Chocolate Cake"
     * @bodyParam image file required An image of the recipe.
     * @bodyParam summary string required A brief summary of the recipe.
     * @bodyParam ingredients array required A list of ingredients with their quantities.
     * @bodyParam ingredients.*.name string required The name of the ingredient. Example: "Flour"
     * @bodyParam ingredients.*.quantity string required The quantity of the ingredient. Example: "200g"
     *
     * @response 201 {
     * "id": 1,
     * "title": "Chocolate Cake",
     * "image": "path/to/image.jpg",
     * "summary": "A rich and moist chocolate cake.",
     * "ingredients": [
     * {
     * "name": "Flour",
     * "quantity": "200g"
     * },
     * {
     * "name": "Cocoa Powder",
     * "quantity": "50g"
     * }
     * ]
     * }
     *
     * @response 422 {
     * "message": "Validation errors",
     * "errors": {
     * "title": ["The title field is required."],
     * "image": ["The image field is required."]
     * }
     * }
     */

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $rules = [
            'title' => 'required|string|max:255',
            'image' => 'required|file',
            'summary' => 'required|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|string|max:255',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        //dd($validator->fails());
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();
        //dd($validatedData);
        if ($request->hasFile('image')) {
            if ($validatedData['image'] && Storage::exists($validatedData['image'])) {
                Storage::delete($validatedData['image']);
            }
            $validatedData['image'] = $request->file('image')->store('images');
        }
        try {
            DB::beginTransaction();

            $recipe = Recipe::create([
                'title' => $validatedData['title'],
                'image' => $validatedData['image'],
                'summary' => $validatedData['summary'],
            ]);
            //dd($validatedData['ingredients']);
            $ingredients = [];
            foreach ($validatedData['ingredients'] as $ingredientData) {
                $ingredient = Ingredient::firstOrCreate(
                    ['name' => $ingredientData['name']],
                    ['quantity' => $ingredientData['quantity']]
                );
                $ingredients[$ingredient->id] = ['quantity' => $ingredientData['quantity']];
            }
            //dd($ingredients);

            $recipe->ingredientss()->attach($ingredients);
            $recipe->categories()->attach($validatedData['categories']);
            DB::commit();

            $recipe->load('ingredientss');
            //dd($recipe);

            return response()->json([
                'id' => $recipe->id,
                'title' => $recipe->title,
                'image' => $recipe->image,
                'summary' => $recipe->summary,
                'ingredients' => $recipe->ingredients->map(function ($ingredient) {
                    return [
                        'name' => $ingredient->name,
                        'quantity' => $ingredient->pivot->quantity,
                    ];
                }),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while creating the recipe.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Show a specific recipe.
     *
     * This endpoint allows users to view a specific recipe by its ID.
     *
     * @urlParam id integer required The ID of the recipe. Example: 1
     *
     * @response {
     * "recipe": {
     * "id": 1,
     * "title": "Spaghetti Bolognese",
     * "image": "path/to/image.jpg",
     * "summary": "A classic Italian pasta dish.",
     * "ingredients": [
     * {
     * "name": "Spaghetti",
     * "quantity": "200g"
     * },
     * {
     * "name": "Ground Beef",
     * "quantity": "100g"
     * }
     * ]
     * }
     * }
     */
    public function show(string $id, CalorieController $calorieController)
    {
        //dd($id);
        $recipe = Recipe::with('ingredientss')->with('categories')->find($id);
        $calories = $calorieController->calculatorCalorie($id);
        //dd($calories);
        return \response()->json([
            'calories' => $calories,
            'recipe' => $recipe
        ],Response::HTTP_OK);
    }


    /**
     * Update an existing recipe.
     *
     * This endpoint allows users to update a specific recipe.
     *
     * @urlParam id integer required The ID of the recipe. Example: 1
     * @bodyParam title string required The title of the recipe. Example: "Vegan Pancakes"
     * @bodyParam image string required The image of the recipe.
     * @bodyParam summary string required A brief summary of the recipe.
     * @bodyParam ingredients array required A list of ingredients with their quantities.
     * @bodyParam ingredients.*.name string required The name of the ingredient. Example: "Banana"
     * @bodyParam ingredients.*.quantity string required The quantity of the ingredient. Example: "2"
     *
     * @response 201 {
     * "status": 201,
     * "message": "Your annonces have been updated successfully",
     * "recipe": {
     * "id": 1,
     * "title": "Vegan Pancakes",
     * "image": "path/to/image.jpg",
     * "summary": "Delicious and healthy vegan pancakes.",
     * "ingredients": [
     * {
     * "name": "Banana",
     * "quantity": "2"
     * },
     * {
     * "name": "Almond Milk",
     * "quantity": "200ml"
     * }
     * ]
     * }
     * }
     *
     * @response 422 {
     * "message": "Validation errors",
     * "errors": {
     * "title": ["The title field is required."],
     * "image": ["The image field is required."]
     * }
     * }
     */
    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $rules = [
            'title' => 'required|string|max:255',
            'image' => 'required|file',
            'summary' => 'required|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|string|max:255',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();
        $recipe = Recipe::with('ingredientss')->with('categories')->find($id);
        if ($request->hasFile('image')){
            $validatedData['image'] = $request->file('image')->store('images');
        }
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
            //dd($validatedData['ingredients']);

            foreach ($validatedData['ingredients'] as $ingredientData) {
                //dd($ingredientData['name']);
                $ingredient = Ingredient::firstOrCreate(
                    ['name' => $ingredientData['name']],
                    ['quantity' => $ingredientData['quantity']]
                );

                $ingredients[$ingredient->id] = ['quantity' => $ingredientData['quantity']];
                //dd($ingredients);
            }
            //dd($ingredients);
            $recipe->ingredientss()->attach($ingredients);
            $recipe->categories()->attach($validatedData['categories']);

            DB::commit();

            $recipe->load('ingredientss');
            //dd($recipe);
            logger('final recipe', ['recipe' => $recipe]);
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Your annonces have been updated successfully',
                'recipe' => $recipe
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while updating the recipe.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Delete a specific recipe.
     *
     * This endpoint allows users to delete a recipe by its ID.
     *
     * @urlParam id integer required The ID of the recipe. Example: 1
     *
     * @response 204 {
     * "status": 204,
     * "message": "Your recipe has been deleted successfully"
     * }
     *
     * @response 404 {
     * "status": 404,
     * "message": "Recette introuvable"
     * }
     */
    public function destroy(string $id)
    {
        //dd($id);
        $recipe = Recipe::with('ingredientss')->with('categories')->find($id);
        //dd($recipe);
        if ($recipe === null)
        {
            return \response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Recipe not found'
            ]);
        }
        $recipe->delete();
        return \response()->json([
            'status' => Response::HTTP_NO_CONTENT,
            'message' => 'Your recipe have been deleted with successfully'
        ]);
    }
}
