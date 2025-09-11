<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\MesaEntradaModelo;
use App\Models\TipoAreaModelo;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class MesaEntradaController extends Controller
{

    protected $storageRepo;

    public function __construct(ManejadorDeArchivosUtils $storageRepo)
    {
        $this->storageRepo = $storageRepo;
    }



    public function index()
    {
        return response()->json(MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])->get());
    }

    public function filtersMesaentrada(Request $request)
    {
        $data = [];

        $sql = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
            ->whereBetween('fecha_carga', [$request->desde, $request->hasta]);

        if (!is_null($request->sindicato)) {
            $sql->where('cod_sindicato', $request->sindicato);
        }

        if (!is_null($request->area)) {
            $sql->where('cod_tipo_area', $request->area);
        }

        if (!is_null($request->cod_usuario)) {
            $sql->where('cod_usuario', $request->cod_usuario);
        }
        $data = $sql->get();
        return response()->json($data);
    }


    public function store(Request $request)
    {
        try {
            $data = json_decode($request->data);
            $archivo = null;
            $archivo = $this->storageRepo->findBycargarArchivo('MESENTRA' . $data->nro_factura . $data->cod_sindicato, 'mesaentrada', $request);

            $mesEntrada = MesaEntradaModelo::create([
                'cod_tipo_documentacion' => $data->cod_tipo_documentacion,
                'emisor' => $data->emisor,
                'nro_factura' => $data->nro_factura,
                'importe' => $data->importe,
                'fecha_documentacion' => $data->fecha_documentacion,
                'fecha_carga' => $data->fecha_carga,
                'observaciones' => $data->observaciones,
                'cod_tipo_area' => $data->cod_tipo_area,
                'cod_sindicato' => $data->cod_sindicato,
                'cod_usuario' => auth()->user()->cod_usuario,
                'archivo' => $archivo
            ]);

            return response()->json(["message" => "El registro fue procesado correctamente.", "datos" => $mesEntrada]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => "error",
                "message" => $th->getMessage()
            ], 409);
        }
    }


    public function show($id)
    {
        return response()->json(MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])->find($id));
    }


    public function update(Request $request, $id)
    {
        try {
            $data = json_decode($request->data);
            $archivo = null;
            $archivo = $this->storageRepo->findBycargarArchivo('MESENTRA' . $data->nro_factura . $data->cod_tipo_area . $data->cod_sindicato, 'mesaentrada', $request);

            $mesa = MesaEntradaModelo::find($data->cod_mesa);
            $mesa->cod_tipo_documentacion = $data->cod_tipo_documentacion;
            $mesa->emisor = $data->emisor;
            $mesa->nro_factura = $data->nro_factura;
            $mesa->importe = $data->importe;
            $mesa->fecha_documentacion = $data->fecha_documentacion;
            $mesa->fecha_carga = $data->fecha_carga;
            $mesa->observaciones = $data->observaciones;
            $mesa->cod_tipo_area = $data->cod_tipo_area;
            $mesa->cod_sindicato = $data->cod_sindicato;
            $mesa->archivo = $archivo;
            $mesa->update();

            return response()->json(["message" => "El registro  fue actualizado correctamente."]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => "error",
                "message" => $th->getMessage()
            ], 409);
        }
    }


    public function destroy($id)
    {
        try {
            $mesa = MesaEntradaModelo::find($id);
            $mesa->delete();
            return response()->json(["message" => "El registro  fue eliminado correctamente."]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function srvRptMesaEntrada(Request $request)
    {

        Pdf::setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $data = [];

        if (!is_null($request->sindicato) && is_null($request->area)) {
            $data = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
                ->where('cod_sindicato', $request->sindicato)
                ->whereBetween('fecha_carga', [$request->desde, $request->hasta])
                ->get();
        } else if (is_null($request->sindicato) && !is_null($request->area)) {
            $data = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
                ->where('cod_tipo_area', $request->area)
                ->get();
        } else if (!is_null($request->sindicato) && !is_null($request->area)) {
            $data = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
                ->where('cod_sindicato', $request->sindicato)
                ->where('cod_tipo_area', $request->area)
                ->get();
        } else {
            $data = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
                ->whereBetween('fecha_carga', [$request->desde, $request->hasta])
                ->get();
        }

        $description = "";
        if (!is_null($request->area)) {
            $dt = TipoAreaModelo::find($request->area);
            $description = $dt->tipo_area;
        }

        $pdf = Pdf::loadView('rpt_mesa_entrada', ["data" => $data, "desde" => $request->desde, "hasta" => $request->hasta, "area" => $description]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('mesa_entrada.pdf');
        //return response()->json($data, 200);
    }

    public function getListarUsuariosMesaEntrada()
    {
        return response()->json(User::where('cod_perfil', '1')->get());
    }

    public function getVerAdjunto(Request $request)
    {
        $path = "mesaentrada/";
        $data = MesaEntradaModelo::find($request->id);
        $anioTrabajo = Carbon::parse($data->fecha_carga)->year;
        $path .= "{$anioTrabajo}/$data->archivo";

        return $this->storageRepo->findByObtenerArchivo($path);
    }
}
