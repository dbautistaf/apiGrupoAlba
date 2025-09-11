<?php

namespace App\Http\Controllers;

use App\Models\RegimenModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RegimenController extends Controller
{
    //
    public function getRegimen(){
        $datos =  RegimenModelo::get();
        return response()->json($datos, 200);
    }
    
}
