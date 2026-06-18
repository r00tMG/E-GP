<?php

namespace App\Http\Controllers\api;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * @group Category Management
 *
 * APIs for managing categories
 */
class CategoryController extends Controller
{
    /**
     * Get All Categories
     *
     *  Retrieves a list of all categories.
     *
     * @response 200 {
     *   "status": 200,
     *   "categories": [
     *       {
     *           "id": 1,
     *           "name": "Beverages",
     *           "created_at": "2024-08-29T12:00:00.000000Z",
     *           "updated_at": "2024-08-29T12:00:00.000000Z"
     *       }
     *   ]
     *  }
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => Response::HTTP_OK,
            'categories' => $categories
        ]);
    }


    /**
     *  Create a New Category
     *
     *  Stores a newly created category in the database.
     *
     * @bodyParam name string required The name of the category. Example: Beverages
     *
     * @response 201 {
     *   "status": 201,
     *   "message": "Your category have been created with successfully",
     *   "category": {
     *       "id": 2,
     *       "name": "Beverages",
     *       "created_at": "2024-08-29T12:00:00.000000Z",
     *       "updated_at": "2024-08-29T12:00:00.000000Z"
     *   }
     *  }
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request):RedirectResponse|JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return \response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Your category have been created with successfully',
            'category' => $category
        ]);
    }

    /**
     *  Show a Specific Category
     *
     *  Displays a specific category.
     *
     * @urlParam id integer required The ID of the category. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Beverages",
     *   "created_at": "2024-08-29T12:00:00.000000Z",
     *   "updated_at": "2024-08-29T12:00:00.000000Z"
     *  }
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Category $category)
    {
        return view('categories.categories',[
            'category' => $category
        ]);
    }

    /**
     * Edit a Specific Category
     *
     *  Show the form for editing the specified category.
     *
     * @urlParam id integer required The ID of the category. Example: 1
     *
     * @response 200
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Category $category)
    {
        return view('categories.edit-categories',[
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Update a Specific Category
     *
     *  Updates the specified category in the database.
     *
     * @urlParam id integer required The ID of the category. Example: 1
     * @bodyParam name string required The name of the category. Example: Beverages
     *
     * @response 200 {
     *   "status": 200,
     *   "message": "Category updated successfully",
     *   "category": {
     *       "id": 1,
     *       "name": "Updated Beverages",
     *       "created_at": "2024-08-29T12:00:00.000000Z",
     *       "updated_at": "2024-08-29T12:00:00.000000Z"
     *   }
     *  }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return JsonResponse
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return \response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Category updated successfully',
            'category' => $category
        ]);

    }

    /**
     * Delete a Category
     *
     *  Removes the specified category from the database.
     *
     * @urlParam id integer required The ID of the category. Example: 1
     *
     * @response 204 {
     *   "status": 204,
     *   "message": "Your category have been deleted with success"
     *  }
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return \response()->json([
            'status' => Response::HTTP_NO_CONTENT,
            'message' => 'Your category have been deleted with success',
        ]);
    }
}
