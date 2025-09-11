<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ProvinciasModelo;

class ProvinciasController extends Controller
{
    //
    public function getProvincia(){
        $query =  ProvinciasModelo::get();
        return response()->json($query, 200);
    }
}
