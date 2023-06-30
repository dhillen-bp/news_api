<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'         => 'required|email',
            'password'      => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $accessToken = $user->createToken('user login')->plainTextToken;
        return $accessToken;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    public function loggedInUser()
    {
        $user = Auth::user();
        return response()->json($user);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email'     => 'required|unique:users',
            'username'  => 'required|unique:users|max:255',
            'password'  => 'required',
            'firstname' => 'required|max:100',
            'lastname' => 'nullable|max:100',
        ]);

        $request['password'] = Hash::make($request['password']);

        $user = User::create($request->all());

        return new UserResource($user);
    }
}
