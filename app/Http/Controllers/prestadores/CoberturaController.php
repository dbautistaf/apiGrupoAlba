<?php

namespace App\Http\Controllers\prestadores;

use App\Models\afiliado\AfiliadoTipoCoberturaEntity;
use Illuminate\Routing\Controller;

class CoberturaController extends Controller
{
    public function getTipoCoberturaAfiliado(){
        $query = AfiliadoTipoCoberturaEntity::orderBy('id_cobertura')
        ->get();
        return response()->json($query, 200);
    }
}
