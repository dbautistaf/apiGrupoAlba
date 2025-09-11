<?php

namespace App\Http\Controllers\liquidaciones;

use App\Http\Controllers\facturacion\repository\FacturasPrestadoresRepository;
use App\Http\Controllers\liquidaciones\repository\LiqDictamenMedicoRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;


class LiqDictamenMedicoController extends Controller
{

    public function getProcesarDictamen(LiqDictamenMedicoRepository $repo, FacturasPrestadoresRepository $repoFactura, Request $request)
    {
        $nombre_archivo = $repo->findByArchivo($request);
        $data = json_decode($request->data);

        if ($data->cambiar_estado) {
            $repoFactura->findByUpdateEstado($data->id_factura, '5');
        }

        if (!empty($data->id_dictamen_medico)) {
            $repo->findByUpdate($data, $nombre_archivo);
            return response()->json(["message" => "Auditoria actualizada correctamente"]);
        } else {
            $repo->findBySave($data, $nombre_archivo);
            return response()->json(["message" => "Auditoria registrada correctamente"]);
        }
    }

    public function getBuscarId(LiqDictamenMedicoRepository $repo, Request $request)
    {
        return response()->json($repo->findByIdFactura($request->id));
    }

    public function getViewFile(Request $request)
    {
        $path = 'public/liquidaciones/dictamen_medicos/' . $request->file;

        if (!Storage::exists($path)) {
            return response()->json(['error' => 'Archivo no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $fileContent = Storage::get($path);
        $fileMimeType = Storage::mimeType($path);

        return response($fileContent, 200)
            ->header('Content-Type', $fileMimeType);
    }
}
