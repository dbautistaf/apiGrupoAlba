<?php

namespace App\Http\Controllers;

use App\Models\GerentesModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GerentesController extends Controller
{
    //
    public function getGerentes(){
        $gerentes =  GerentesModelo::get();
        return response()->json($gerentes, 200);
    }
}
