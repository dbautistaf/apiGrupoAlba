<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\BancosCobranza;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BancosCobranzaController extends Controller
{

    public function getListBancosCobranza()
    {
        $query = BancosCobranza::orderBy('descripcion_banco', 'asc')->get();
    return response()->json($query, 200);
    }

}