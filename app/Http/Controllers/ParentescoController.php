<?php

namespace App\Http\Controllers;

use App\Models\afiliado\AfiliadoTipoParentescoEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ParentescoController extends Controller
{
    //
    public function getParentesco(){
        $parentesco =  AfiliadoTipoParentescoEntity::get();
        return response()->json($parentesco, 200);
    }
}
