<?php

namespace App\Http\Controllers\medicacion_alto_costo;

use App\Models\TipoAutorizacion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TipoAutorizacionController extends Controller
{
    //Lista los tipo de autorizacion con estado activo (1)
    public function getTipoAutorizacion()
    {
        $query = TipoAutorizacion::where('estado', '1')->get();
        return response()->json($query, 200);
    }
}
