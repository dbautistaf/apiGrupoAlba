<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ActividadEconomicaModelo;

class ActividadEconomicaController extends Controller
{
    //
    public function getActividadEconomica(){
        $actividad =  ActividadEconomicaModelo::get();
        return response()->json($actividad, 200);
    }
}
