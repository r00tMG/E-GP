<?php

namespace App\Http\Controllers\api;

use App\APIKEY;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyController extends Controller
{
    public function index()
    {
        $apiKeys = APIKEY::all();
        //dd($apiKeys);
        return \response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'La liste des apiKeys',
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
            return \response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad Request',
                'errors' => $validator->errors()
            ]);
        }
        $apikey = APIKEY::create([
            'key' => $request->key,
            'value' => $request->value
        ]);
        //dd($apikey);
        return \response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'L\'apiKey a été bien ajouté',
            'apikey' => $apikey
        ]);
    }

    public function destroy($id)
    {
        $apikey = APIKEY::find($id);
        $apikey->delete();
        return \response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'L\'apikey a été bien supprimé',
        ]);
    }
}
