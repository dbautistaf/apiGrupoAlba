<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\PartidosModelo;

class PartidosController extends Controller
{
    //
    public function getPartidos($idProv){
        $partido =  PartidosModelo::where('id_provincia',$idProv)->get();
        return response()->json($partido, 200);
    }
}
