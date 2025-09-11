<?php

namespace App\Http\Controllers\medicacion_alto_costo;

use App\Models\EstadoTratamiento;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstadoTratamientoController extends Controller
{
    //Lista los estado de tratamiento con estado activo (1)
    public function getEstadoTratamiento()
    {
        $query = EstadoTratamiento::where('estado', '1')->get();
        return response()->json($query, 200);
    }
}
