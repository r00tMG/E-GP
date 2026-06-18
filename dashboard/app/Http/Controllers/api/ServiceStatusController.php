<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class ServiceStatusController extends Controller
{
    public function index()
    {
        $result = Artisan::call('check:service-status');
        $services = Artisan::output();
        if ($result === 0)
        {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Les status de tes services',
                'services' => json_decode($services, true)
            ]);
        }else{
            return response()->json([
               'status' => Response::HTTP_NO_CONTENT,
               'message' => 'Aucun status de tes services, n\'est disponible'
            ]);
        }
    }
}
