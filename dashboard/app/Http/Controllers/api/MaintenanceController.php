<?php

namespace App\Http\Controllers\api;

use App\APIKEY;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        $apiKeys = APIKEY::orderBy('created_at', 'DESC')->get();
        return \response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'La dernière version et la liste des apiKeys',
            'setting' => $setting,
            'apiKeys' => $apiKeys
        ]);
    }

    public function store(Request $request)
    {
        $setting = new Setting();
        $setting->version = $request->input('version');
        $setting->save();
        //dd($setting);
        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'La version a été ajouté'
        ]);
    }

    public function update(Request $request,$id)
    {
        $setting = Setting::find($id);
        $setting->version = $request->input('version');
        $setting->update();
        //dd($setting);

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'La version a été mis à jour'
        ]);
    }
    public function activeMaintenance(Request $request)
    {
        //dd('salut');
        $excludedRoutes = $request->get('excluded_routes', []);
        //dd($excludedRoutes);
        config(['maintenance.excluded_routes'=>$excludedRoutes]);
        //dd($excludedRoutes);
        if (App::isDownForMaintenance()) {
            Artisan::call('up');
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Le mode maintenance est désactivé'
            ]);
        } else {
            Artisan::call('down',[
                '--secret' => '1234',
                '--render'=>"errors::503"
            ]);
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Le mode maintenance est activé'
            ]);
        }

    }


}
