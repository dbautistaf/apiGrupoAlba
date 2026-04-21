<?php

namespace App\Http\Controllers\AltaTemporal;

use App\Http\Controllers\Controller;
use App\Models\afiliado\AltaTemporalEntity;
use Barryvdh\DomPDF\Facade\Pdf;
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
        if ($request->id_temporal != '') {
            try {

                DB::beginTransaction();

                $query = AltaTemporalEntity::where('id_temporal', $request->id_temporal)->first();

                $query->cuil_tit = $request->cuil_tit;
                $query->cuil_benef = $request->cuil_benef;
                $query->id_tipo_documento = $request->id_tipo_documento;
                $query->dni = $request->dni;
                $query->nombre = $request->nombre;
                $query->apellidos = $request->apellidos;
                $query->id_sexo = $request->id_sexo;
                $query->fe_nac = $request->fe_nac;
                $query->fe_alta = $request->fe_alta;
                $query->id_usuario = $request->id_usuario;
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
                        'fe_nac' => $request->fe_nac,
                        'fe_alta' =>$now->format('Y-m-d'),
                        'id_usuario' => $user->cod_usuario,
                        'id_parentesco' => $request->id_parentesco,
                        'fe_baja' => $request->fe_baja,
                        'activo' => $request->activo,
                        'observaciones' => $request->observaciones,
                        'id_locatario' => $request->id_locatario,
                        'id_tipo_beneficiario' => $request->id_tipo_beneficiario,
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
            $query->whereBetween('fe_alta', [$request->desde, $request->hasta]);
        }

        $files = $query
            ->OrderBy('fe_alta')
            ->limit(50)
            ->get();

        return response()->json($files, 200);
    }

    public function printCarnetPersonal(Request $request)
    {

        $now = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $fecha_inicio = $now->format('Y-m-d');
        $fecha_final = $now->modify('+2 days')->format('Y-m-d');
        $datos = AltaTemporalEntity::with('tipoParentesco')->where('dni', $request->dni)->where('activo', '1')->get();
        if ($datos[0]->activo != 0) {
            $now = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
            $datos = AltaTemporalEntity::with( 'tipoParentesco')->where('dni', $request->dni)->get();
            $grupal = AltaTemporalEntity::with( 'tipoParentesco')->where('cuil_tit', $datos[0]->cuil_tit)
                ->OrderBy('id_parentesco', 'asc')->get();

            $correlativo = null;
            foreach ($grupal as $index => $item) {
                if ($item->dni == $request->dni) {
                    $correlativo = $index;
                    break;
                }
            }
            $datos = $datos->map(function ($afiliado) use ($correlativo) {
                $afiliado->correlativo = $correlativo;
                return $afiliado;
            });

            if ($datos) {
                foreach ($datos as $afiliado) {
                    $afiliado["cuil_benef"] = $afiliado->dni;
                }
                $pdf = Pdf::loadView('carnet_afiliado', ["data" => $datos, "f_inicio" => $fecha_inicio, "f_fin" => $fecha_final, "plan" => $grupal]);
                $pdf->setPaper('A5', 'landscape');
                return $pdf->download('carnet.pdf');
            }
        } else {
            return response()->json(['error' => 'El usuario esta inactivo. Muchas gracias.'], 404);
        }
    }

    public function getDniPadron(Request $request)
    {
        $query = AltaTemporalEntity::where('dni', $request->dni)->first();
        if ($query) {
            return response()->json($query, 200);
        } else {
            return response()->json(['message' => 'No se encontró el registro con el numero DNI ingresado'], 500);
        }
    }
}
