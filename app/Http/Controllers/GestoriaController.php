<?php

namespace App\Http\Controllers;

use App\Models\GestoriaModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GestoriaController extends Controller
{
    //
    public function getGestoria(){
        $gestoria =  GestoriaModelo::get();
        return response()->json($gestoria, 200);
    }
}
