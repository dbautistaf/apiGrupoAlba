<?php

namespace App\Http\Controllers\Afiliados\Services;

use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\afiliado\AfiliadoEscolaridadEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AfiliadoEscolaridadController extends Controller
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function getEscolaridad($idPadron)
    {
        $escolaridad =  AfiliadoEscolaridadEntity::where('id_padron', $idPadron)->first();
        return response()->json($escolaridad, 200);
    }

    public function saveEscolaridad(Request $request)
    {
        if ($request->id != '') {
            $query = AfiliadoEscolaridadEntity::where('id', $request->id)->first();
            $query->nivel_estudio = $request->nivel_estudio;
            $query->fecha_presentacion = $request->fecha_presentacion;
            $query->fecha_vencimiento = $request->fecha_vencimiento;
            $query->id_padron = $request->id_padron;
            $query->fecha_modifica = $this->fechaActual;
            $query->cod_usuario_modifica = $this->user->cod_usuario;
            $query->save();
            $msg = 'Datos actualizados correctamente';
        } else {
            $escolaridad =  AfiliadoEscolaridadEntity::where('id_padron', $request->id_padron)->first();
            if ($escolaridad) {
                return response()->json(['message' => 'El afiliado ya tiene un registro de escolaridad'], 500);
            } else {
                AfiliadoEscolaridadEntity::create([
                    'nivel_estudio' => $request->nivel_estudio,
                    'fecha_presentacion' => $request->fecha_presentacion,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'id_padron' => $request->id_padron,
                    'fecha_registra' => $this->fechaActual,
                    'cod_usuario_registra' => $this->user->cod_usuario
                ]);
                $msg = 'Datos de Escolaridad registrado correctamente';
            }
        }
        return response()->json(['message' => $msg], 200);
    }

    public function getListarEscolaridad(Request $request)
    {
        $data = null;
        $sql = AfiliadoEscolaridadEntity::with(['afiliado']);
        if (!is_null($request->searchs)) {
            $sql->whereHas('afiliado', function ($subQuery) use ($request) {
                $subQuery->where('dni', 'LIKE', "%$request->searchs%")
                    ->orWhere('apellidos', 'LIKE', "%$request->searchs%");
            });
        }

        if (!is_null($request->desde) && !is_null($request->hasta)) {
            $sql->whereBetween('fecha_vencimiento', [$request->desde, $request->hasta]);
        }

        if (!is_null($request->nivel_estudio)) {
            $sql->where('nivel_estudio', $request->nivel_estudio);
        }

        $data = $sql->get();

        return response()->json($data);
    }

    public function getBuscarId(Request $request)
    {
        return response()->json(AfiliadoEscolaridadEntity::with(['afiliado'])->find($request->id));
    }

    public function getVerAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "afiliados/discapacidad/";
        $data = AfiliadoEscolaridadEntity::find($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_registra)->year;
        $path .= "{$anioTrabaja}/$data->url_adjunto";

        return $storageFile->findByObtenerArchivo($path);
    }
}
