<?php

namespace App\Http\Controllers\storage;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Routing\Controller;

class StorageController extends Controller
{

    public function getDescargarFormatoImportadorPracticas()
    {
        $filePath = 'public/recursos/formato_importador_practicas.xlsx';

        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }

        return response()->json(['message' => 'Archivo no encontrado'], 404);
    }

    public function getDescargarFormatoImportadorLiquidaciones()
    {
        $filePath = 'public/recursos/formato_importar_liquidaciones.xlsx';

        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }

        return response()->json(['message' => 'Archivo no encontrado'], 404);
    }
}
