<?php

namespace App\Http\Controllers\articulos;
use App\Models\articulos\ArticuloFamiliaEntity;
use App\Models\articulos\ArticuloRubrosEntity;
use App\Models\articulos\ArticuloSubfamiliaEntity;
use App\Models\articulos\ArticuloUnidadMedidaEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MantenimientoArticuloController extends Controller
{

    public function getListarFamilia(Request $request)
    {

        $data = ArticuloFamiliaEntity::get();

        return response()->json($data);
    }

    public function getListarSubFamilia(Request $request)
    {

        $data = ArticuloSubfamiliaEntity::get();

        return response()->json($data);
    }

    public function getListarRubroFamilia(Request $request)
    {

        $data = ArticuloRubrosEntity::get();

        return response()->json($data);
    }

    public function getListarUnidaMedidaArticulo(Request $request)
    {

        $data = ArticuloUnidadMedidaEntity::get();

        return response()->json($data);
    }


}
