<?php

namespace App\Http\Controllers\web;

use App\Annonce;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FastApiService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('users.profile');

    }
}