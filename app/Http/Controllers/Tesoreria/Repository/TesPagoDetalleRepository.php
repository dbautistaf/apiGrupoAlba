<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesPagoDetalleEntity;
use App\Models\Tesoreria\TesPagoEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use App\Models\Tesoreria\TesEstadoOrdenPagoEntity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Tesoreria\Repository\FacturasOpaRepository;
use App\Models\Tesoreria\TesFacturasOpaEntity;

class TesPagoDetalleRepository
{
    protected $tesPagosRepo;

    public function __construct(TesPagosRepository $tesPagosRepository)
    {
        $this->tesPagosRepo = $tesPagosRepository;
    }

    public function findById($id)
    {
        return TesPagoDetalleEntity::findOrFail($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $detalle = TesPagoDetalleEntity::create($data);

            // Recalcular totales
            $this->tesPagosRepo->recalcPagoTotal($detalle->id_pago);
            $pago = TesPagoEntity::find($detalle->id_pago);
            if ($pago) {
                $this->tesPagosRepo->recalcOpaTotalsAndState($pago->id_orden_pago);
                // Recalcular estado de facturas vinculadas a la OPA
                $facturas = TesFacturasOpaEntity::where('id_orden_pago', $pago->id_orden_pago)->pluck('id_factura');
                if ($facturas && $facturas->count() > 0) {
                    $factRepo = new FacturasOpaRepository();
                    foreach ($facturas as $idFactura) {
                        $factRepo->recalcularEstadoPagoFacturaFromDetalles($idFactura);
                    }
                }
            }

            DB::commit();
            return $detalle;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error crear detalle pago: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $detalle = TesPagoDetalleEntity::findOrFail($id);
            $detalle->update($data);

            $this->tesPagosRepo->recalcPagoTotal($detalle->id_pago);
            $pago = TesPagoEntity::find($detalle->id_pago);
            if ($pago) {
                $this->tesPagosRepo->recalcOpaTotalsAndState($pago->id_orden_pago);
                $facturas = TesFacturasOpaEntity::where('id_orden_pago', $pago->id_orden_pago)->pluck('id_factura');
                if ($facturas && $facturas->count() > 0) {
                    $factRepo = new FacturasOpaRepository();
                    foreach ($facturas as $idFactura) {
                        $factRepo->recalcularEstadoPagoFacturaFromDetalles($idFactura);
                    }
                }
            }

            DB::commit();
            return $detalle;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizar detalle pago: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $detalle = TesPagoDetalleEntity::findOrFail($id);
            $idPago = $detalle->id_pago;
            $detalle->delete();

            $this->tesPagosRepo->recalcPagoTotal($idPago);
            $pago = TesPagoEntity::find($idPago);
            if ($pago) {
                $this->tesPagosRepo->recalcOpaTotalsAndState($pago->id_orden_pago);
                $facturas = TesFacturasOpaEntity::where('id_orden_pago', $pago->id_orden_pago)->pluck('id_factura');
                if ($facturas && $facturas->count() > 0) {
                    $factRepo = new FacturasOpaRepository();
                    foreach ($facturas as $idFactura) {
                        $factRepo->recalcularEstadoPagoFacturaFromDetalles($idFactura);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminar detalle pago: ' . $e->getMessage());
            throw $e;
        }
    }
}
