<?php

namespace App\Http\Controllers;

use App\Http\Resources\MealsResource;
use App\Meal;
use App\Recipe;
use App\User;
use Illuminate\Http\Request;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $meals = Meal::with('user')->with('recipe')->get();
        //dd(MealsResource::collection($meals));
        $events = [];
        foreach ($meals as $meal) {
            $events[] = [
                'title' => $meal->recipe->title,
                'start' => $meal->date,
                'extendedProps' => [
                    'user' => $meal->user->name,
                    'meal_type' => $meal->meal_type,
                ],
            ];
        }
        //dd($events);
        return view('meals.index', [
            'events' => $events,
            'meals' => $meals
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $recipes = Recipe::all();
        $users = User::all();
        return view('meals.create', compact('recipes', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $meal = new Meal();
        $meal->user_id = $request->input('user');
        $meal->recipe_id = $request->input('recipe');
        $meal->meal_type = $request->input('type');
        $meal->date = $request->input('date');
        $meal->save();

        return to_route("meals.index")->with("success", "Meal added by success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $meal = Meal::with('user')->with('recipe')->find($id);
        return view('meals.show', compact('meal'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $meal = Meal::with('user')->with('recipe')->find($id);
        $users = User::all();
        $recipes = Recipe::all();
        //dd($meal);
        return view('meals.edit', compact('meal','users','recipes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $meal = Meal::find($id);
        $meal->user_id = $request->input('user_id');
        $meal->recipe_id = $request->input('recipe_id');
        $meal->meal_type = $request->input('type');
        $meal->date = $request->input('date');
        $meal->update();
        //dd($request->all(),$meal);
        return to_route('meals.index')->with('success','Meal have been updated with success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $meal = Meal::find($id);
        $meal->delete();
        return to_route('meals.index')->with('success', 'Meals have been deleted with success');
    }
}
