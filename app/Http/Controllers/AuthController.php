<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request) {
        $user = User::where('email', $request->email)->first();
        if($user){
            if(Hash::check($request->get('password'), $user->password)){
                $token = $user->createToken('api-token');
                return ['token' => $token->plainTextToken];
            }else{
                return response()->json([
                    'errors' => [
                        'email' => ['Invalid Credentials']
                    ],
                    'message' => 'The given data was invalid.'
                ], 422);
            }
        }
    }
}
