<?php

namespace App\Http\Controllers\liquidaciones\repository;

use App\Models\liquidaciones\LiqDebitoInternoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LiqDebitoInternoRepository
{

    public function findBySave($params, $name_archivo)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        return LiqDebitoInternoEntity::create([
            'id_factura' => $params->id_factura,
            'nombre_archivo' => $name_archivo,
            'fecha_carga' => $fechaActual,
            'cod_usuario_crea' => $user->cod_usuario,
            'observaciones' => $params->observaciones,
            'tipo' => $params->tipo
        ]);
    }

    public function findByUploadFile($params, $tipo)
    {
        $nombre_archivo = null;

        if ($params->hasFile('archivo')) {
            $horaCarga = Carbon::now('America/Lima')->format('H-i-s');
            $file = $params->file('archivo');
            $nombre_archivo = 'ARCHIVO_ADJUNTO_' . $tipo . '_' . $horaCarga  . "." . $file->extension();
            $path = "public/liquidaciones/debito_interno";
            Storage::putFileAs($path, $file, $nombre_archivo);
        }
        return $nombre_archivo;
    }

    public function findByListIdFactura($id_factura, $tipo)
    {
        return LiqDebitoInternoEntity::where('id_factura', $id_factura)
            ->where('tipo', $tipo)
            ->get();
    }

    public function findByDeleteId($id)
    {
        $file = LiqDebitoInternoEntity::find($id);
        $this->findByDeleteFile($file->nombre_archivo);
        return $file->delete();
    }

    public function findByDeleteFile($name_file)
    {
        $path = 'public/liquidaciones/debito_interno/' . $name_file;
        return Storage::exists($path) ? Storage::delete($path) : null;
    }
}
