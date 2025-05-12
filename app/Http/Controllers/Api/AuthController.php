<?php

namespace App\Http\Controllers\Api;

use Auth;
use DB;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if(! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Anda Belum Terdaftar'
            ]);
        }

        $user  = User::where('email', $request->input('email'))->firstOrFail();

        // $data = $user->tokens->first();

        // if($data) {
        //     return reponse()->json([
        //         $data => true
        //     ]);
        // } else {
        //     return response()->json([
        //         $data = false
        //     ]);
        // }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'success',
            'token' => $token,
            'type' => 'Bearer'
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout Berhasil'
        ]);
    }

    public function validatedToken(Request $request)
    {
        // $user = User::where('email', $request->input('email'))->firstOrFail();

        // $user = DB::table('personal_access_tokens')->where('token', $request->token)->first();
        $auth =  auth('sanctum')->check();
        // $data = $user()->tokens->first();

        if($auth) {
            return response()->json([
                'data' => true
            ]);
        } else {    
            return response()->json([
                'data' => false
            ]);
        }
    }
}