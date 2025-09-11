<?php

namespace App\Http\Controllers;

use App\Models\TipoQrModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TipoQrController extends Controller
{
    //
    public function getQr(){
        $datos =  TipoQrModelo::get();
        return response()->json($datos, 200);
    }
}
