<?php

namespace App\Http\Controllers;

use App\Models\afiliado\AfiliadoCertificadoEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DiscapacidadController extends Controller
{
    //
    public function getDiscapacidadIdPadron($idPadron)
    {
        $discapaciodad = AfiliadoCertificadoEntity::where('id_padron', $idPadron)->first();
        return response()->json($discapaciodad, 200);
    }

    public function saveDiscapacidad(Request $request)
    {
        $nombre_archivo = null;
        $anioActual = Carbon::now('America/Lima')->year;
        $horaCarga = Carbon::now('America/Lima')->format('H-i-s');
        $model = json_decode($request->data);
        if ($request->hasFile('archivo')) {
            $fileStorage = $request->file('archivo');
            $nombre_archivo = 'DISCAPACIDAD_' . $model->id_padron . "_" . $horaCarga . "_AF_" . $anioActual . "." . $fileStorage->extension();
            $destinationPath = "public/afiliados";
            Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);
        }
        $msg = null;
        if ($model->id!='') {
            $query = AfiliadoCertificadoEntity::where('id', $model->id)->first();
            $query->id_tipo_discapacidad = $model->id_tipo_discapacidad;
            $query->diagnostico = $model->diagnostico;
            $query->fecha_certificado = $model->fecha_certificado;
            $query->fecha_vto = $model->fecha_vto;
            $query->id_padron = $model->id_padron;
            $query->certificado = $model->certificado;
            $query->url_adjunto = $nombre_archivo;
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
}
