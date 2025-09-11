<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\EstadoCivilModelo;

class EstadoCivilController extends Controller
{
    //
    public function getEstadoCivil(){
        $estado =  EstadoCivilModelo::get();
        return response()->json($estado, 200);
    }
}
