<?php

namespace App\Http\Controllers\tratamientoProlongado;

use App\Http\Controllers\Controller;
use App\Models\tratamientoProlongado\detalleTratamientoModel;
use App\Models\tratamientoProlongado\tratamientoProlongadoModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class tratamientoProlongadoController extends RoutingController
{
    //

    public function getTratamientoProlongado(Request $request)
    {
        $datos = $request->search;
        if (is_null($request->search)) {
            $query =  tratamientoProlongadoModel::with('afiliado')->get();
        } else {
            $query =  tratamientoProlongadoModel::with('afiliado')->where(function ($query) use ($datos) {
                $query->whereHas('afiliado', function ($queryAfiliado) use ($datos) {
                    $queryAfiliado->where('nombre', 'LIKE', "$datos%")->orWhere('dni', 'LIKE', "$datos%")
                        ->orWhere('apellidos', 'LIKE', "$datos%");
                });
            })->whereBetween(DB::raw('DATE(fecha_proceso)'), [$request->desde, $request->hasta])->get();
        }

        return response()->json($query, 200);
    }

    public function getTratamientoId(Request $request)
    {
        return tratamientoProlongadoModel::with(['detalles.vademecum', 'especialidad', 'afiliado'])->where('id_tratamiento', $request->id)->firstOrFail();
    }

    public function saveTratamiento(Request $request)
    {
        $now = new \DateTime();
        //return response()->json($request->detalle, 200);
        if ($request->id_tratamiento) {
            $query = tratamientoProlongadoModel::where('id_tratamiento', $request->id_tratamiento)->first();
            $query->dni_afiliado = $request->dni_afiliado;
            $query->edad = $request->edad;
            $query->nro_ingreso = $request->nro_ingreso;
            $query->fecha_proceso = $request->fecha_proceso;
            $query->nombres_medico = $request->nombres_medico;
            $query->especialidad_medico = $request->especialidad_medico;
            $query->telefono_medico = $request->telefono_medico;
            $query->email_medico = $request->email_medico;
            $query->fecha_inicio_tratamiento = $request->fecha_inicio_tratamiento;
            $query->fecha_fin_tratamiento = $request->fecha_fin_tratamiento;
            $query->id_usuario = $request->id_usuario;
            $query->observaciones = $request->observaciones;
            $query->save();
            if (count($request->detalle) > 0) {
                foreach ($request->detalle as $detalle) {
                    if ($detalle['id_detalle'] == "") {
                        detalleTratamientoModel::create([
                            'id_vademecum' => $detalle['id_vademecum'],
                            'nombre_comercial' => $detalle['nombre_comercial'],
                            'dosis' => $detalle['dosis'],
                            'envases_mensuales' => $detalle['envases_mensuales'],
                            'id_tratamiento' => $query->id_tratamiento,
                        ]);
                    } else {
                        $querydetalle = detalleTratamientoModel::where('id_detalle', $detalle['id_detalle'])->first();
                        $querydetalle->id_vademecum = $detalle['id_vademecum'];
                        $querydetalle->nombre_comercial = $detalle['nombre_comercial'];
                        $querydetalle->dosis = $detalle['dosis'];
                        $querydetalle->envases_mensuales = $detalle['envases_mensuales'];
                        $querydetalle->id_tratamiento = $querydetalle->id_tratamiento;
                        $querydetalle->save();
                    }
                }
            }

            return response()->json(["message" => "Tratamiento prolongado actualizado con éxito"], 200);
        } else {
            $user = Auth::user();
            //$request->id_usuario=$user->id_usuario;
            $tratamiento = tratamientoProlongadoModel::create([
                'dni_afiliado' => $request->dni_afiliado,
                'edad' => $request->edad,
                'nro_ingreso' => $request->nro_ingreso,
                'fecha_proceso' => $now->format('Y-m-d H:i:s'),
                'nombres_medico' => $request->nombres_medico,
                'especialidad_medico' => $request->especialidad_medico,
                'telefono_medico' => $request->telefono_medico,
                'email_medico' => $request->email_medico,
                'fecha_inicio_tratamiento' => $request->fecha_inicio_tratamiento,
                'fecha_fin_tratamiento' => $request->fecha_fin_tratamiento,
                'id_usuario' => $user->cod_usuario,
                'observaciones' => $request->observaciones
            ]);

            if (count($request->detalle) > 0) {
                foreach ($request->detalle as $detalle) {
                    detalleTratamientoModel::create([
                        'id_vademecum' => $detalle['id_vademecum'],
                        'nombre_comercial' => $detalle['nombre_comercial'],
                        'dosis' => $detalle['dosis'],
                        'envases_mensuales' => $detalle['envases_mensuales'],
                        'id_tratamiento' => $tratamiento->id_tratamiento,
                    ]);
                }
            }
            return response()->json(["message" => "Tratamiento prolongado actualizado con éxito"], 200);
        }
    }

    public function deleteTratamiento(Request $request)
    {
        detalleTratamientoModel::where('id_tratamiento', $request->id)->delete();
        tratamientoProlongadoModel::where('id_tratamiento', $request->id)->delete();
        return response()->json(['message' => 'Tratamiento prolongado eliminado correctamente'], 200);
    }
}
