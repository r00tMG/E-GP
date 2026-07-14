<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginForm;
use App\Services\FastApiService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function doregister(
        Request $request,
        FastApiService $api
    )
    {
        #dd("test");
        $payload = [
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'confirm_password' => $request->confirm_password,
            'role' => $request->role,
        ];
        #dd($payload);

        $response = $api->post('/register', $payload);

        $data = $response->json();
        #dd($data);

        if ($response->successful()) {

            return redirect()
                ->route('login')
                ->with('success', $data['message']);
        }
        #dd($data['detail'][0]['msg']);
        return back()
            ->with('error', $data['detail']);
    }

    public function dologin(
        Request $request,
        FastApiService $api
    )
    {
        $payload = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        #dd($payload);

        $response = $api->post('/login', $payload);
        $data = $response->json();

        if ($response->successful()) {

            session([
                'api_token' => $data['access_token'],
                'user_id'=>$data['user_id'],
            ]);
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome to E-GP');
            #return redirect()->route('dashboard')->with('success', "Welcome to E-GP");
        }

        return back()->with('error', 'Identifiants invalides');
    }

    public function redirectToGoogle()
    {
        logger('redirect to google');
        return Socialite::driver('google')
            ->scopes(['profile', 'email'])
            ->redirect();
    }

    public function handleCallBack()
    {
        try {
            $user = Socialite::driver('google')->user();
            //dd($user);
            logger('user',['user' => $user]);
            $findUser = User::where('social_id',$user->id)->first();
            logger('user find',['userfind' => $findUser]);
            //dd($findUser);
            if($findUser)
            {
                logger('user is finded and connected',['findUser' => $findUser]);
                Auth::login($findUser);
                return to_route('dashboard');
            }else{
                $newUser = User::create([
                   'name' => $user->name,
                   'email' => $user->email,
                   'social_id' => $user->id,
                    'social_type' => 'google',
                    'password' => Hash::make('my-google')
                ]);
                logger('user is new', ['newUser'=>$newUser]);
                //dd($newUser);
                Auth::login($newUser);
                return to_route('dashboard');
            }
        }catch (\Exception $e){
            logger($e->getMessage());
        }
    }
//    public function register(Request $request)
//    {
//        $user = User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'password' => Hash::make($request->password)
//        ]);
//        return to_route('pages.auth.register',[
//            'user' => $user
//        ]);
//    }
    public function register()
    {
        return view('auth.register');
    }
    public function login()
    {
        return view('auth.login');
    }


    public function logout(FastApiService $api)
    {
        $response = $api->post('/logout');
        session()->forget('api_token');
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
