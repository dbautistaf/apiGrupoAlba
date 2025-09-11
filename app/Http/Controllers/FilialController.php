<?php

namespace App\Http\Controllers;

use App\Models\FilialModelos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FilialController extends Controller
{
    //
    public function getFilial(){
        $datos =  FilialModelos::where('activo','1')->get();
        return response()->json($datos, 200);
    }
}
