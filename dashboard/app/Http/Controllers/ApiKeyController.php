<?php

namespace App\Http\Controllers;

use App\APIKEY;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiKeyController extends Controller
{
    public function index()
    {
        $apiKeys = APIKEY::all();
        //dd($apiKeys);
        return view('settings.index',[
            'apiKeys' => $apiKeys
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'key' => 'required',
            'value' => 'required'
        ]);
        if ($validator->fails())
        {
            return view('settings.index', [
                'errors' => $validator->errors()
            ]);
        }
        APIKEY::create([
           'key' => strtoupper($request->key),
           'value' => $request->value
        ]);
        return to_route('settings.index');
    }



    public function destroy($id)
    {
        $apikey = APIKEY::find($id);
        $apikey->delete();
        return to_route('settings.index')->with('success', 'La suppression a réussie');
    }
}
