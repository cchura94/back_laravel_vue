<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // localhost:8000/api/producto?page=1&rows=10
        if($request->rows){
            $filas = $request->rows;
        }else{
            $filas = 5;
        }
        if($request->q){
            $productos = Producto::orWhere('nombre', 'like', '%'.$request->q.'%')
                                    ->orWhere('precio', 'like', '%'.$request->q.'%')
                                    ->with('categoria')
                                    ->paginate($filas);
        }else{
            $productos = Producto::with('categoria')->paginate($filas);

        }
        return response()->json($productos, 200);
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
            "nombre" => "required|string|max:100|min:2",
            "categoria_id" => "required"
        ]);
        // subir imagen
        $imagen = "";
        if($file = $request->file("imagen")){
            $direccion_imagen = time(). "-" .$file->getClientOriginalName();
            $file->move("imagenes/", $direccion_imagen);

            $imagen = "imagenes/". $direccion_imagen;
        }
        // guardar datos 
        $producto = new Producto();
        $producto->nombre = $request->nombre;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->categoria_id = $request->categoria_id;
        $producto->descripcion = $request->descripcion;
        $producto->imagen = $imagen;
        $producto->save();
        // respuesta

        return response()->json(["mensaje" => "Producto registrado"], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $producto = Producto::with("categoria")->findOrFail($id);
        // $producto = Producto::findOrFail($id);
        // $producto->categoria;
        return response()->json($producto, 200);
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
        // return $request;
         // validar
         $request->validate([
            "nombre" => "required|string|max:100|min:2",
            "categoria_id" => "required"
        ]);
        
        // guardar datos 
        $producto = Producto::find($id);
        $producto->nombre = $request->nombre;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->categoria_id = $request->categoria_id;
        $producto->descripcion = $request->descripcion;

        // subir imagen
        // $imagen = $producto->imagen;
        if($file = $request->file("imagen")){
            $direccion_imagen = time(). "-" .$file->getClientOriginalName();
            $file->move("imagenes/", $direccion_imagen);

            $imagen = "imagenes/". $direccion_imagen;
            $producto->imagen = $imagen;
        }

        $producto->update();
        // respuesta

        return response()->json(["mensaje" => "Producto actualizado"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return response()->json(["mensaje" => "Producto eliminado"], 200);
    }

    public function actualizarImagen(Request $request, $id)
    {
        return $request;
        $imagen = "";
        if($file = $request->file("imagen")){
            $direccion_imagen = time(). "-" .$file->getClientOriginalName();
            $file->move("imagenes/", $direccion_imagen);

            $imagen = "imagenes/". $direccion_imagen;

            $producto = Producto::find($id);
            $producto->imagen = $imagen;
            $producto->update();
        }
        return response()->json(["mensaje" => "Imagen Actualizada"], 200);

    }
}
