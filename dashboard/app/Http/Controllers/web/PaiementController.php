<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Services\FastApiService;
use Illuminate\Http\Request;

class PaiementController extends Controller
{

    public function pricing(FastApiService $api, $id)
    {
        try {
            $response = $api->get('/reservation/'.$id);
            #dd($response->json());
            if (!$response->successful()) {

                if($response->status() == 404 || $response->status() == 401){
                    return to_route('login')->with('error',"Could not validate credentials");
                }
            }

            $res = $response->json();
            #dd($res['annonce']);
            return view(
                'paiements.pricing',[
                    'reservation' => $res['reservations']
                ]
            );

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
        #dd($id);
        $response = $api->get('/reservation/'.$id);
        $reservation = $response->json();
        return view("paiements.pricing", [
            "reservation" => $reservation['reservations'],
        ]);
    }

    public function stripe(FastApiService $api, Request $request)
    {
        $payload = [
            "reservation_id" => $request->id,
        ];
        #dd($payload);
        $response = $api->post('/stripe/create-checkout-session', $payload);

        $data = $response->json();
        #dd($data['checkout_url']);
        if ($response->status() == 401) {
            return redirect()->route('login')->with('error', $data['detail']);
        }

        if ($response->successful()) {
            return redirect()->away($data['checkout_url']);
        }
        #dd($data['detail']);
        return back()
            ->with('error', $data['detail']);
    }

    public function success()
    {
        #dd("success");
        return redirect()
            ->route('reservations.index')
            ->with('success', 'Votre reservation a été validé avec succés.');
    }

}
