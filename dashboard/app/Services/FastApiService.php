<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FastApiService
{
    public function post($endpoint, array $data = [])
    {
        #dd($endpoint, $data);
        return Http::withToken(
            session('api_token')
        )->post(
                env('FASTAPI_URL').$endpoint,
                $data
            );
    }

    public function get($endpoint)
    {
        return Http::withToken(
            session('api_token')
        )->get(
                env('FASTAPI_URL').$endpoint
            );
    }
    public function put($endpoint, array $data = [])
    {
        return Http::withToken(
            session('api_token')
        )->put(
            env('FASTAPI_URL').$endpoint,
            $data
        );
    }

    public function delete($endpoint)
    {
        return Http::withToken(
            session('api_token')
        )->post(
            env('FASTAPI_URL').$endpoint,
        );
    }

}
