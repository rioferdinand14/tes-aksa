<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = bin2hex(openssl_random_pseudo_bytes(30)); // Generate a temporary token

            // Save token to user session
            $request->session()->put('auth_token', $token);

            // Debug untuk memastikan token disimpan di sesi
dd($request->session()->all());


            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                    ]
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }
    }


        public function logout(Request $request)
        {
            // Hapus token dari sesi
            $request->session()->forget('auth_token');
            
            // Invalidasi sesi
            $request->session()->invalidate();
            
            // Regenerasi token sesi
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful',
            ]);
        }

}