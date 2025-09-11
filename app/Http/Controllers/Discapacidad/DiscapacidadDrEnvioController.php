<?php

namespace App\Http\Controllers\Discapacidad;

use App\Http\Controllers\Discapacidad\Repository\DiscapacidadDrEnvioRepository;
use App\Imports\DiscapacidadTesoreriaImport;
use App\Models\Discapacidad\DiscapacidadDrEnvioEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DiscapacidadDrEnvioController extends Controller
{

    public function getImportarDrEnvio(DiscapacidadDrEnvioRepository $repo, Request $request)
    {
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $contenido = file_get_contents($archivo->getRealPath());
            $lineas = explode("\n", $contenido);
            $data = [];
            foreach ($lineas as $linea) {
                $campos = explode('|', $linea);
                if (count($campos) > 0 && !empty($campos[0])) {
                    if (!$repo->findByExisteIdRendicion($campos[0])) {
                        $data[] = $campos;
                        $repo->findBySaveDrEnvior($campos);
                    }
                }
            }
        }

        return response()->json(["message" => "Se importaron " . count($data) . " registros."]);
    }

    public function getImportarTesoreria(Request $request)
    {
        $nombre_archivo = null;
        $horaCarga = Carbon::now('America/Lima')->format('His');

        if ($request->hasFile('file')) {
            $fileStorage = $request->file('file');
            $nombre_archivo = 'IMPORT_TES_' . $horaCarga . "." . $fileStorage->extension();
            $destinationPath = "public/archivos-importar";
            Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);
            $import = new DiscapacidadTesoreriaImport();
            Excel::import($import, 'public/archivos-importar/' . $nombre_archivo);

            return response()->json([
                'message' => 'Archivo importado correctamente',
                "data" => $import->discaNoEnontradas
            ], 200);
        }
        return response()->json(["message" => "No se encontro un archivo"], 404);
    }
}
