<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\FormasPago;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FormasPagoController extends Controller
{

    public function getListFormasPago()
    {
        $query = FormasPago::all();
        return response()->json($query, 200);
    }

}