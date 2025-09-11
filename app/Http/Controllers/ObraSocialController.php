<?php

namespace App\Http\Controllers;

use App\Models\ObrasSocialesModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ObraSocialController extends Controller
{
    //
    public function getObraSocial()
    {
        $query =  ObrasSocialesModelo::get();
        return response()->json($query, 200);
    }
}
