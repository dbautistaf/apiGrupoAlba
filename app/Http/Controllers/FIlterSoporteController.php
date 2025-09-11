<?php

namespace App\Http\Controllers;

use App\Models\Soporte\SoporteCategoriaModelo;
use App\Models\SoporteEstadoModelo;
use App\Models\SoporteInstanciaModelo;
use App\Models\SoportePrioridadModelo;
use App\Models\SoporteProductosModelo;
use App\Models\SoporteUsuarioSoporteModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FIlterSoporteController extends Controller
{
    //
    public function soportePrioridad()
    {
        $datos =  SoportePrioridadModelo::get();
        return response()->json($datos, 200);
    }
    public function soporteCategoria()
    {
        $datos =  SoporteCategoriaModelo::get();
        return response()->json($datos, 200);
    }

    public function soporteProductos()
    {
        $datos =  SoporteProductosModelo::get();
        return response()->json($datos, 200);
    }

    public function soporteEstado()
    {
        $datos =  SoporteEstadoModelo::get();
        return response()->json($datos, 200);
    }

    public function soporteInstancia()
    {
        $datos =  SoporteInstanciaModelo::get();
        return response()->json($datos, 200);
    }

    public function soportetarea()
    {
        $datos =  SoporteUsuarioSoporteModelo::get();
        return response()->json($datos, 200);
    }
}
