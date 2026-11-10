<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = app('hash')->make($request->input('password'));
        $user->save();

        return response()->json($user, 200);
    }

    public function login(Request $request)
{
    $this->validate($request, [
        'email'    => 'required|string',
        'password' => 'required|string',
    ]);

    $credentials = $request->only(['email', 'password']);

    if (!$token = Auth::attempt($credentials)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    return response()->json([
        'token'        => $token,
        'token_type'   => 'bearer',
        'expires_in'   => Auth::factory()->getTTL() * 60,
    ], 200);
}
}