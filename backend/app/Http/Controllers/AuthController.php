<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Realiza login e retorna token Sanctum
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário ou senha inválidos.',
            ], 401);
        }

        $user = Auth::user();

        // Opcional: remove tokens antigos antes de criar um novo
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'type'  => $user->type ?? 'user',
            ],
            'token' => $token,
        ], 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Realiza logout e revoga todos os tokens do usuário
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso',
            'action'  => 'logout',
        ]);
    }
}
