<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\EmpresaModelo;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    //

    public function getEmpresa()
    {
        $empresa = EmpresaModelo::limit(10)->get();
        return response()->json($empresa, 200);
    }

    public function getEmpresaId($id)
    {
        $query = EmpresaModelo::where('id_empresa', $id)->first();
        return response()->json($query, 200);
    }

    public function getLikeEmpresa($busqueda)
    {
        $query = EmpresaModelo::where('cuit', 'LIKE', "%$busqueda%")
            ->orWhere('razon_social', 'LIKE', "%$busqueda%")->limit(10)->get();
        return response()->json($query, 200);
    }

    public function getFechaEmpresa(Request $request)
    {
        $query = EmpresaModelo::whereBetween('fecha_carga', [$request->desde, $request->hasta])->get();
        return response()->json($query, 200);
    }


    public function postSaveEmpresa(Request $request)
    {
        if ($request->id_empresa != '') {
            $query = EmpresaModelo::where('id_empresa', $request->id_empresa)->first();
            $query->id_empresa = $request->id_empresa;
            $query->razon_social = $request->razon_social;
            $query->id_localidad = $request->id_localidad;
            $query->fecha_alta = $request->fecha_alta;
            $query->fecha_carga = $request->fecha_carga;
            $query->id_usuario = $request->id_usuario;
            $query->telefono = $request->telefono;
            $query->celular = $request->celular;
            $query->cuit = $request->cuit;
            $query->id_provincia = $request->id_provincia;
            $query->observaciones = $request->observaciones;
            $query->fecha_baja = $request->fecha_baja;
            $query->nombre_fantasia = $request->nombre_fantasia;
            $query->email = $request->email;
            $query->id_delegacion = $request->id_delegacion;
            $query->id_actividad_economica = $request->id_actividad_economica;
            $query->tipo_empresa = $request->tipo_empresa;
            $query->domicilio = $request->domicilio;
            $query->save();
            $msg = 'Datos de empresa actualizados correctamente';
        } else {
            $user = Auth::user();
            $empresa = EmpresaModelo::where('cuit', $request->cuit)->first();
            if ($empresa == '') {
                EmpresaModelo::create([
                    'razon_social' => $request->razon_social,
                    'id_localidad' => $request->id_localidad,
                    'fecha_alta' => $request->fecha_alta,
                    'fecha_carga' => $request->fecha_carga,
                    'id_usuario' => $user->cod_usuario,
                    'telefono' => $request->telefono,
                    'celular' => $request->celular,
                    'cuit' => $request->cuit,
                    'id_partido' => $request->id_partido,
                    'id_provincia' => $request->id_provincia,
                    'nombre_fantasia' => $request->nombre_fantasia,
                    'email' => $request->email,
                    'fecha_baja' => $request->fecha_baja,
                    'id_delegacion' => $request->id_delegacion,
                    'id_actividad_economica' => $request->id_actividad_economica,
                    'observaciones' => $request->observaciones,
                    'tipo_empresa' => $request->tipo_empresa,
                    'domicilio' => $request->domicilio,
                ]);
            } else {
                return response()->json(['message' => 'El CUIT de la empresa ya se encuentra registrada'], 500);
            }
            //$request->merge(['id_usuario' => $user->cod_usuario]);

            $msg = 'Datos de empresa registrado correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    public function deleteEmpresa(Request $request)
    {
        EmpresaModelo::where('id_empresa', $request->id)->delete();
        return response()->json(['message' => 'Empresa eliminado correctamente'], 200);
    }
}
