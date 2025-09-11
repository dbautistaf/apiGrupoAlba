<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\DeledagosModelo;

class DelegadosController extends Controller
{
    //
    public function getDelegacion(){
        $cpostal =  DeledagosModelo::get();
        return response()->json($cpostal, 200);
    }
}
