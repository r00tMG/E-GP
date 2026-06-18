<?php

namespace App\Http\Controllers;

use App\APIKEY;
use App\Setting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        $apiKeys = APIKEY::orderBy('created_at', 'DESC')->get();
        return view('settings.index',[
            'setting' => $setting,
            'apiKeys' => $apiKeys,
        ]);
    }
    public function editApiKey(APIKEY $apikey)
    {
        $setting = Setting::first();
        return view('settings.index',[
            'apikey' => $apikey,
            'setting' => $setting
        ]);
    }
    public function updateApiKey(Request $request,APIKEY $apikey)
    {
        $apikey->key = $request->input('key');
        $apikey->value = $request->input('value');
        $apikey->update();
        //dd($apikey);
        return to_route('apikeys.update')->with('success','La modification a réussie');
    }

    public function store(Request $request)
    {
        $setting = new Setting();

        $setting->version = $request->input('version');
        $setting->save();
        //dd($setting);
        return redirect()->back()->with('success','La version a été ajouté');
    }

    public function update(Request $request,$id)
    {
        $setting = Setting::find($id);
        $setting->version = $request->input('version');
        $setting->update();
        //dd($setting);

        return redirect()->back()->with('success','La version a été modifié');
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
            return redirect()->back()->with('success', 'Le mode maintenance est désactivé.');
        } else {
            Artisan::call('down',[
                '--secret' => '1234',
               '--render'=>"errors::503"
            ]);
            return redirect()->back()->with('success', 'Le mode maintenance est activé.');
        }

    }
}
