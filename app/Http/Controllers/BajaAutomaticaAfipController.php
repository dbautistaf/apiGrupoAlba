<?php

namespace App\Http\Controllers;

use App\Models\BajaAutomaticaAfipModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BajaAutomaticaAfipController extends Controller
{
    //
    public function getBajaAUtomatica(){
        $query =  BajaAutomaticaAfipModelo::get();
        return response()->json($query, 200);
    }
    
}
