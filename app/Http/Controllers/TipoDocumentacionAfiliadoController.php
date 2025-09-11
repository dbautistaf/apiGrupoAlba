<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumentacionAfiliadoModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TipoDocumentacionAfiliadoController extends Controller
{
    //
    public function getTipoDocumentacionAfiliado(){
        $tipoDoc =  TipoDocumentacionAfiliadoModelo::get();
        return response()->json($tipoDoc, 200);
    }
}
