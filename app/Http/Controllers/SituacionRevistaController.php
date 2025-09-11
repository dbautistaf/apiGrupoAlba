<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\SituacionRevistaModelo;

class SituacionRevistaController extends Controller
{
    //
    public function getSituacion(){
        $query =  SituacionRevistaModelo::get();
        return response()->json($query, 200);
    }
}
