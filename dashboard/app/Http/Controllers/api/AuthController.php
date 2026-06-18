<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
         return Socialite::driver('google')
             ->scopes(['profile', 'email'])
             ->redirect();
    }

    public function handleCallBack()
    {
        try {
            $user = Socialite::driver('google')->user();

            $findUser = User::where('social_id',$user->id)->first();
            if($findUser)
            {
                //Auth::login($findUser);
                return \response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'L\'utilisateur est bien connecter à google',
                    'user' => $findUser
                ]);
            }else{
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'social_id' => $user->id,
                    'social_type' => 'google',
                    'password' => Hash::make('my-google')
                ]);
                //Auth::login($newUser);

                return \response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Vous êtes bien enrégistré',
                    'user' => $newUser
                ]);
            }

        }catch (\Exception $e){
            logger($e->getMessage());
        }

    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
          'email' => 'required|email',
          'password' => 'required'
        ]);
        if ($validator->fails())
        {
            return \response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad Request',
                'errors' => $validator->errors()
            ]);
        }
        if ( !Auth::attempt( $request->only(['email', 'password']) ) )
        {
             return \response()->json([
                 'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                 'message' => 'Les données sont invalides',
                 'errors' => [
                     'email' => [
                         'Invalid Credentials'
                     ]
                 ]
             ]);
        }
        $user = User::where('email', $request->email)->first();
            //dd($user);
        $services = Artisan::output();
        if(App::isDownForMaintenance())
        {
            return \response()->json([
               'status' => Response::HTTP_SERVICE_UNAVAILABLE,
               'message' => 'Le service est pas indisponible',
                'services' => $services
            ]);
        }
        return \response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Logged in successfully',
            'token' => $request->user()->createToken('API TOKEN',['*'], now()->addMonth(3))->plainTextToken,
            'user' => $user
        ]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad request',
                'errors' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' =>  Hash::make($request->password)
        ]);
        //dd($user);
        return \response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'L\'utilisateur a été créé avec succés',
            'user' => $user
        ]);
    }
}
