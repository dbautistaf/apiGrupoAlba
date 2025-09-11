<?php

namespace App\Http\Controllers\convenios;

use App\Models\configuracion\TipoGalenosEntity;
use App\Models\convenios\ConveniosGalenosEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ConvenioGalenosController extends Controller
{

    public function postAgregarGalenosConvenio(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->id_conf_tipo_galeno == 0) {
                // #OBTENER LOS TIPOS DE GALENOS ACTIVOS
                $TipoGalenos = TipoGalenosEntity::where('vigente', 1)->get();

                // #RECORREMOS TODO LOS GALENOS Y SUS PLANES PARA CREAR UN POR CADA UNO
                foreach ($TipoGalenos as $galeno) {
                    ConveniosGalenosEntity::create([
                        'cod_convenio' => $request->cod_convenio,
                        'id_conf_tipo_galeno' => $galeno['id_conf_tipo_galeno'],
                        'tipo_importe' => $request->tipo_importe,
                        'monto_anterior_valor_base' => $request->monto_anterior_valor_base,
                        'monto_valor_base' => $request->monto_valor_base,
                        'monto_valor_convenio' => $request->monto_valor_convenio,
                        'tipo_valor_base'  => $request->tipo_valor_base
                    ]);
                }
            } else {
                ConveniosGalenosEntity::create([
                    'cod_convenio' => $request->cod_convenio,
                    'id_conf_tipo_galeno' => $request->id_conf_tipo_galeno,
                    'tipo_importe' => $request->tipo_importe,
                    'monto_anterior_valor_base' => $request->monto_anterior_valor_base,
                    'monto_valor_base' => $request->monto_valor_base,
                    'monto_valor_convenio' => $request->monto_valor_convenio,
                    'tipo_valor_base'  => $request->tipo_valor_base
                ]);
            }

            DB::commit();
            return response()->json(["message" => "Los datos se procesaron correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postActualizarGalenosConvenio(Request $request)
    {
        try {
            DB::beginTransaction();

            $galeno = ConveniosGalenosEntity::find($request->id_convenio_galeno);

            $galeno->cod_convenio = $request->cod_convenio;
            $galeno->id_conf_tipo_galeno = $request->id_conf_tipo_galeno;
            $galeno->tipo_importe = $request->tipo_importe;
            $galeno->monto_anterior_valor_base = $request->monto_anterior_valor_base;
            $galeno->monto_valor_base = $request->monto_valor_base;
            $galeno->monto_valor_convenio = $request->monto_valor_convenio;
            $galeno->tipo_valor_base  = $request->tipo_valor_base;
            $galeno->update();


            DB::commit();
            return response()->json(["message" => "Los datos se actualizaron correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function eliminarGalenoConvenio(Request $request)
    {
        try {
            DB::beginTransaction();

            $galeno = ConveniosGalenosEntity::find($request->id_convenio_galeno);
            $galeno->delete();


            DB::commit();
            return response()->json(["message" => "Registro eliminado correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarConvenioGalenos(Request $request)
    {
        $data = [];

        $data = ConveniosGalenosEntity::with(['tipoGaleno'])
            ->where('cod_convenio', $request->convenio)
            ->orderByDesc('id_convenio_galeno')
            ->get();

        return response()->json($data, 200);
    }

    public function eliminarGalenoMasivo(Request $request) {
        $detalleEliminar = json_decode($request->detalle);
        foreach ($detalleEliminar as $galeno) {
            $galeno = ConveniosGalenosEntity::find($galeno->id_convenio_galeno);
            $galeno->delete();
        }

        return response()->json(["message" => "Registros eliminados correctamente"]);
    }
}
