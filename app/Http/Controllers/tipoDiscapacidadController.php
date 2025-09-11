<?php

namespace App\Http\Controllers;

use App\Models\afiliado\AfiliadoTipoDiscapacidad;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class tipoDiscapacidadController extends Controller
{
    //
    public function getTipoDiscapacidad(){
        $tipoDisc =  AfiliadoTipoDiscapacidad::get();
        return response()->json($tipoDisc, 200);
    }
}
