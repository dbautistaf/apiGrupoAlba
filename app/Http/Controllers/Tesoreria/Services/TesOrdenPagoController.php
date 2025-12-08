<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Exports\OrdenesPagoExport;
use App\Http\Controllers\Tesoreria\Repository\TesPagosRepository;
use App\Http\Controllers\Tesoreria\Repository\TestOrdenPagoRepository;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class TesOrdenPagoController extends Controller
{

    public function getListTipoEstado(TestOrdenPagoRepository $opa)
    {
        return response()->json($opa->findByListTipoEstado());
    }

    public function getFilterOrdenPago(Request $request, TestOrdenPagoRepository $opa)
    {
        $data = [];
        $data = $opa->getFiltroDinamico($request);
        return response()->json($data);
    }

    public function getProcesar(Request $request, TestOrdenPagoRepository $opa, TesPagosRepository $pagosRepo)
    {
        try {
            DB::beginTransaction();
            $menssage = "OPA generado con éxito.";
            if (is_null($request->id_orden_pago)) {
                $opa->findByCreate($request);
            } else {
                /* if (!$opa->findByExistsOpaEstado($request->id_orden_pago, '1')) {
                    DB::rollBack();
                    return response()->json(['message' => 'La OPA ya se encuentra en un proceso de PAGO y no puede ser modificado.'], 409);
                } */
                $opa->findByUpdate($request);
                $pagosRepo->findByUpdatePagoPorOpa($request, $request->id_orden_pago);
                $menssage = "OPA actualizo con éxito.";
            }

            DB::commit();
            return response()->json(['message' => $menssage]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getModificarEstado(Request $request, TestOrdenPagoRepository $opa)
    {
        $tes = $opa->findByUpdateEstado($request->id_orden_pago, $request->id_estado_orden_pago, $request->motivo);
        return response()->json(['message' => "OPA {$tes->estado->descripcion_estado} con éxito"]);
    }

    public function printOrderPay($id)
    {
        $query = TesOrdenPagoEntity::with([
            'estado',
            'factura.razonSocial',
            'factura.tipoComprobante',
            'proveedor.datosBancarios',
            'proveedor.tipoIva',
            'prestador.datosBancarios',
            'prestador.tipoIva',
            'pagos.formaPago',
            'pagos.cuenta.entidadBancaria',
            'pagos.pagosParciales'
        ])->whereRelation('factura', 'id_factura', $id)
            ->first();

        Carbon::setLocale('es');
        $fecha = Carbon::parse($query?->factura?->periodo);

        $datos = [
            "comprobante_nro" => $query?->num_orden_pago,
            "fecha_emision" => $query?->fecha_emision,
            "cuit_proveedor" => $query?->proveedor ? $query->proveedor->cuit : $query?->prestador->cuit,
            "nombre_proveedor" => $query?->proveedor ? $query->proveedor->razon_social : $query?->prestador->razon_social,
            "cbu_proveedor" => $query?->proveedor ? $query->proveedor->datosBancarios?->cbu_cuenta : $query?->prestador->datosBancarios?->cbu_cuenta,
            "iva_proveedor" => $query?->proveedor ? $query->proveedor->tipoIva->descripcion_iva : $query?->prestador->tipoIva->descripcion_iva,
            "domicilio_proveedor" => $query?->proveedor ? $query->proveedor->direccion : $query?->prestador->direccion,
            "tipo_comprobante" => $query?->factura?->tipoComprobante?->descripcion,
            "numero_comprobante" => $query?->factura?->numero,
            "facturas" => $query?->factura,
            "total" => $query?->monto_orden_pago,
            "pagos" => $query?->pagos,
            "fecha_pago" => $query?->fecha_confirma_pago,
            "debito" => $query?->factura?->total_debitado_liquidacion,
            "totalPagos" => !empty($query?->pagos) && count($query?->pagos) > 0
                ? number_format((float) $query?->monto_orden_pago, 2, '.', '')
                : '0.00',
            "razon_social" => $query?->factura->razonSocial,
            "observaciones" => 'PRESTACIÓN ' . strtoupper($fecha->translatedFormat('F')) . ' ' . $fecha->year,
            "pagosParciales" =>$query?->pagos?->pagosParciales,
        ];

        $pdf = PDF::loadView('orden_pago', $datos);
        $pdf->setPaper('A4');
        return $pdf->download('recibo-pago-' . $query->id_orden_pago . '.pdf');
    }

    public function exportOrdenesPago(Request $request)
    {
        return Excel::download(new OrdenesPagoExport($request), 'OrdenesPago.xlsx');
    }
}
