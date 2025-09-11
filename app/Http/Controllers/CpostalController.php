<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\CpostalModelo;

class CpostalController extends Controller
{
    //
    public function getCpostal(){
        $cpostal =  CpostalModelo::get();
        return response()->json($cpostal, 200);
    }
}
