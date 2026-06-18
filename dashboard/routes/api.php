<?php

use App\Http\Controllers\api\ApiKeyController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\IngredientController;
use App\Http\Controllers\api\MaintenanceController;
use App\Http\Controllers\api\MealController;
use App\Http\Controllers\api\RecipeController;
use App\Http\Controllers\api\ServiceStatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('calorie/{id}/recipe',[\App\Http\Controllers\CalorieController::class,'calculatorCalorie']);
#Route public
Route::middleware(['auth:sanctum'])->group(function (){
    Route::apiResource('annonces', RecipeController::class);
    Route::apiResource('ingredients',IngredientController::class);
    Route::apiResource('categories',CategoryController::class);
    Route::apiResource('meals',MealController::class);
    Route::apiResource('settings',MaintenanceController::class);
    Route::apiResource('apikeys',ApiKeyController::class);

    Route::post('maintenance',[MaintenanceController::class,'activeMaintenance'])->name('maintenance');
    Route::get('health',[ServiceStatusController::class,'index']);
});


#Route private
Route::middleware(['guest'])->group(function (){
    Route::get('auth/google',[AuthController::class,'redirectToGoogle'])->name('google');
    Route::get('callback/google',[AuthController::class,'handleCallBack']);

    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login']);
});


Route::middleware(['geoBlock'])->group(function (){
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
