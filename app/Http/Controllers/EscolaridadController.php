<?php

namespace App\Http\Controllers;

use App\Models\EscolaridadModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EscolaridadController extends Controller
{
    //
    public function getEscolaridad($idPadron)
    {
        $escolaridad =  EscolaridadModelo::where('id_padron',$idPadron)->first();
        return response()->json($escolaridad, 200);
    }

    public function saveEscolaridad(Request $request){
        if($request->id != ''){
            $query = EscolaridadModelo::where('id', $request->id)->first();
            $query->nivel_estudio= $request->nivel_estudio;
            $query->fecha_presentacion=$request->fecha_presentacion;
            $query->fecha_vencimiento=$request->fecha_vencimiento;
            $query->id_padron= $request->id_padron;
            $query->save();
            $msg = 'Datos actualizados correctamente';
        }else{
            $escolaridad =  EscolaridadModelo::where('id_padron',$request->id_padron)->first();
            if($escolaridad){
                return response()->json(['message' => 'El afiliado ya tiene un registro de escolaridad'], 500);
            }else{
                EscolaridadModelo::create($request->all());
                $msg = 'Datos de Escolaridad registrado correctamente';
            }
            
        }
        return response()->json(['message' => $msg], 200);
    }
}
