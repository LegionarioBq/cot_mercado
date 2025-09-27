<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * 🔑 Realiza login e retorna token Sanctum
     */
    public function login(Request $request)
    {
        // 🔍 Validação dos campos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 🚫 Se não autenticar
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // ✅ Usuário autenticado
        $user = Auth::user(); // 👈 mais seguro que $request->user()
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '✅ Login realizado com sucesso',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'type'  => $user->type, // 👈 retorna também o tipo (admin/editor/viewer)
            ],
            'token'   => $token,
        ]);
    }

    /**
     * 🚪 Realiza logout e revoga todos os tokens do usuário
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => '✅ Logout realizado com sucesso',
        ]);
    }
}
