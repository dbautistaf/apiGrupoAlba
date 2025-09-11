<?php

namespace App\Http\Controllers\medicacion_alto_costo;

use App\Models\ModoEntrega;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ModoEntregaController extends Controller
{
    //lista los modo de entrega con estado activo (1)
    public function getModoEntrega()
    {
        $query = ModoEntrega::where('estado', '1')->get();
        return response()->json($query, 200);
    }
}
