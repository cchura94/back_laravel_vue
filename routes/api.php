<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
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

Route::group(["middleware" => "auth:sanctum"], function(){
    // actualizar imagen
    Route::put("producto/{id_prod}/actualizar-img", [ProductoController::class, "actualizarImagen"]);

    // CRUD API
    Route::apiResource("categoria", CategoriaController::class);
    Route::apiResource("producto", ProductoController::class); //->middleware("role:admin");
    Route::apiResource("cliente", ClienteController::class);
    Route::apiResource("pedido", PedidoController::class);
});


Route::get("/no-autorizado", function(){
    return ["mensaje" => "No tienes permiso para acceder a esta pagina"];
})->name("login");