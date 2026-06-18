<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
class ServiceStatusController extends Controller
{
    public function index()
    {
        $result = Artisan::call('check:service-status');
        $services = Artisan::output();

        #dd( $result, response()->json(json_decode($services)) );
        if ($result === 0)
        {
            return view('settings.status', [
                'services' => json_decode($services, true)
            ]);
        }else{
            return response()->make("<script>
                alert('Aucun status n\'est disponible');
                window.history.back();
            </script>");
        }
    }
}
