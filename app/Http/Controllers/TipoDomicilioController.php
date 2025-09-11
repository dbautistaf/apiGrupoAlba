<?php

namespace App\Http\Controllers;

use App\Models\TIpoDomicilioModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TipoDomicilioController extends Controller
{
    //
    public function getTipoDomicilio(){
        $query =  TIpoDomicilioModelo::get();
        return response()->json($query, 200);
    }
}
