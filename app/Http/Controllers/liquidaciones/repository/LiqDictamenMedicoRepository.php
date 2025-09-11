<?php

namespace App\Http\Controllers\liquidaciones\repository;

use App\Models\liquidaciones\DictamenMedicoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LiqDictamenMedicoRepository
{

    public function findBySave($params, $archivo)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        return DictamenMedicoEntity::create([
            'id_factura' => $params->id_factura,
            'observacion_auditoria' => $params->observacion_auditoria,
            'cod_usuario' => $user->cod_usuario,
            'fecha_registra' => $fechaActual,
            'nombre_archivo' => $archivo
        ]);
    }

    public function findByArchivo($params)
    {
        $nombre_archivo = null;

        if ($params->hasFile('archivo')) {
            $horaCarga = Carbon::now('America/Argentina/Buenos_Aires')->format('His');
            $file = $params->file('archivo');
            $nombre_archivo = 'DICTAMEN_MEDICO_' . $horaCarga  . "." . $file->extension();
            $path = "public/liquidaciones/dictamen_medicos";
            Storage::putFileAs($path, $file, $nombre_archivo);
        }
        return $nombre_archivo;
    }

    public function findByIdFactura($id)
    {
        return DictamenMedicoEntity::with(['usuario'])
            ->where('id_factura', $id)->first();
    }

    public function findByUpdate($params, $archivo)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $med = DictamenMedicoEntity::find($params->id_dictamen_medico);
        $med->id_factura = $params->id_factura;
        $med->observacion_auditoria = $params->observacion_auditoria;
        $med->cod_usuario = $user->cod_usuario;
        $med->fecha_registra = $fechaActual;
        $med->nombre_archivo = $archivo;
        $med->update();
        return $med;
    }
}
