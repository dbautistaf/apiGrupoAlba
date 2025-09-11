<?php

namespace App\Http\Controllers;

use App\Models\PeriodoModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PeriodoController extends Controller
{
    //
    public function getPeriodo(){
        $periodo =  PeriodoModelo::get(); 
        return response()->json($periodo, 200);
    }
}
