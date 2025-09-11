<?php

namespace App\Http\Controllers;

use App\Models\TipoCarpetaModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TipoCarpetaController extends Controller
{
    //
    public function getTipoCarpeta(){
        $datos =  TipoCarpetaModelo::get();
        return response()->json($datos, 200);
    }
}
