<?php

namespace App\Http\Controllers;

use App\Models\PatalogiaModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class patalogiaController extends Controller
{
    //
    public function getPatalogia(){
        $query =  PatalogiaModelo::get();
        return response()->json($query, 200);
    }
}
