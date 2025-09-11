<?php

namespace App\Http\Controllers;

use App\Models\SupervisoresModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SupervisoresController extends Controller
{
    //
    public function getSupervisores(){
        $datos =  SupervisoresModelo::get();
        return response()->json($datos, 200);
    }
}
