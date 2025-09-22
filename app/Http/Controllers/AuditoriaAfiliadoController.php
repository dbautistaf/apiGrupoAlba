<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaPadronModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuditoriaAfiliadoController extends Controller
{
    //
    function getListIDAuditoria($id)
    {
        $datos=[];
        $auditoria = AuditoriaPadronModelo::with('Usuario')->where('id_padron', $id)->get();
        /* foreach ($auditoria as $array) {
            $array->antes=json_decode($array->antes, true);
            $array->ahora=json_decode($array->ahora, true);
            array_push($datos,$array);
        } */
        return response()->json($auditoria, 200);
    }
}
