<?php

namespace App\Http\Controllers;

use App\Models\DesempleoModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DesempleoController extends Controller
{
    //
    public function getSuperPadron(){
        $query =  DesempleoModelo::get();
        return response()->json($query, 200);
    }
}
