<?php

namespace App\Http\Controllers;

use App\Models\EstadoAutorizacionModelos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstadoAutorizacionController extends Controller
{
    //
    public function getAutorizacion(){
        $datos =  EstadoAutorizacionModelos::get();
        return response()->json($datos, 200);
    }
}
