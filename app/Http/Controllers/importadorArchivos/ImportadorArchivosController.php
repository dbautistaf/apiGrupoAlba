<?php

namespace App\Http\Controllers\importadorArchivos;

use App\Imports\ImportarCertificadoImport;
use App\Imports\PracticasMatrizImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportadorArchivosController extends Controller
{
    public function getImportarPracticas(Request $request)
    {
      //  Excel::import(new PracticasMatrizImport, 'public/archivos-importar/');

        $nombre_archivo = null;
        $horaCarga = Carbon::now('America/Lima')->format('His');

        if ($request->hasFile('file')) {
            $fileStorage = $request->file('file');
            $nombre_archivo = 'IMPORT_' . $horaCarga . "." . $fileStorage->extension();
            $destinationPath = "public/archivos-importar";
            Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);
            Excel::import(new PracticasMatrizImport($request->id), 'public/archivos-importar/' . $nombre_archivo);

            return response()->json([
                'message' => 'Practicas importadas correctamente'
            ], 200);
        }
        return response()->json(["message" => "El Archivos de practicas fue importado correctamente"]);
    }

    public function getImportarCertificados()
    {
        Excel::import(new ImportarCertificadoImport, 'public/archivos-importar/certificados_afliado.xlsx');

        return response()->json(["message" => "El Archivos de practicas fue importado correctamente"]);
    }
}
