<?php

namespace App\Http\Controllers\web;

use App\Annonce;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FastApiService;
use Illuminate\Support\Facades\Auth;

class AnnonceController extends Controller
{
    public function index(FastApiService $api)
    {
//        #dd("Hello");
//        $response = $api->get('/annonces');
//        #dd($response);
//
//        $annonces = $response->json();
//        dd($annonces);
//        #dd($annonces['annonces']);
//        return view(
//            'annonces.index',
//            compact('annonces')
//        );
        try {
            $response = $api->get('/annonces');

            if (!$response->successful()) {

                $error = $response->json();
                if($error['detail'] == "Could not validate credentials"){
                    return to_route('login')->with('error', $error['detail']);

                }
            }

            $annonces = $response->json();

            return view(
                'annonces.index',
                compact('annonces')
            );

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function create(){
        return view('annonces.create');
    }
    public function store(Request $request,FastApiService $api){
        $payload = [
                'gp_id'=>session('user_id'),
                'origin'=>$request->origin,
                'date_depart'=>$request->date_depart,
                'destination'=>$request->destination,
                'description'=>$request->description,
                'date_arrivee'=>$request->date_arrivee,
                'prix_du_kilo'=>$request->prix_du_kilo,
                'prix_par_piece'=>$request->prix_par_piece,
                'kilos_disponibles'=>$request->kilos_disponibles
        ];
        #dd($payload);
        $response = $api->post('/annonces', $payload);

        $data = $response->json();
        #dd($data);
        if ($response->status() == 401) {
            return redirect()->route('login')->with('error', $data['detail']);
        }

        if ($response->successful()) {

            return redirect()
                ->route('annonces.index')
                ->with('success', $data['message']);
        }
        #dd($data['detail']);
        return back()
            ->with('error', $data['detail']);
    }

    /**
     * Display the specified resource.
     */
    public function show(FastApiService $api, $id)
    {
        try {
            $response = $api->get('/annonce/'.$id);
            #dd($response->json());
            if (!$response->successful()) {

                if($response->status() == 404 || $response->status() == 401){
                    return to_route('login')->with('error',"Could not validate credentials");
                }
            }

            $res = $response->json();
            #dd($res['annonce']);
            return view(
                'annonces.show',[
                'annonce' => $res['annonce'],
                    'id'=>$id
                    ]
            );

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }

    }

    public function edit(FastApiService $api, string $id)
    {
        try {
            $response = $api->get('/annonce/'.$id);
            #dd($response->json());
            if (!$response->successful()) {

                if($response->status() == 404 || $response->status() == 401){
                    return to_route('login')->with('error',"Could not validate credentials");
                }
            }

            $res = $response->json();
            #dd($res['annonce']);
            return view(
                'annonces.edit',[
                    'annonce' => $res['annonce'],
                     'id'=>$id
                ]
            );

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
        #return view('annonces.edit');
    }

    public function update(Request $request, FastApiService $api, string $id)
    {
        $payload = [
            'origin'=>$request->origin,
            'date_depart'=>$request->date_depart,
            'destination'=>$request->destination,
            'description'=>$request->description,
            'date_arrivee'=>$request->date_arrivee,
            'prix_du_kilo'=>$request->prix_du_kilo,
            'prix_par_piece'=>$request->prix_par_piece,
            'kilos_disponibles'=>$request->kilos_disponibles
        ];
        #dd($payload);
        try {

            $response = $api->put('/annonce/'.$id, $payload);
            #dd($response->json());
            if (!$response->successful()) {

                if($response->status() == 404 || $response->status() == 401){
                    return to_route('login')->with('error',"Could not validate credentials");
                }
            }

            $res = $response->json();
            #dd($res['annonce']);
            return to_route('annonces.index')->with('success', "Annonce mis à jour avec succés");

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
        #return view('annonces.edit');
    }

    public function destroy(FastApiService $api, $id)
    {
        try {

            $response = $api->delete('/annonce/'.$id);
            #dd($response->json());
            if (!$response->successful()) {

                if($response->status() == 404 || $response->status() == 401){
                    return to_route('login')->with('error',"Could not validate credentials");
                }
            }

            $res = $response->json();
            #dd($res['annonce']);
            return to_route('annonces.index')->with('success', "Annonce supprimée avec succés");

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}

