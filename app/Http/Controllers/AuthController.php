<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginLaravel(Request $request)
    {       
        # validar
        $request->validate([
            "email" => "required|email|string",
            "password" => "required|string"
        ]);
        //
        $credenciales = request(["email", "password"]);
        if(!Auth::attempt($credenciales)){
            return response()->json([
                "mensaje" => "No Autorizado"
            ], 401);
        }

        $usuario = $request->user();

        $tokenResult = $usuario->createToken('Personal token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'usuario' => $usuario,
            'token_type' => 'Bearer'
        ]);        
    }

    public function refreshToken(Request $request)
    {
        $usuario = $request->user();        
        $tokenResult = $usuario->createToken('Personal token');
        $token = $tokenResult->plainTextToken;
        
        Auth::user()->tokens()->delete();

        return response()->json([
            'access_token' => $token,
            'usuario' => $usuario,
            'token_type' => 'Bearer'
        ]);        
    }


    public function registro(Request $request)
    {
        # guardar un nuevo user en la BD
        // validar
        $request->validate([
            "name" => "required",
            "email" => "required|unique:users|email",
            "password" => "required|string",
            "c_password" => "required|same:password"
        ]);
        //guardar
        // $usuario = new User($request->all());
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->save();
        //retornar el mensaje
        return response()->json(["mensaje" => "Usuario registrado"]);
    }

    public function perfil()
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }

    public function salir()
    {
        Auth::user()->tokens()->delete();
        return response()->json(["mensaje" => "Tokens eliminados"]);  
    }
}
