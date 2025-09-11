<?php

namespace App\Http\Controllers\facturacion;
use App\Models\facturacion\FacturacionAuditarEntity;
use App\Models\facturacion\FacturacionDatosEntity;
use App\Models\facturacion\FacturacionDetalleEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditarFacturaController extends Controller
{

    public function postAuditarFactura(Request $request)
    {
        DB::beginTransaction();
        try {
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
            $user = Auth::user();

            $cabecera = json_decode($request->datos);
            $detalle = json_decode($request->detalle);

            $facturacion = FacturacionDatosEntity::find($cabecera->id_factura);
            $total_debitar = 0;
            foreach ($detalle as $key) {
               // $item = FacturacionDetalleEntity::find($key->id_detalle);
                FacturacionAuditarEntity::create([
                    'id_detalle' => $key->id_detalle,
                    'estado_autoriza' => $key->estado_autoriza,
                    'observacion_rechazo' => $key->observacion_rechazo,
                    'cod_usuario' => $user->cod_usuario,
                    'fecha_audita' => $fechaActual,
                    'monto_debito' => $key->monto_debito
                ]);
                $total_debitar += $key->monto_debito;
            }
            $facturacion->monto_debitar = $total_debitar;
            $facturacion->estado = 1;
            $facturacion->update();

            DB::commit();
            return response()->json(["message" => "Factura auditada correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
