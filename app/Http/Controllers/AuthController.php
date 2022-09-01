<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Validator;
class AuthController extends Controller
{
    public function index()
    {
        return 'hello';
    }

    public function register(Request $request)
    {
        // dd($request);
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8'
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }
            $user = User::create(
                $request->only('name','email')
            + [
                'password' => \Hash::make($request->input('password'))
            ]    
        );
            $response = [
                'success' => true,
                'error' => [],
                'message' => 'Anda berhasil mendaftar',
                'data' => $user
            ];
            return response()->json($response, 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }
        if(!\Auth::attempt($request->only('email','password'))){
            return response([
                'error' => 'user tidak ditemukan'
            ]);
        }
        $user = \Auth::user();

        $jwt = $user->createToken('auth_token',['user'])->plainTextToken;
        $cookie = cookie('jwt', $jwt, 60*24);//1day

        return response([
            'message' => 'succes'
        ])->withCookie($cookie);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        return $user;
    }

    public function logout()
    {
        $cookie = \Cookie::forget('jwt');
        return response([
            'message' => 'succes'
        ])->withCookie($cookie);
    }
}
