<?php

namespace App\Http\Controllers\NubeComprobantes;

use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\NubeComprobantes\NubeComprobantesEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NubeComprobantesController extends Controller
{
    protected $repoArchivos;

    public function __construct(ManejadorDeArchivosUtils $repoArchivos)
    {
        $this->repoArchivos = $repoArchivos;
    }


    public function consultar(Request $request)
    {
        $sql = NubeComprobantesEntity::with([]);

        if (!is_null($request->cuit)) {
            $sql->where('cuit', $request->cuit);
        }

        if (!is_null($request->nro_factura)) {
            $sql->where('nro_factura', $request->nro_factura);
        }

        if (!is_null($request->periodo)) {
            $sql->where('periodo', $request->periodo);
        }

        $data = $sql->orderByDesc('id_comprobante')
            ->limit(2000)
            ->get();

        return response()->json($data);
    }

    public function procesar(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();

            // $model = json_encode($request->data);
            $data = json_decode($request->data);

            Log::info($data->cuit);
            if (is_numeric($data->id_comprobante)) {
                $nombreFile = $data->cuit . '-' . $data->periodo . '-Discapacidad';
                $adjunto = $this->repoArchivos->findBySubirDocumentoPersonal($nombreFile, 'nubecomprobantes', $request->file('documento'));
                $nube = NubeComprobantesEntity::find($data->id_comprobante);
                $nube->cuit = $data->cuit;
                $nube->nro_factura = $data->nro_factura;
                $nube->periodo = $data->periodo;
                $nube->nombre_archivo = $adjunto;
                $nube->update();
                DB::commit();
                return response()->json(['message' => 'Documento modificado con éxito']);
            } else {
                $nombreFile = $data->cuit . '-' . $data->periodo . '-Discapacidad';
                $adjunto = $this->repoArchivos->findBySubirDocumentoPersonal($nombreFile, 'nubecomprobantes', $request->file('documento'));
                NubeComprobantesEntity::create([
                    'cuit' => $data->cuit,
                    'nro_factura' => $data->nro_factura,
                    'periodo' => $data->periodo,
                    'nombre_archivo' => $adjunto,
                    'fecha_subida' => Carbon::now(),
                    'cod_usuario_registra' => $user->cod_usuario
                ]);
                DB::commit();
                return response()->json(['message' => 'Documento cargado con éxito']);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar archivo', 'error' => $th->getMessage()], 500);
        }
    }

    public function getVerAdjunto(Request $request)
    {
        $path = "nubecomprobantes/";
        $data = NubeComprobantesEntity::find($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_subida)->year;
        $path .= "{$anioTrabaja}/$data->nombre_archivo";

        return $this->repoArchivos->findByObtenerArchivo($path);
    }
}
