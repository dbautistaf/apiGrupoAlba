<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\PagoRetencionesEntity;
use App\Models\Tesoreria\TesPagoEntity;
use App\Models\Contabilidad\RetencionReglasEntity;
use App\Models\Tesoreria\TesFacturasOpaEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagoRetencionesRepository
{
    protected $tesPagosRepo;
    private $user;
    private $fechaActual;

    public function __construct(TesPagosRepository $tesPagosRepository)
    {
        $this->tesPagosRepo = $tesPagosRepository;
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    /**
     * Obtiene todas las retenciones de un pago con relaciones cargadas
     */
    public function findByPago($idPago)
    {
        return PagoRetencionesEntity::where('id_pago', $idPago)
            ->with(['tipoRetencion', 'regla'])
            ->get();
    }

    /**
     * Obtiene retención por ID
     */
    public function findById($id)
    {
        return PagoRetencionesEntity::with(['tipoRetencion', 'regla', 'pago'])
            ->findOrFail($id);
    }

    /**
     * Obtiene la regla vigente para un tipo de retención
     * Criterio: fecha_desde <= hoy, (fecha_hasta IS NULL OR fecha_hasta >= hoy), vigente = 1
     */
    public function getReglaVigente($idRetencion)
    {
        $hoy = Carbon::now('America/Argentina/Buenos_Aires')->format('Y-m-d');

        return RetencionReglasEntity::where('id_retencion', $idRetencion)
            ->whereDate('fecha_desde', '<=', $hoy)
            ->where(function ($query) use ($hoy) {
                $query->whereNull('fecha_hasta')
                    ->orWhereDate('fecha_hasta', '>=', $hoy);
            })
            ->where('vigente', 1)
            ->orderByDesc('fecha_desde')
            ->first();
    }

    /**
     * Calcula el monto de retención aplicando las reglas de negocio
     * - Si base_imponible < minimo_no_imponible => monto = 0
     * - Sino: monto = base_imponible * porcentaje / 100
     */
    public function calcularMonto($baseImponible, $regla)
    {
        if ($baseImponible < $regla->minimo_no_imponible) {
            return 0;
        }

        return round($baseImponible * $regla->porcentaje / 100, 2);
    }

    /**
     * Valida y calcula datos de retención antes de guardar
     * Retorna array con datos calculados o false si hay error de validación
     */
    public function calcularYValidar(array $data)
    {
        // Validar que base_imponible sea > 0
        if (!isset($data['base_imponible']) || $data['base_imponible'] <= 0) {
            return [
                'error' => true,
                'message' => 'base_imponible debe ser mayor a 0',
                'code' => 422
            ];
        }

        // Validar que existe la regla vigente
        if (!isset($data['id_retencion'])) {
            return [
                'error' => true,
                'message' => 'id_retencion es requerido',
                'code' => 422
            ];
        }

        $regla = $this->getReglaVigente($data['id_retencion']);
        if (!$regla) {
            return [
                'error' => true,
                'message' => 'No existe regla vigente para esta retención',
                'code' => 409
            ];
        }

        // Calcular el monto (backend, no confiar en frontend)
        $montoCalculado = $this->calcularMonto($data['base_imponible'], $regla);

        // Validar consistencia: suma_retenciones <= monto_opa
        if (isset($data['id_pago'])) {
            $pago = TesPagoEntity::find($data['id_pago']);
            if (!$pago) {
                return [
                    'error' => true,
                    'message' => 'Pago no encontrado',
                    'code' => 404
                ];
            }

            // Sumar retenciones existentes + la nueva
            $sumaActual = PagoRetencionesEntity::where('id_pago', $data['id_pago'])
                ->sum('monto') ?? 0;

            if ($sumaActual + $montoCalculado > $pago->monto_opa) {
                return [
                    'error' => true,
                    'message' => 'La suma de retenciones excede el monto OPA',
                    'code' => 409
                ];
            }
        }

        // Retornar datos validados y calculados
        return [
            'error' => false,
            'id_retencion' => $data['id_retencion'],
            'id_retencion_regla' => $regla->id_regla,
            'base_imponible' => round($data['base_imponible'], 2),
            'porcentaje' => $regla->porcentaje,  // snapshot histórico
            'monto' => $montoCalculado,
            'minimo_aplicado' => $regla->minimo_no_imponible,  // snapshot histórico
            'observaciones' => $data['observaciones'] ?? null,
            'regla' => $regla
        ];
    }

    /**
     * Crea una retención con validaciones y recalc en cascada
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $validacion = $this->calcularYValidar($data);
            if ($validacion['error']) {
                DB::rollBack();
                return $validacion;
            }

            $retencion = PagoRetencionesEntity::create([
                'id_pago' => $data['id_pago'],
                'id_retencion' => $validacion['id_retencion'],
                'id_retencion_regla' => $validacion['id_retencion_regla'],
                'base_imponible' => $validacion['base_imponible'],
                'porcentaje' => $validacion['porcentaje'],
                'monto' => $validacion['monto'],
                'minimo_aplicado' => $validacion['minimo_aplicado'],
                'observaciones' => $validacion['observaciones'],
                'fecha_registra' => $this->fechaActual,
                'id_usuario' => $this->user->cod_usuario
            ]);

            // Recalcular totales del pago
            $this->tesPagosRepo->recalcPagoTotal($retencion->id_pago);

            // Recalcular estado del pago
            $pago = TesPagoEntity::find($retencion->id_pago);
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
            return [
                'error' => false,
                'data' => $retencion->load(['tipoRetencion', 'regla'])
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error crear retencion: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Error al crear retención: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }

    /**
     * Actualiza una retención con validaciones y recalc en cascada
     */
    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $retencion = PagoRetencionesEntity::findOrFail($id);

            // Agregar id_pago a los datos para validación
            $data['id_pago'] = $retencion->id_pago;

            $validacion = $this->calcularYValidar($data);
            if ($validacion['error']) {
                DB::rollBack();
                return $validacion;
            }

            $retencion->update([
                'id_retencion' => $validacion['id_retencion'],
                'id_retencion_regla' => $validacion['id_retencion_regla'],
                'base_imponible' => $validacion['base_imponible'],
                'porcentaje' => $validacion['porcentaje'],
                'monto' => $validacion['monto'],
                'minimo_aplicado' => $validacion['minimo_aplicado'],
                'observaciones' => $validacion['observaciones']
            ]);

            // Recalcular totales del pago
            $this->tesPagosRepo->recalcPagoTotal($retencion->id_pago);

            $pago = TesPagoEntity::find($retencion->id_pago);
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
            return [
                'error' => false,
                'data' => $retencion->load(['tipoRetencion', 'regla'])
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizar retencion: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Error al actualizar retención: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }

    /**
     * Lista retenciones con filtros: tipo (PROVEEDOR/PRESTADOR), desde, hasta, razon_social, cuit, id_retencion
     */
    public function findByListRetencionesFiltroPrincipal($params)
    {
        $query = PagoRetencionesEntity::with(['tipoRetencion', 'regla', 'pago.opa.proveedor', 'pago.opa.prestador']);

        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $query->whereHas('pago', function ($q) use ($params) {
                $q->whereBetween(DB::raw('DATE(fecha_probable_pago)'), [$params->desde, $params->hasta]);
            });
        }

        if (!is_null($params->tipo ?? null) && $params->tipo !== '') {
            $query->whereHas('pago', function ($q) use ($params) {
                $q->where('tipo_factura', $params->tipo);
            });
        }

        if (!is_null($params->id_retencion ?? null) && $params->id_retencion !== '') {
            $query->where('id_retencion', $params->id_retencion);
        }

        if (!is_null($params->razon_social ?? null) && $params->razon_social !== '') {
            $query->where(function ($q) use ($params) {
                $q->whereHas('pago.opa.proveedor', function ($sql) use ($params) {
                    $sql->where('razon_social', 'LIKE', "%{$params->razon_social}%")
                        ->orWhere('nombre_fantasia', 'LIKE', "%{$params->razon_social}%");
                })->orWhereHas('pago.opa.prestador', function ($sql) use ($params) {
                    $sql->where('razon_social', 'LIKE', "%{$params->razon_social}%")
                        ->orWhere('nombre_fantasia', 'LIKE', "%{$params->razon_social}%");
                });
            });
        }

        if (!is_null($params->cuit ?? null) && $params->cuit !== '') {
            $query->where(function ($q) use ($params) {
                $q->whereHas('pago.opa.proveedor', function ($sql) use ($params) {
                    $sql->where('cuit', 'LIKE', "%{$params->cuit}%");
                })->orWhereHas('pago.opa.prestador', function ($sql) use ($params) {
                    $sql->where('cuit', 'LIKE', "%{$params->cuit}%");
                });
            });
        }

        $query->orderByDesc('fecha_registra');

        return $query->get();
    }

    /**
     * Elimina una retención y recalc en cascada
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $retencion = PagoRetencionesEntity::findOrFail($id);
            $idPago = $retencion->id_pago;

            $retencion->delete();

            // Recalcular totales del pago
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
            return [
                'error' => false,
                'message' => 'Retención eliminada exitosamente'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminar retencion: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Error al eliminar retención: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }
}
