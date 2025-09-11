<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\Institucion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InstitucionesController extends Controller
{

    public function getListInstituciones()
    {
        $query = Institucion::all();
        return response()->json($query, 200);
    }

}