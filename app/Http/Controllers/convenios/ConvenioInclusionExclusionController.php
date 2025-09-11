<?php

namespace App\Http\Controllers\convenios;


use App\Models\convenios\ConvenioDetalleModuloEntity;
use App\Models\convenios\ConvenioInclusionExclusionEntity;
use App\Models\convenios\ConvenioModuloEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class ConvenioInclusionExclusionController extends Controller
{

    public function getListarModulosTipo(Request $request)
    {
        $data = [];

        $data = ConvenioModuloEntity::where("tipo_modulo", $request->tipo)
            ->where("id_convenio", $request->id_convenio)
            ->get();

        return response()->json($data, 200);
    }

    public function postCrearModulo(Request $request)
    {
        ConvenioModuloEntity::create($request->all());
        return response()->json(["message" => "Modulo creado correctamente."], 200);
    }

    public function postCrearDetalleModulo(Request $request)
    {
        if(!is_null($request->id_modulo_detalle)){
            $data = ConvenioDetalleModuloEntity::find($request->id_modulo_detalle);
            $data->update($request->all());
        }else{
            ConvenioDetalleModuloEntity::create($request->all());
        }


        return response()->json(["message" => "Detalle agregado correctamente."], 200);
    }

    public function getListarDetalleModulo(Request $request)
    {
        $data = [];

        $data = ConvenioDetalleModuloEntity::where("id_modulo", $request->id_modulo)
            ->get();

        return response()->json($data, 200);
    }

    public function postAgregarInclusionExclusion(Request $request)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $model = json_decode($request->model);
        $anioActual = Carbon::now('America/Lima')->year;
        $horaCarga = Carbon::now('America/Lima')->format('H-i-s');

        $nombre_archivo = "";

        if ($request->hasFile('archivo')) {
            $fileStorage = $request->file('archivo');
            $nombre_archivo = 'CONV_CABE_' . $model->id_convenio . "_" . $horaCarga . "_AF_" . $anioActual . "." . $fileStorage->extension();
            $destinationPath = "public/convenios/cabecera";
            Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);
        }

        if (!empty($model->id_inclu_exclu)) {
            $data = ConvenioInclusionExclusionEntity::find($model->id_inclu_exclu);
            $data->observaciones = $model->observaciones;
            if (!is_null($nombre_archivo)) {
                $data->archivo = $nombre_archivo;
            }

            $data->id_convenio = $model->id_convenio;
            $data->update();
        } else {
            ConvenioInclusionExclusionEntity::create([
                'observaciones' => $model->observaciones,
                'archivo' => $nombre_archivo,
                'id_convenio' => $model->id_convenio
            ]);
        }

        return response()->json(["message" => "Registro procesado correctamente."], 200);
    }

    public function getBuscarCabecera(Request $request)
    {
        $data = ConvenioInclusionExclusionEntity::where('id_convenio', $request->id_convenio)
            ->first();
        return response()->json($data, 200);
    }

    public function getObtenerArchivoCabecera(Request $request)
    {
        if (!Storage::exists('public/convenios/cabecera/' . $request->archivo)) {
            return response()->json(['error' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        $fileContent = Storage::get('public/convenios/cabecera/' . $request->archivo);
        $fileMimeType = Storage::mimeType('public/convenios/cabecera/' . $request->archivo);

        return response($fileContent, 200)
            ->header('Content-Type', $fileMimeType);
    }

    public function getEliminarDetalle(Request $request) {
        $data = ConvenioDetalleModuloEntity::find($request->id_modulo_detalle);
        $data->delete();
        return response()->json(["message" => "Registro eliminado correctamente."], 200);
    }
}
