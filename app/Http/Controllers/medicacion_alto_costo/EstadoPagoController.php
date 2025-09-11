<?php

namespace App\Http\Controllers\medicacion_alto_costo;

use App\Models\EstadoPago;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstadoPagoController extends Controller
{
    //Lista los estado de pago con estado activo (1)
    public function getEstadoPago()
    {
        $query = EstadoPago::where('estado', '1')->get();
        return response()->json($query, 200);
    }
}
