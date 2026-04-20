<?php

namespace App\Http\Controllers\AltaTemporal;

use App\Http\Controllers\Controller;
use App\Models\afiliado\AltaTemporalEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AltaTemporalController extends RoutingController
{

    public function postSavePadron(Request $request)
    {
        $now = Carbon::now('America/Argentina/Buenos_Aires');
        $user = Auth::user();
        if ($request->id != '') {
            try {

                DB::beginTransaction();

                $query = AltaTemporalEntity::where('id', $request->id)->first();

                $query->cuil_tit = $request->cuil_tit;
                $query->cuil_benef = $request->cuil_benef;
                $query->id_tipo_documento = $request->id_tipo_documento;
                $query->dni = $request->dni;
                $query->nombre = $request->nombre;
                $query->apellidos = $request->apellidos;
                $query->id_sexo = $request->id_sexo;
                $query->id_estado_civil = $request->id_estado_civil;
                $query->fe_nac = $request->fe_nac;
                $query->fe_alta = $request->fe_alta;
                $query->id_usuario = $request->id_usuario;
                $query->fecha_carga = $request->fecha_carga;
                $query->id_tipo_beneficiario = $request->id_tipo_beneficiario;
                $query->id_parentesco = $request->id_parentesco;
                $query->fe_baja = $request->fe_baja;
                $query->observaciones = $request->observaciones;
                $query->id_locatario = $request->id_locatario;

                $query->save();

                DB::commit();
                $msg = 'Datos actualizados correctamente';
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['message' => $th->getMessage()], 500);
            }
        } else {
            try {
                DB::beginTransaction();
                $dni = AltaTemporalEntity::where('dni', $request->dni)->first();

                if (!$dni) {
                    $user = Auth::user();
                    AltaTemporalEntity::create([
                        'cuil_tit' => $request->cuil_tit,
                        'cuil_benef' => $request->cuil_benef,
                        'id_tipo_documento' => $request->id_tipo_documento,
                        'dni' => $request->dni,
                        'nombre' => $request->nombre,
                        'apellidos' => $request->apellidos,
                        'id_sexo' => $request->id_sexo,
                        'id_estado_civil' => $request->id_estado_civil,
                        'fe_nac' => $request->fe_nac,
                        'fe_alta' => $request->fe_alta,
                        'id_usuario' => $user->cod_usuario,
                        'id_parentesco' => $request->id_parentesco,
                        'fe_baja' => $request->fe_baja,
                        'activo' => $request->activo,
                        'observaciones' => $request->observaciones,
                        'id_locatario' => $request->id_locatario,
                    ]);

                    $msg = 'Datos registrados correctamente';
                } else {
                    return response()->json(['message' => 'Ya existe un afiliado con el mismo número de documento'], 500);
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['message' => $th->getMessage()], 500);
            }
        }
        return response()->json(['message' => $msg], 200);
    }

    public function getLikePadron(Request $request)
    {
        $user = Auth::user();
        $query = AltaTemporalEntity::with([
            'tipoParentesco',
            'sexo' ,
            'obrasocial' ,
        ]);
        if (!empty($request->dni)) {
            $query->where(function ($q) use ($request) {
                $q->where('dni', 'like', $request->dni . '%')
                    ->orWhere('cuil_tit', 'like', '%' . $request->dni . '%')
                    ->orWhere('cuil_benef', 'like', '%' . $request->dni . '%')
                    ->orWhere('nombre', 'like', '%' . $request->dni . '%')
                    ->orWhere('apellidos', 'like', '%' . $request->dni . '%');
            });
        }

        if (!empty($request->desde) && !empty($request->hasta)) {
            $query->whereBetween('fecha_carga', [$request->desde, $request->hasta]);
        }

        $files = $query
            ->limit(50)
            ->get();

        return response()->json($files, 200);
    }
}
