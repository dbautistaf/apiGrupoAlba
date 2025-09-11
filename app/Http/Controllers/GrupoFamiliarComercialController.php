<?php

namespace App\Http\Controllers;

use App\Models\GrupoFamiliarComercialModelo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class GrupoFamiliarComercialController extends Controller
{
    //
    public function getFamiliarAfiliadoComercial($cuil_tit)
    {
        $familiar =  GrupoFamiliarComercialModelo::where('cuil_titular', $cuil_tit)->get();
        $newFamiliar=[];
        foreach ($familiar as $edad) {
            $fechaNacimiento = Carbon::parse($edad->fec_nac);
            $fechaActual = Carbon::now();
            $diferencia = $fechaNacimiento->diff($fechaActual);
           
            $newFamiliar[]=[
                'id'=>$edad->id,
                'apellidos'=>$edad->apellidos,
                'cuil_benef'=>$edad->cuil_benef,
                'dni'=>$edad->dni,
                'nombres'=>$edad->nombres,
                'fec_nac'=>$edad->fec_nac,
                'edad'=>$diferencia->y,
                'sexo'=>$edad->sexo,
                'nacionalidad'=>$edad->nacionalidad,
                'discapacidad'=>$edad->discapacidad
            ];
        }
       
        return response()->json($newFamiliar, 200);
    }

    public function getIDFamiliarPadronComercial($id)
    {
        $query = GrupoFamiliarComercialModelo::where('id', $id)->first();
        return response()->json($query, 200);
    }

    public function UpdateEstadoFamiliarComercial(Request $request)
    {
        $now = new \DateTime();
        $fecha = '1900-01-01';
        $query = GrupoFamiliarComercialModelo::where('id', $request->id)->first();
        if ($query->fe_baja == $fecha) {
            $fecha = $now->format('Y-m-d');
        }
        GrupoFamiliarComercialModelo::where('id', $request->id)->update(['activo' => $request->activo, 'fe_baja' => $fecha]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }

    public function savePadronFamiliarComercial(Request $request)
    {
        if ($request->id != '') {
            $query = GrupoFamiliarComercialModelo::where('id', $request->id)->first();

            $query->cuil_titular = $request->cuil_titular;
            $query->cuil_benef = $request->cuil_benef;
            $query->dni = $request->dni;
            $query->apellidos = $request->apellidos;
            $query->nombres = $request->nombres;
            $query->fec_nac = $request->fec_nac;
            $query->nacionalidad = $request->nacionalidad;
            $query->sexo = $request->sexo;
            $query->discapacidad = $request->discapacidad;
            $query->id_parentesco = $request->id_parentesco;
            $query->id_estado_civil = $request->id_estado_civil;
            $query->id_usuario = $request->id_usuario;
            $query->save();
            $msg = 'Datos actualizados correctamente';
        } else {
            $dni = GrupoFamiliarComercialModelo::where('dni', $request->dni)->first();
            if (!$dni) {

                $user = Auth::user();
                $request->merge(['id_usuario' => $user->cod_usuario]);
                GrupoFamiliarComercialModelo::create($request->all());
            } else {
                return response()->json(['message' => 'El número de documento ya se encuentra registrado en base de datos'], 500);
            }
            $msg = 'Datos registrados correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    
    public function deletePadronFamiliar(Request $request)
    {
        GrupoFamiliarComercialModelo::where('id', $request->id)->delete();
        return response()->json(['message' => 'Familiar eliminado correctamente'], 200);
    }
}
