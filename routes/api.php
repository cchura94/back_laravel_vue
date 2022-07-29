<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1/auth'], function(){
    // /api/v1/auth/login
    Route::post("login", [AuthController::class, "loginLaravel"]);
    Route::post("registro", [AuthController::class, "registro"]); 

    Route::group(["middleware" => "auth:sanctum"], function(){
        // /api/v1/auth/perfil
        Route::get("perfil", [AuthController::class, "perfil"]);
        Route::post("logout", [AuthController::class, "salir"]);
    });
});
