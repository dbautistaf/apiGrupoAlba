<?php

namespace App\Http\Controllers\medicos;

use App\Models\medicos\TurnosMedicosEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TurnosController extends Controller
{
    //
    public function getListTurnos(Request $request)
    {
        if ($request->id != '') {
            $query = TurnosMedicosEntity::with('afiliado', 'afiliado.tipoParentesco', 'afiliado.detalleplan.TipoPlan', 'medico', 'centro', 'especialidad')->where('id_turno', $request->id)->first();
        } else {
            $query = TurnosMedicosEntity::with(['afiliado', 'medico', 'centro', 'especialidad'])->get();
        }
        return response()->json($query, 200);
    }

    public function getLikeTurnos($datos)
    {
        $query = TurnosMedicosEntity::with('afiliado', 'afiliado.tipoParentesco', 'afiliado.detalleplan.TipoPlan', 'medico', 'centro', 'especialidad')->where(function ($query) use ($datos) {
            $query->whereHas('Afiliado', function ($queryAfiliado) use ($datos) {
                $queryAfiliado->where('nombre', 'LIKE', "$datos%")->orWhere('dni', 'LIKE', "$datos%");
            })->orWhereHas('medico', function ($queryFarmacia) use ($datos) {
                $queryFarmacia->where('nombre', 'LIKE', "$datos%");
            })->orWhereHas('centro', function ($queryFarmacia) use ($datos) {
                $queryFarmacia->where('nombre', 'LIKE', "$datos%");
            });
        })->get();
        return response()->json($query, 200);
    }

    public function getFechaTurno(Request $request)
    {
        $query = TurnosMedicosEntity::with(['afiliado', 'medico', 'centro', 'especialidad'])->whereBetween('fecha_carga', [$request->desde, $request->hasta])
            ->get();
        return response()->json($query, 200);
    }

    public function saveTurnos(Request $request)
    {
        if ($request->id_turno != '') {
            $query = TurnosMedicosEntity::where('id_turno', $request->id_turno)->first();
            $query->fecha_desde = $request->fecha_desde;
            $query->fecha_hasta = $request->fecha_hasta;
            $query->horario_inicio =  $request->horario_inicio;
            $query->horario_fin =  $request->horario_fin;
            $query->estado =  $request->estado;
            $query->id_afiliado =  $request->id_afiliado;
            $query->id_centro_medico =  $request->id_centro_medico;
            $query->id_medico =  $request->id_medico;
            $query->id_locatorio =  $request->id_locatorio;
            $query->id_especialidad =  $request->id_especialidad;
            $query->id_usuario = $request->id_usuario;
            $query->save();
            $msg = 'Turno actualizado correctamente';
        } else {
            $user = Auth::user();
            TurnosMedicosEntity::create([
                'fecha_desde' => $request->fecha_desde,
                'fecha_hasta' => $request->fecha_hasta,
                'horario_inicio' => $request->horario_inicio,
                'horario_fin' => $request->horario_fin,
                'estado' => $request->estado,
                'id_afiliado' => $request->id_afiliado,
                'id_centro_medico' => $request->id_centro_medico,
                'id_medico' => $request->id_medico,
                'id_locatorio' => $request->id_locatorio,
                'id_especialidad' => $request->id_especialidad,
                'id_usuario' => $user->cod_usuario
            ]);
            $msg = 'Turno registrado correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    public function deleteTurnos(Request $request)
    {
        TurnosMedicosEntity::where('id_turno', $request->id_turno)->delete();
        return response()->json(['message' => 'Turno eliminado correctamente'], 200);
    }
}
