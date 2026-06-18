<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Ingredient Management
 *
 * APIs for managing ingredients
 */
class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     * *
     * * @response {
     * *   "status": 200,
     * *   "message": "Liste des ingrédients",
     * *   "ingredients": [
     * *      {"id": 1, "name": "Tomato", "quantity": "2 kg", "created_at": "2024-08-29", "updated_at": "2024-08-29"},
     * *      {"id": 2, "name": "Onion", "quantity": "1 kg", "created_at": "2024-08-29", "updated_at": "2024-08-29"}
     * *   ]
     * * }
 */
    public function index()
    {
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
            'message' => 'Liste des ingrédients',
            'ingredients' => Ingredient::orderBy('created_at', 'DESC')->get()
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @bodyParam name string required The name of the ingredient. Example: Tomato
     * @bodyParam quantity string required The quantity of the ingredient. Example: 2 kg
     *
     * @response 201 {
     * "status": 201,
     * "message": "Recipe add successfully",
     * "ingredient": {
     * "id": 1,
     * "name": "Tomato",
     * "quantity": "2 kg",
     * "created_at": "2024-08-29",
     * "updated_at": "2024-08-29"
     * }
     * }
     * @response 400 {
     *  "status": 400,
     *  "message": "Bad Request",
     *  "errors": {
     *  "name": ["The name field is required."],
     *  "quantity": ["The quantity field is required."]
     *  }
     * }
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'name' => ['required','string'],
            'quantity' => ['required','string']
        ]);
        #dd($validated->getData());
        if ($validated->fails())
        {
            return \response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad Request',
                'errors' => $validated->errors()
            ]);
        }
        $ingredient = Ingredient::create($validated->getData());
        return \response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Recipe add successfully',
            'ingredient' => $ingredient
        ]);

    }


    /**
     * Display the specified resource.
     *
     * @urlParam id integer required The ID of the ingredient. Example: 1
     *
     * @response {
     * "message": "Liste des ingrédients",
     * "ingredient": {
     * "id": 1,
     * "name": "Tomato",
     * "quantity": "2 kg",
     * "created_at": "2024-08-29",
     * "updated_at": "2024-08-29"
     * }
     * }
     */
    public function show(Ingredient $ingredient)
    {
        return \response()->json([
            'message' => 'Liste des ingrédients',
            'ingredient' => $ingredient
        ], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @urlParam id integer required The ID of the ingredient. Example: 1
     * @bodyParam name string required The name of the ingredient. Example: Tomato
     * @bodyParam quantity string required The quantity of the ingredient. Example: 2 kg
     *
     * @response 201 {
     * "status": 201,
     * "message": "Recipe update successfully",
     * "ingredient": {
     * "id": 1,
     * "name": "Tomato",
     * "quantity": "2 kg",
     * "created_at": "2024-08-29",
     * "updated_at": "2024-08-29"
     * }
     * }
     * @response 400 {
     * "status": 400,
     * "message": "Bad Request",
     * "errors": {
     * "name": ["The name field is required."],
     * "quantity": ["The quantity field is required."]
     * }
     * }
     */
    public function update(Request $request, string $id)
    {
        $validated = Validator::make($request->all(),[
            'name' => ['required','string'],
            'quantity' => ['required','string']
        ]);
        //dd($validated->fails());
        if ($validated->fails())
        {
            return \response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad Request',
                'errors' => $validated->errors()
            ]);
        }
        $ingredient = Ingredient::find($id);
        $ingredient->update($request->all());
        return \response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Recipe update successfully',
            'ingredient' => $ingredient
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @urlParam id integer required The ID of the ingredient. Example: 1
     *
     * @response 204 {
     * "status": 204,
     * "message": "Your ingredient have been deleted with successfully"
     * }
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return \response()->json([
            'status' => Response::HTTP_NO_CONTENT,
            'message' => 'Your ingredient have been deleted with successfully'
        ]);
    }
}
