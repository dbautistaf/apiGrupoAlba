<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\SexoModelo;

class SexoController extends Controller
{
    //
    public function getSexo(){
        $query =  SexoModelo::get();
        return response()->json($query, 200);
    }
}
