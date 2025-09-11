<?php

namespace App\Http\Controllers\Reclamos;

use App\Http\Controllers\Controller;
use App\Models\Reclamos\TipoReclamosModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;

class TipoReclamosController extends RoutingController
{
    //
    public function getReclamosActivos()
    {
        $query =  TipoReclamosModel::where('activo', '1')->get();
        return response()->json($query, 200);
    }
}
