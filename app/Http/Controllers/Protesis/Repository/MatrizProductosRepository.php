<?php

namespace App\Http\Controllers\Protesis\Repository;

use App\Models\Protesis\ProtesisMatrizProductosEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MatrizProductosRepository
{
    public function save($parmas)
    {
        $user = Auth::user();
        $fechaAnctual = Carbon::now();

        return ProtesisMatrizProductosEntity::create([
            'id_categoria' => $parmas->id_categoria,
            'descripcion_producto' => $parmas->descripcion_producto,
            'presentacion' => $parmas->presentacion,
            'monodroga' => $parmas->monodroga,
            'laboratorio' => $parmas->laboratorio,
            'fecha_crea' => $fechaAnctual,
            'cod_usuario' => $user->cod_usuario,
            'material' => $parmas->material,
            'observaciones' => $parmas->observaciones,
            'vigente' => $parmas->vigente
        ]);
    }

    public function saveId($parms)
    {
        $fechaAnctual = Carbon::now();

        $prod = ProtesisMatrizProductosEntity::find($parms->id_producto);
        $prod->id_categoria = $parms->id_categoria;
        $prod->descripcion_producto = $parms->descripcion_producto;
        $prod->presentacion = $parms->presentacion;
        $prod->monodroga = $parms->monodroga;
        $prod->laboratorio = $parms->laboratorio;
        $prod->material = $parms->material;
        $prod->observaciones = $parms->observaciones;
        $prod->fecha_actualiza = $fechaAnctual;
        $prod->vigente = $parms->vigente;
        $prod->update();

        return $prod;
    }

    public function fidnByExistId($id)
    {
        return ProtesisMatrizProductosEntity::where('id_producto', $id)->exists();
    }

    public function findByEliminarId($id)
    {
        return ProtesisMatrizProductosEntity::find($id)->delete();
    }

    public function findByListTodos($limit)
    {
        return ProtesisMatrizProductosEntity::with(['categoria'])
            ->limit($limit)->get();
    }

    public function findByListProductoLike($search, $limit)
    {
        return ProtesisMatrizProductosEntity::where('descripcion_producto', 'LIKE', '%' . $search . '%')
            ->limit($limit)->get();
    }

    public function findByListIdCategria($id, $limit)
    {
        return ProtesisMatrizProductosEntity::with(['categoria'])
            ->where('id_categoria', $id)
            ->limit($limit)->get();
    }

    public function findByListProductoLikeAndIdCategoria($search, $id, $limit)
    {
        return ProtesisMatrizProductosEntity::with(['categoria'])
            ->where('id_categoria', $id)
            ->where('descripcion_producto', 'LIKE', '%' . $search . '%')
            ->limit($limit)->get();
    }
}
