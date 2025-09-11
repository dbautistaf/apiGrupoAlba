<?php

namespace App\Http\Controllers;

use App\Models\AdhesionAfipModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdhesionAfipController extends Controller
{
    //
    public function getAdhesionAfip(){
        $query =  AdhesionAfipModelo::get();
        return response()->json($query, 200);
    }
}
