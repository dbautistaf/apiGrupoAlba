<?php

namespace App\Http\Controllers;

use App\Models\SindicatosModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SindicatoController extends Controller
{
    public function index()
    {
        return response()->json(SindicatosModelo::orderBy("nombre_sindicato")->get(),200);
    }

    public function show($id)
    {
        return response()->json(SindicatosModelo::find($id),200);
    }
}
