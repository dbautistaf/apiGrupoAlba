<?php

namespace App\Http\Controllers\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class ManejadorDeArchivosUtils
{
    public function findBycargarArchivo($nameFile, $path, $request)
    {
        $nombre_archivo = null;

        $anioTrabajo = date('Y');
        $directoryPath = storage_path("app/{$path}/{$anioTrabajo}");

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        if ($request->hasFile('archivo')) {
            $horaCarga = Carbon::now('America/Argentina/Buenos_Aires')->format('His');
            $file = $request->file('archivo');
            $nombre_archivo = $nameFile . '_' . $horaCarga . "." . $file->extension();
            $pathFile = "public/{$path}/{$anioTrabajo}";
            Storage::putFileAs($pathFile, $file, $nombre_archivo);
        }
        return $nombre_archivo;
    }

    public function findByCargaMasivaArchivos($nombreFantasia, $path, $request)
    {
        $archivosSubidos = [];

        $anioTrabajo = date('Y');
        $directoryPath = storage_path("app/{$path}/{$anioTrabajo}");

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        if ($request->hasFile('archivos')) {
            $horaCarga = Carbon::now('America/Argentina/Buenos_Aires')->format('His');
            $x = 1;
            foreach ($request->file('archivos') as $archivo) {
                $nombreArchivo = $nombreFantasia . $x . $horaCarga . "." . $archivo->getClientOriginalExtension();
                $pathFile = "public/{$path}/{$anioTrabajo}";
                Storage::putFileAs($pathFile, $archivo, $nombreArchivo);
                $archivosSubidos[] = ["nombre" => $nombreArchivo, "ruta" => $pathFile];
                $x++;
            }
        }
        return $archivosSubidos;
    }

    public function findByDeleteFileName($pathFileName)
    {
        if (Storage::disk('public')->exists($pathFileName)) {
            Storage::disk('public')->delete($pathFileName);
        }
    }

    public function findByObtenerArchivo($pathFile)
    {
        if (!Storage::exists("public/{$pathFile}")) {
            return response()->json(['error' => 'Archivo no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $fileContent = Storage::get("public/{$pathFile}");
        $fileMimeType = Storage::mimeType("public/{$pathFile}");

        return response($fileContent, 200)
            ->header('Content-Type', $fileMimeType);
    }

    public function findBySubirDocumento($nameFile, $path, $requestFile)
    {
        $nombre_archivo = null;

        $anioTrabajo = date('Y');
        $directoryPath = storage_path("app/{$path}/{$anioTrabajo}");

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        if ($requestFile) {
            $horaCarga = Carbon::now()->format('His');
            // $file = $requestFile->file('documento');
            $nombre_archivo = $nameFile . $horaCarga . "." . $requestFile->extension();
            $pathFile = "public/{$path}/{$anioTrabajo}";
            Storage::putFileAs($pathFile, $requestFile, $nombre_archivo);
        }
        return $nombre_archivo;
    }

    public function findBySubirDocumentoPersonal($nameFile, $path, $requestFile)
    {
        $nombre_archivo = null;

        $anioTrabajo = date('Y');
        $directoryPath = storage_path("app/{$path}/{$anioTrabajo}");

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        if ($requestFile) {
            $nombre_archivo = $nameFile . "." . $requestFile->extension();
            $pathFile = "public/{$path}/{$anioTrabajo}";
            Storage::putFileAs($pathFile, $requestFile, $nombre_archivo);
        }
        return $nombre_archivo;
    }
}
