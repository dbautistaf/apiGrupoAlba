<?php

namespace App\Http\Controllers\articulos;

use App\Http\Controllers\articulos\Repository\ArticuloRepository;
use App\Models\articulos\ArticuloMatrizEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ArcitulosMatrizController extends Controller
{

    public function getFiltrarArticulos(ArticuloRepository $repo, Request $request)
    {
        $data = [];
        if (!is_null($request->activo)) {
            $data = $repo->findByListArticuloAlls(1000);
        } else if (!is_null($request->search) && is_null($request->codigo)) {
            $data = $repo->findByListArticuloLike($request->search . '%', 5);
        } else if (is_null($request->search) && !is_null($request->codigo)) {
            $data = $repo->findByListArticuloId($request->id);
        } else {
            $data = $repo->findByListArticuloVigente($request->vigente, 100);
        }
        return response()->json($data);
    }


    public function getArticuloId(Request $request)
    {
        return response()->json(ArticuloMatrizEntity::find($request->id));
    }

    public function getProcesarArticulo(Request $request)
    {
        DB::beginTransaction();
        try {
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
            $user = Auth::user();

            if (is_null($request->id_articulo)) {
                ArticuloMatrizEntity::create([
                    'id_familia' => $request->id_familia,
                    'id_subfamilia' => $request->id_subfamilia,
                    'id_rubro' => $request->id_rubro,
                    'id_unidad_medida' => $request->id_unidad_medida,
                    'descripcion_articulo' => $request->descripcion_articulo,
                    'codigo_barra' => $request->codigo_barra,
                    'fraccionable' => $request->fraccionable,
                    'por_lote' => $request->por_lote,
                    'promedio' => $request->promedio,
                    'ultima_compra' => $request->ultima_compra,
                    'empresa' => $request->empresa,
                    'vigente' => $request->vigente,
                    'fecha_registra' => $fechaActual,
                    'fecha_actualiza' => null,
                    'cod_usuario' => $user->cod_usuario,
                    'id_tipo_imputacion' => $request->id_tipo_imputacion
                ]);
            } else {
                $articulo = ArticuloMatrizEntity::find($request->id_articulo);
                $articulo->id_familia = $request->id_familia;
                $articulo->id_subfamilia = $request->id_subfamilia;
                $articulo->id_rubro = $request->id_rubro;
                $articulo->id_unidad_medida = $request->id_unidad_medida;
                $articulo->descripcion_articulo = $request->descripcion_articulo;
                $articulo->codigo_barra = $request->codigo_barra;
                $articulo->fraccionable = $request->fraccionable;
                $articulo->por_lote = $request->por_lote;
                $articulo->promedio = $request->promedio;
                $articulo->ultima_compra = $request->ultima_compra;
                $articulo->vigente = $request->vigente;
                $articulo->fecha_actualiza = $fechaActual;
                $articulo->id_tipo_imputacion = $request->id_tipo_imputacion;
                $articulo->update();
            }

            DB::commit();
            return response()->json(["message" => "Registro procesado correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function getEliminarArticulo(Request $request)
    {
        $articulo = ArticuloMatrizEntity::find($request->id);
        $articulo->delete();

        return response()->json(["message" => "Registro eliminado correctamente"]);
    }
}
