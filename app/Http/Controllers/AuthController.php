<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = \App\Models\User::create([
            'name' => '',  // fixme
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
        ]);
        $token = $user->createToken($email)->plainTextToken;
        return response()->json(['message' => 'created', 'token' => $token]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $successAttempt = auth()->attempt([
            'email' => $request['email'],
            'password' => $request['password']
        ]);

        if (!$successAttempt) {
            return response()->json([
                'message' => 'invalid email or password'
            ]);
        }

        // creating token
        $user = \App\Models\User::where('email', $request['email'])->first();
        $token = $user->createToken($email)->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        // take token from the authorization header and invalidate it from the db
        $invalidated = $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => ($invalidated ? 'Succesfully logged out.' : 'Something went wrong.'),
        ]);
    }
}
