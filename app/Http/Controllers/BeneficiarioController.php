<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BeneficiarioModelo;

class BeneficiarioController extends Controller
{
    //
    public function getBeneficiario(){
        $beneficiario =  BeneficiarioModelo::get();
        return response()->json($beneficiario,200);
    }
}
