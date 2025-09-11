<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\NacionalidadModelo;

class NacionalidadController extends Controller
{
    //
    public function getNacionalidad(){
        $nacionalidad =  NacionalidadModelo::get();
        return response()->json($nacionalidad, 200);
    }
}
