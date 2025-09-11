<?php

namespace App\Http\Controllers\Afiliados\Services;

use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\afiliado\AfiliadoCertificadoEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AfiliadoDiscapacidadController extends Controller
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }
    public function getDiscapacidadIdPadron($idPadron)
    {
        $discapaciodad = AfiliadoCertificadoEntity::where('id_padron', $idPadron)->first();
        return response()->json($discapaciodad, 200);
    }

    public function saveDiscapacidad(ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $nombre_archivo = null;
        $model = json_decode($request->data);
        $nombre_archivo = $storageFile->findBycargarArchivo("DISCAPACIDAD_" . $model->id_padron, 'afiliados/discapacidad', $request);
        $msg = null;
        if (!is_null($model->id)) {
            $query = AfiliadoCertificadoEntity::where('id', $model->id)->first();
            $query->id_tipo_discapacidad = $model->id_tipo_discapacidad;
            $query->diagnostico = $model->diagnostico;
            $query->fecha_certificado = $model->fecha_certificado;
            $query->fecha_vto = $model->fecha_vto;
            $query->id_padron = $model->id_padron;
            $query->certificado = $model->certificado;
            $query->url_adjunto = $nombre_archivo;
            $query->fecha_modifica = $this->fechaActual;
            $query->cod_usuario_modifica = $this->user->cod_usuario;
            $query->save();
            $msg = 'Datos actualizados correctamente';
        } else {
            if (AfiliadoCertificadoEntity::where('id_padron', $model->id_padron)->exists()) {
                return response()->json(['message' => 'El afiliado ya cuentacon un registro de discapacidad'], 500);
            } else {
                AfiliadoCertificadoEntity::create([
                    'id_tipo_discapacidad' => $model->id_tipo_discapacidad,
                    'diagnostico' => $model->diagnostico,
                    'fecha_certificado' => $model->fecha_certificado,
                    'fecha_vto' => $model->fecha_vto,
                    'id_padron' => $model->id_padron,
                    'certificado' => $model->certificado,
                    'url_adjunto' => $nombre_archivo,
                    'fecha_registra' => $this->fechaActual,
                    'cod_usuario_registra' => $this->user->cod_usuario
                ]);
                $msg = 'Datos de discapacidad registrado correctamente';
            }
        }
        return response()->json(['message' => $msg], 200);
    }

    public function srvFilterData(Request $request)
    {
        $data = [];

        if (!is_null($request->dni)) {
            $data = AfiliadoCertificadoEntity::with(['afiliado', 'tipo'])
                ->whereHas('afiliado', function ($query) use ($request) {
                    $query->where('cuil_benef', $request->dni);
                })
                ->get();
        } else {
            $data = AfiliadoCertificadoEntity::with(['afiliado', 'tipo'])
                ->orderByDesc('id')
                ->limit(500)
                ->get();
        }

        return response()->json($data, 200);
    }

    public function getBuscarDiscapacidadCertificado(Request $request)
    {
        $discapaciodad = AfiliadoCertificadoEntity::with(['afiliado', 'tipo'])->find($request->id);
        return response()->json($discapaciodad, 200);
    }

    public function getListarDiscapacidad(Request $request)
    {
        $data = null;
        $sql = AfiliadoCertificadoEntity::with(['afiliado', 'tipo']);
        if (!is_null($request->searchs)) {
            $sql->whereHas('afiliado', function ($subQuery) use ($request) {
                $subQuery->where('dni', 'LIKE', "%$request->searchs%")
                    ->orWhere('apellidos', 'LIKE', "%$request->searchs%");
            });
        }

        if (!is_null($request->desde) && !is_null($request->hasta)) {
            $sql->whereBetween('fecha_certificado', [$request->desde, $request->hasta]);
        }

        if (!is_null($request->id_tipo_discapacidad)) {
            $sql->where('id_tipo_discapacidad', $request->id_tipo_discapacidad);
        }

        $data = $sql->get();

        return response()->json($data);
    }

    public function getVerAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "afiliados/discapacidad/";
        $data = AfiliadoCertificadoEntity::find($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_registra)->year;
        $path .= "{$anioTrabaja}/$data->url_adjunto";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function getBuscarId(Request $request)
    {
        return response()->json(AfiliadoCertificadoEntity::with(['afiliado'])->find($request->id));
    }
}
