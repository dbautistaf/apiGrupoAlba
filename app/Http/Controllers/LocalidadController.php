<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\LocalidadModelo;

class LocalidadController extends Controller
{
    //
    public function getLocalidad($idProv,$idParido){
        $localidad =  LocalidadModelo::where('id_provincia',$idProv)
                        ->where('id_partido', '=', $idParido)
                        ->get();
        return response()->json($localidad, 200);
    }
}
