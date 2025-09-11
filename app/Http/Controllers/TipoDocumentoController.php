<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\tipo_documento;

class TipoDocumentoController extends Controller
{
    //
    public function getTipoDocumento(){
        $query =  tipo_documento::get();
        return response()->json($query, 200);
    }
}
