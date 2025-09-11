<?php

namespace App\Http\Controllers\convenios\Repository;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ConvNegociacionesRepository
{
    public function findByArchivo($params)
    {
        $nombre_archivo = null;

        if ($params->hasFile('file')) {
            $horaCarga = Carbon::now(tz: 'America/Argentina/Buenos_Aires')->format('His');
            $file = $params->file('file');
            $nombre_archivo = 'NEGOCIACION_' . $horaCarga  . "." . $file->extension();
            $path = "public/convenios/negociaciones";
            Storage::putFileAs($path, $file, $nombre_archivo);
        }
        return $nombre_archivo;
    }
}
