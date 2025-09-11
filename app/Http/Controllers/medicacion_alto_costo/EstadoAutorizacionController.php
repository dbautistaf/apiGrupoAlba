<?php

namespace App\Http\Controllers\medicacion_alto_costo;

use App\Models\TipoAutorizacion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstadoAutorizacionController extends Controller
{
    //
    public function getEstadoAutorizacion()
    {
        $query = TipoAutorizacion::where('estado', '1')->get();
        return response()->json($query, 200);
    }
}
