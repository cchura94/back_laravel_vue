<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pedidos = Pedido::with('cliente','productos')->paginate(10);
        return response()->json($pedidos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validar
        $request->validate([
            "cliente_id" => "required",
        ]);

        $user = Auth::user();
        $fecha_pedido = date("Y-m-d H:i:s");

        DB::beginTransaction();

        try {
            
            // guardar pedido
            $pedido = new Pedido();
            $pedido->user_id = $user->id;
            $pedido->cliente_id = $request->cliente_id;
            $pedido->fecha_pedido = $fecha_pedido;
            $pedido->save();

            // asignar productos al pedido
            $productos = $request->productos;
            foreach ($productos as $prod) {
                // attach
                $pedido->productos()->attach($prod['id'], ["cantidad" => $prod['cantidad']]);
            }
            // actualizar estado pedido
            $pedido->estado = 2;
            $pedido->update();
            // retornar

            DB::commit();
            return response()->json(["mensaje" => "Pedido Registrado"], 201);
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return response()->json(["mensaje" => "Error al registrar", "error" => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
