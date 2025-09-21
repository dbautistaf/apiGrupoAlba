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

    public function saveEscolaridad(ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $nombre_archivo = null;
        $model = json_decode($request->data);
        $nombre_archivo = $storageFile->findBycargarArchivo("ESCOLARIDAD_" . $model->id_padron, 'afiliados/escolaridad', $request);
        if ($model->id != '') {
            $query = AfiliadoEscolaridadEntity::where('id', $model->id)->first();
            if ($nombre_archivo != null) {
                $query->url_adjunto = $nombre_archivo;
            }
            $query->nivel_estudio = $model->nivel_estudio;
            $query->fecha_presentacion = $model->fecha_presentacion;
            $query->fecha_vencimiento = $model->fecha_vencimiento;
            $query->id_padron = $model->id_padron;
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
                    'nivel_estudio' => $model->nivel_estudio,
                    'fecha_presentacion' => $model->fecha_presentacion,
                    'fecha_vencimiento' => $model->fecha_vencimiento,
                    'id_padron' => $model->id_padron,
                    'url_adjunto' => $nombre_archivo,
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
        $path = "afiliados/escolaridad/";
        $data = AfiliadoEscolaridadEntity::find($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_registra)->year;
        $path .= "{$anioTrabaja}/$data->url_adjunto";

        return $storageFile->findByObtenerArchivo($path);
    }
}
