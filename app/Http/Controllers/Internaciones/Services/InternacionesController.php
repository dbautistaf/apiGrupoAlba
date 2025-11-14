<?php

namespace  App\Http\Controllers\Internaciones\Services;

use App\Exports\Internacion\InternacionExport;
use App\Http\Controllers\Internaciones\Repository\InternacionesAutorizacionRepository;
use App\Http\Controllers\Internaciones\Repository\InternacionesRepository;
use App\Http\Controllers\Internaciones\Repository\InternacionFiltrosRepository;
use App\Models\Internaciones\InternacionesEntity;
use App\Models\Internaciones\InternacionesNotasEntity;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InternacionesController  extends Controller
{

    public function getObtenerInternacionId(InternacionesRepository $repoInternacion, Request $request)
    {
        $data = $repoInternacion->findByPrestacionInternacionId($request->id);
        return response()->json($data);
    }


    public function getConsultarInternaciones(InternacionFiltrosRepository $repoInternacionFiltro, Request $request)
    {
        $data = [];

        if (!empty($request->search)) {
            if (is_numeric($request->search)) {
                $data = $repoInternacionFiltro->findByListDniLikeAndLimit($request->search, 20);
            } else {
                $data = $repoInternacionFiltro->findByListNombresLikeAndLimit($request->search, 20);
            }
        } elseif (!empty($request->estado)) {
            $data = $repoInternacionFiltro->findByListEstadoLimit($request->estado, 20);
        } elseif (!empty($request->interestado)) {
            $data = $repoInternacionFiltro->findByListNewEstadoLimit($request->interestado, 20);
        } else {
            $data = $repoInternacionFiltro->findByListLimit(20);
        }

        foreach ($data as $key) {
            $detalle = $repoInternacionFiltro->finByListaDetallePrestaciones($key->cod_internacion);
            $key->setAttribute('show', false);
            $key->setAttribute('detalle', $detalle);
        }

        return response()->json($data, 200);
    }

    public function getProcesarInternacion(
        InternacionesRepository $repoInternacion,
        InternacionesAutorizacionRepository $repoInterAut,
        Request $request
    ) {
        try {
            DB::beginTransaction();
            $message = "Internación registrado correctamente.";
            if (!is_null($request->cod_internacion)) {
                $repoInternacion->findByUpdate($request);
                $repoInterAut->findByUpdate($request->id_internacion_autorizacion, $request->cod_internacion);
                $message = "Internación actualizado correctamente.";
            } else {
                $internacion = $repoInternacion->findBySave($request);
                $repoInterAut->findBySave($request->id_internacion_autorizacion, $internacion->cod_internacion);
            }
            DB::commit();
            return response()->json(["message" => $message], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getId(InternacionFiltrosRepository $repoInternacion, Request $request)
    {
        $data = $repoInternacion->findById($request->id);
        $detalle = $repoInternacion->finByListaDetallePrestaciones($data->cod_internacion);
        $data->setAttribute('show', false);
        $data->setAttribute('detalle', $detalle);

        return response()->json($data, 200);
    }

    public function getBuscarInternacionesDNI(InternacionFiltrosRepository $repoInternacion, Request $request)
    {
        $data = $repoInternacion->findByListDni($request->dni);
        return response()->json($data, 200);
    }

    public function getEliminarInternacion(InternacionFiltrosRepository $repoFiltroInternacion, InternacionesRepository $repoInternacion, Request $request)
    {
        if ($repoFiltroInternacion->findByIdExistsAndEstado($request->id, 2)) {
            if (!$repoFiltroInternacion->findByExistAndPrestaciones($request->id)) {
                $repoInternacion->findByDeleteId($request->id);
                return response()->json(['message' => 'Internacion eliminada correctamente']);
            } else {
                return response()->json(['message' => 'No se puede eliminar una internacion cuando ya tiene cargada mas de una prestacion medica'], 409);
            }
        } else {
            return response()->json(['message' => 'No se puede eliminar una internacion ya auditado'], 409);
        }
    }

    public function postUpdateEstado(Request $request)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        InternacionesEntity::where('cod_internacion', $request->cod_internacion)->update(['estado' => $request->estado, 'fecha_egreso' => $fechaActual]);
        return response()->json(['message' => 'Estado Cambiado Correctamente'], 200);
    }

    public function validarInternacion(Request $request)
    {
        $validar = InternacionesEntity::where('dni_afiliado', $request->dni_afiliado)->where('estado', 1)->first();
        if ($validar) {
            return response()->json(["message" => 'el afiliado tiene una internacion abierta, tiene que cerrarla'], 500);
        } else {
            return response()->json(["message" => 'el afiliado no tiene pendientes'], 200);
        }
    }

    public function postSaveNotasInternacion(Request $request)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        InternacionesNotasEntity::create([
            'dni_afiliado' => $request->dni_afiliado,
            'cod_usuario' => $user->cod_usuario,
            'fecha_registra' => $fechaActual,
            'descripcion' => $request->descripcion,
        ]);
        return response()->json(['message' => 'Nota Registrado Correctamente'], 200);
    }

    public function getExportInternacion(Request $request)
    {
        return Excel::download(new InternacionExport($request), 'Internacion.xlsx');
    }
}
