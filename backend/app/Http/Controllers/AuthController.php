<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validate = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        $validate['password'] =  bcrypt($request->password);
        $validate['role'] = 'user';

        $user = User::create($validate);
        $success['token'] = $user->createToken('AdminBooklist')->plainTextToken;
        $success['name'] = $user->name;

        return response()->json($success, Response::HTTP_CREATED);
    }

    public function login(Request $request) {
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('AdminBooklist')->plainTextToken;
            $success['name'] = $user->name;
            $success['role'] = $user->role;
            return response()->json($success, 201);
        } else {
            return response()->json(['error' => 'Akun tidak terdaftar'], 401);
        }
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}
