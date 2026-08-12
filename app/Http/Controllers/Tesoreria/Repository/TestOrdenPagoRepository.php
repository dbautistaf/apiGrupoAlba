<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesEstadoOrdenPagoEntity;
use App\Models\Tesoreria\TesFechaProbablePagoEntity;
use App\Models\Tesoreria\TesOrdenPagoDetalleEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use App\Models\Tesoreria\TesPagoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestOrdenPagoRepository
{
    // Estados de tb_tes_estado_orden_pago
    const ESTADO_OPA_PENDIENTE = 1;
    const ESTADO_OPA_APROBADO  = 2;
    const ESTADO_OPA_RECHAZADO = 3;
    const ESTADO_OPA_EN_PROCESO = 4;
    const ESTADO_OPA_PAGADO    = 5;

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByListTipoEstado()
    {
        return TesEstadoOrdenPagoEntity::get();
    }

    public function findListAlls($desde, $hasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'prestador'])
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnEstado($estado, $desde, $hasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'prestador'])
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnTipo($tipo, $desde, $hasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor',  'prestador'])
            ->where('tipo_factura', $tipo)
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnTipoAndEstado($tipo, $estado, $desde, $hasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'prestador'])
            ->where('tipo_factura', $tipo)
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnTipoAndEstadoAndMontos($tipo, $estado, $desde, $hasta, $montDesde, $montHasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor',  'prestador'])
            ->where('tipo_factura', $tipo)
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween('monto_orden_pago', [$montDesde, $montHasta])
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnTipoAndEstadoAndMontosAndBeneficiario($tipo, $estado, $desde, $hasta, $montDesde, $montHasta, $beneficiario)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor',  'prestador'])
            ->where('tipo_factura', $tipo)
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween('monto_orden_pago', [$montDesde, $montHasta])
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->where(function ($query) use ($beneficiario) {
                $query->whereHas('proveedor', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                })->orWhereHas('prestador', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                });
            })
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnEstadoAndBeneficiario($estado, $desde, $hasta, $beneficiario)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'prestador'])
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->where(function ($query) use ($beneficiario) {
                $query->whereHas('proveedor', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                })->orWhereHas('prestador', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                });
            })
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAndBeneficiario($desde, $hasta, $beneficiario)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'prestador'])
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->where(function ($query) use ($beneficiario) {
                $query->whereHas('proveedor', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                })->orWhereHas('prestador', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                });
            })
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function getFiltroDinamico($params)
    {
        $query = TesOrdenPagoEntity::with([
            'estado',
            'opadetalle.detallefc.razonSocial',
            'opadetalle.detallefc.comprobantes',
            'proveedor',
            'prestador',
            'pagoFecha.fechaprobablepagos',
            'opadetalle',
            'opadetalle.detallefc',
        ]);

        $query = TesOrdenPagoEntity::with([
            'estado',
            'opadetalle.detallefc.razonSocial',
            'opadetalle.detallefc.comprobantes',
            'proveedor',
            'prestador',
            'pagoFecha.fechaprobablepagos',
            'opadetalle',
            'opadetalle.detallefc',
        ]);

        if (!is_null($params->tipo)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('opadetalle.detallefc', function ($subQuery) use ($params) {
                    $subQuery->where('tipo_factura', $params->tipo);
                });
            });
        }

        if (!is_null($params->estado)) {
            $query->where('id_estado_orden_pago', $params->estado);
        }

        if (!is_null($params->monto_desde) && !is_null($params->monto_hasta)) {
            $query->whereBetween('monto_orden_pago', [$params->monto_desde, $params->monto_hasta]);
        }

        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $query->whereBetween(DB::raw('DATE(fecha_genera)'), [$params->desde, $params->hasta]);
        }

        if (!is_null($params->beneficiario)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('proveedor', function ($subQuery) use ($params) {
                    $subQuery->where('razon_social', 'LIKE', "{$params->beneficiario}%");
                })->orWhereHas('prestador', function ($subQuery) use ($params) {
                    $subQuery->where('razon_social', 'LIKE', "{$params->beneficiario}%");
                });
            });
        }

        if (!is_null($params->id_locatorio)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('opadetalle.detallefc', function ($subQuery) use ($params) {
                    $subQuery->where('id_locatorio', $params->id_locatorio);
                });
            });
        }

        if (!is_null($params->id_tipo_imputacion)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('opadetalle.detallefc', function ($subQuery) use ($params) {
                    $subQuery->where('id_tipo_imputacion_sintetizada', $params->id_tipo_imputacion);
                });
            });
        }

        if ($params->pago_urgente == '1') {
            $query->where('pago_emergencia', $params->pago_urgente);
        }

        if (!is_null($params->n_factura)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('opadetalle.detallefc', function ($subQuery) use ($params) {
                    $subQuery->where('numero', $params->n_factura);
                });
            });
        }

        return $query
            //->leftJoin('tb_prestador as p', 'p.cod_prestador', '=', 'tb_tes_orden_pago.id_prestador')
            //->leftJoin('tb_proveedor as prov', 'prov.cod_proveedor', '=', 'tb_tes_orden_pago.id_proveedor')
            //->orderByRaw('COALESCE(p.razon_social, prov.razon_social)')
            ->orderBy('monto_orden_pago')
            ->get();
    }

    public function findByOpaFactura($idFactura, $estado)
    {
        return TesOrdenPagoEntity::where('id_factura', $idFactura)
            ->where('id_estado_orden_pago', $estado)->first();
    }

    /**
     * OPA vigente de una factura: cualquier estado MENOS RECHAZADO (3), que se considera muerta
     * y por lo tanto habilita generar una nueva.
     *
     * Existe porque buscar solo por estado PENDIENTE (1) hacía que, si la OPA ya había avanzado
     * a EN PROCESO/PAGADO, el sistema creyera que no existía y generara una segunda OPA para la
     * misma factura (se detectaron 6 casos así en producción). Ver docs/pendientes.md.
     */
    public function findByOpaVigenteFactura($idFactura)
    {
        // OJO: en las OPAs AGRUPADAS la cabecera tiene id_factura = NULL — el vínculo con las
        // facturas vive solo en tb_tes_orden_pago_detalle. Por eso hay que buscar por los dos
        // lados: mirar solo la cabecera dejaba fuera a todas las agrupadas.
        return TesOrdenPagoEntity::where('id_estado_orden_pago', '!=', self::ESTADO_OPA_RECHAZADO)
            ->where(function ($q) use ($idFactura) {
                $q->where('id_factura', $idFactura)
                    ->orWhereExists(function ($sub) use ($idFactura) {
                        $sub->select(DB::raw(1))
                            ->from('tb_tes_orden_pago_detalle as d')
                            ->whereColumn('d.id_orden_pago', 'tb_tes_orden_pago.id_orden_pago')
                            ->where('d.id_factura', $idFactura);
                    });
            })
            ->orderBy('id_orden_pago')
            ->first();
    }

    public function findByExistsOpaFacturaEstado($idFactura, $estado)
    {
        return TesOrdenPagoEntity::where('id_factura', $idFactura)
            ->where('id_estado_orden_pago', $estado)->exists();
    }

    public function findByCreate($param)
    {
        $opa = TesOrdenPagoEntity::create([
            'id_proveedor' => $param->id_proveedor,
            'id_prestador' => $param->id_prestador,
            'monto_orden_pago' => $param->monto_orden_pago,
            'id_moneda' => $param->id_moneda,
            'fecha_emision' => $param->fecha_emision,
            'fecha_vencimiento' => $param->fecha_vencimiento,
            'fecha_probable_pago' => $param->fecha_probable_pago,
            'id_estado_orden_pago' => $param->id_estado_orden_pago,
            'monto_anticipado' => $param->monto_anticipado,
            'observaciones' => $param->observaciones,
            'cod_usuario' => $this->user->cod_usuario,
            'fecha_genera' => $this->fechaActual,
            'id_factura' => $param->id_factura,
            'tipo_factura' => $param->tipo_factura
        ]);

        TesOrdenPagoDetalleEntity::create([
            'id_orden_pago' => $opa->id_orden_pago,
            'id_factura' => $param->id_factura,
            'monto_factura' => $param->monto_orden_pago,
            'tipo_factura' => $param->tipo_factura,
            'factura_unida' => 0
        ]);
        return $opa;
    }

    public function findByUpdateOpaFactura($param)
    {
        $opa = TesOrdenPagoEntity::find($param->id_orden_pago);
        $opa->monto_orden_pago = $param->monto_orden_pago;
        $opa->update();
    }

    public function findByUpdate($param)
    {
        $tes = TesOrdenPagoEntity::find($param->id_orden_pago);
        $tes->id_moneda = $param->id_moneda;
        $tes->fecha_emision = $param->fecha_emision;
        $tes->fecha_vencimiento = $param->fecha_vencimiento;
        $tes->fecha_probable_pago = $param->fecha_probable_pago;
        $tes->observaciones = $param->observaciones;
        $tes->pago_emergencia = $param->pago_emergencia;
        $tes->update();

        //$idsFinales[]=[];
        $id_pago = $param->fechaprobablepagos[0]['id_pago'];
        foreach ($param->fechaprobablepagos as $index => $item) {
            $orden = $index + 1;
            if (!empty($item['id_fecha_probable'])) {
                $detalle = TesFechaProbablePagoEntity::find($item['id_fecha_probable']);
                if ($detalle) {
                    $detalle->fecha_probable_pago = $item['fecha_probable_pago'];
                    $detalle->orden_cuotas = $item['orden_cuotas'];
                    $detalle->update();
                }
                $idsFinales[] = $detalle->id_fecha_probable;
            } else {
                $nuevo = TesFechaProbablePagoEntity::create([
                    'fecha_registra' => $item['fecha_registra'],
                    'fecha_probable_pago' => $item['fecha_probable_pago'],
                    'orden_cuotas' => $orden,
                    'id_pago' => $id_pago,
                ]);
                $idsFinales[] = $nuevo->id_fecha_probable;
            }
        }

        TesFechaProbablePagoEntity::where('id_pago', $id_pago)
            ->whereNotIn('id_fecha_probable', $idsFinales)
            ->delete();
        return $tes;
    }

    public function findByUpdateEstado($id_opa, $estado, $motivoRechazo = null)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->id_estado_orden_pago = $estado;
        if ($estado == '3') {
            $tes->motivo_rechazo = $motivoRechazo;
            $tes->fecha_rechazo = $this->fechaActual;
            $tes->cod_usuario_rechaza = $this->user->cod_usuario ?? null;
        }
        $tes->update();
        return $tes;
    }

    /**
     * ¿La OPA tiene pagos vivos? (pagos no anulados — un pago anulado queda en estado 3)
     * Es el criterio real para saber si se puede rechazar: no alcanza con mirar el estado de la
     * OPA. De 982 OPAs EN PROCESO relevadas, 977 tenían pago vivo pero 5 no.
     */
    public function findByOpaTienePagosVivos($idOpa)
    {
        return TesPagoEntity::where('id_orden_pago', $idOpa)
            ->where('id_estado_orden_pago', '!=', self::ESTADO_OPA_RECHAZADO)
            ->exists();
    }

    /**
     * Anula (rechaza) la OPA vigente de una factura, dejando constancia del motivo y del usuario.
     *
     * - Si la OPA agrupa varias facturas, primero desagrupa ESTA factura a una OPA propia
     *   (findRemoveFacturaMultiple) y rechaza solo esa: las demás facturas del grupo no se tocan.
     * - Si la OPA tiene pagos vivos, NO anula y devuelve el motivo del bloqueo.
     *
     * No abre transacción propia: depende de la del controlador.
     *
     * @return array{anulada: bool, message: string, opa: TesOrdenPagoEntity|null}
     */
    public function findByAnularOpaDeFactura($idFactura, $motivo = null)
    {
        $opa = $this->findByOpaVigenteFactura($idFactura);

        if (is_null($opa)) {
            return ['anulada' => false, 'message' => 'La factura no tiene una orden de pago vigente.', 'opa' => null];
        }

        if ($this->findByOpaTienePagosVivos($opa->id_orden_pago)) {
            return [
                'anulada' => false,
                'opa' => $opa,
                'message' => 'No se puede anular: la orden de pago ' . $opa->num_orden_pago
                    . ' ya tiene pagos asociados. Primero hay que anular esos pagos.'
            ];
        }

        // Si la OPA agrupa varias facturas, desagrupar esta a una OPA propia antes de rechazar,
        // para no afectar a las demás facturas del grupo.
        $cantFacturasEnOpa = TesOrdenPagoDetalleEntity::where('id_orden_pago', $opa->id_orden_pago)->count();

        if ($cantFacturasEnOpa > 1) {
            $opa = $this->findRemoveFacturaMultiple((object) [
                'idFactura'    => $idFactura,
                'idOrdenPago'  => $opa->id_orden_pago,
            ]);
        }

        $opa = $this->findByUpdateEstado($opa->id_orden_pago, self::ESTADO_OPA_RECHAZADO, $motivo);
        // refresh: si la OPA se acaba de crear al desagrupar, el num_orden_pago lo asigna la base
        // y el modelo en memoria todavía no lo tiene.
        $opa->refresh();

        return [
            'anulada' => true,
            'opa' => $opa,
            'message' => 'Se anuló la orden de pago ' . $opa->num_orden_pago . '.'
        ];
    }

    public function findByConfirmarEstado($id_opa, $fechaPago, $estado)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->id_estado_orden_pago = $estado;
        $tes->fecha_confirma_pago = $fechaPago;
        $tes->update();
        return $tes->load(['proveedor']);
    }

    public function findByConfirmarFechaProbablePago($id_opa, $fechaProbablePago, $cuotas)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->fecha_probable_pago = $fechaProbablePago;
        $tes->cuotas = $cuotas;
        $tes->update();
        return $tes->load(['proveedor']);
    }

    public function findByConfirmarPagoEmergencia($id_opa, $emergencia)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->pago_emergencia = $emergencia;
        $tes->update();
        return $tes->load(['proveedor']);
    }

    public function findByAnticipoPago($id_opa, $montoAnticipo)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->monto_anticipado = $montoAnticipo;
        $tes->update();
        return $tes->load(['proveedor']);
    }

    public function findByExistsOpaEstado($id, $estado)
    {
        return TesOrdenPagoEntity::where('id_orden_pago', $id)
            ->where('id_estado_orden_pago', $estado)
            ->exists();
    }

    public function findById($idOpa)
    {
        return TesOrdenPagoEntity::find($idOpa);
    }

    public function findByIdFacturaEnProcesoOrPendiente($factura, $montoFactura)
    {
        // $idEstados = is_array([1, 4]);
        $opa = TesOrdenPagoEntity::where('id_factura', $factura)
            ->where('tipo_factura', 'PRESTADOR')
            ->whereIn('id_estado_orden_pago', [1, 4])
            ->first();
        if ($opa != null) {
            $opa->monto_orden_pago = $montoFactura;
            $opa->update();
        }
        return $opa ?? null;
    }

    /**
     * Recalcula el monto de la cabecera de una OPA sumando su detalle. (2026-08-11)
     *
     * La cabecera NUNCA debe mantenerse con `+=` / `-=`: si una operación fallaba a mitad,
     * el monto quedaba desincronizado del detalle y el error se propagaba en cada agrupado
     * posterior (se detectaron OPAs con el monto duplicado exacto en cadena).
     * El detalle es la fuente de verdad.
     */
    private function recalcularMontoDesdeDetalle($idOpa): float
    {
        $total = (float) TesOrdenPagoDetalleEntity::where('id_orden_pago', $idOpa)
            ->sum('monto_factura');

        TesOrdenPagoEntity::where('id_orden_pago', $idOpa)
            ->update(['monto_orden_pago' => $total]);

        return $total;
    }

    /**
     * Indica si una OPA tiene pagos vivos (no rechazados). Se usa como guarda antes de
     * cualquier borrado físico: borrar una OPA pagada deja el pago sin respaldo. (2026-08-11)
     */
    private function tienePagosVivos($idOpa): bool
    {
        return TesPagoEntity::where('id_orden_pago', $idOpa)
            ->where('id_estado_orden_pago', '!=', self::ESTADO_OPA_RECHAZADO)
            ->exists();
    }

    public function findByIdFacturaMultiple($idFacturas)
    {
        $detalleOpa = TesOrdenPagoDetalleEntity::whereIn('id_factura', $idFacturas)->get();
        $idOrdenes = $detalleOpa->pluck('id_orden_pago')->unique()->values()->toArray();

        $opa = TesOrdenPagoEntity::whereIn('id_orden_pago', $idOrdenes)->get();
        $first = $opa->first();

        // No agrupar OPAs que ya tienen pagos: más abajo se las borra físicamente y el pago
        // quedaría sin orden de pago que lo respalde. (2026-08-11)
        foreach ($idOrdenes as $idOrdenExistente) {
            if ($this->tienePagosVivos($idOrdenExistente)) {
                throw new \Exception(
                    "No se puede agrupar: la orden de pago {$idOrdenExistente} ya tiene pagos asociados."
                );
            }
        }

        // El total sale del DETALLE, no de sumar cabeceras: si alguna cabecera venía con el
        // monto desincronizado, sumarlas propagaba el error al agrupado nuevo. (2026-08-11)
        $totalMonto = (float) $detalleOpa->sum('monto_factura');
        if ($first != null) {
            $newOpa = TesOrdenPagoEntity::create([
                'id_proveedor' => $first->id_proveedor ?? null,
                'id_prestador' => $first->id_prestador,
                'monto_orden_pago' => $totalMonto,
                'id_moneda' => $first->id_moneda,
                'fecha_emision' => $first->fecha_emision,
                'fecha_vencimiento' => $first->fecha_vencimiento,
                'fecha_probable_pago' => $first->fecha_probable_pago,
                'id_estado_orden_pago' => $first->id_estado_orden_pago,
                'monto_anticipado' => $first->monto_anticipado,
                'observaciones' => $first->observaciones,
                'cod_usuario' => $this->user->cod_usuario,
                'fecha_genera' => $this->fechaActual
            ]);

            foreach ($detalleOpa as $det) {

                TesOrdenPagoDetalleEntity::create([
                    'id_orden_pago' => $newOpa->id_orden_pago,
                    'id_factura' => $det->id_factura,
                    'monto_factura' => $det->monto_factura,
                    'tipo_factura' => $det->tipo_factura,
                    'factura_unida' => 1
                ]);
            }
            TesOrdenPagoDetalleEntity::whereIn('id_orden_pago', $idOrdenes)->delete();
            TesOrdenPagoEntity::whereIn('id_orden_pago', $idOrdenes)->delete();
            return $newOpa;
        }
    }

    public function findAddFacturaMultiple($request)
    {
        // Se acepta idOrdenPagoOrigen para desambiguar. Antes se hacía `->first()` a secas: si la
        // factura figuraba en el detalle de más de una OPA, tomaba una cualquiera (sin orden
        // definido) y agrupaba desde la OPA equivocada, salteándose incluso la guarda de pagos.
        // Mismo defecto que ya se había corregido en findRemoveFacturaMultiple. (2026-08-11)
        $candidatos = TesOrdenPagoDetalleEntity::where('id_factura', $request->idFactura)
            ->when(
                !empty($request->idOrdenPagoOrigen),
                fn($q) => $q->where('id_orden_pago', $request->idOrdenPagoOrigen)
            )
            ->get();

        if ($candidatos->count() > 1) {
            $opasAmbiguas = $candidatos->pluck('id_orden_pago')->implode(', ');
            return [
                'success' => false,
                'message' => "La factura {$request->idFactura} figura en más de una orden de pago ({$opasAmbiguas}). "
                    . "Indicá desde cuál se debe agrupar."
            ];
        }

        $detalleOpa = $candidatos->first();

        if (!$detalleOpa) {
            return [
                'success' => false,
                'message' => 'No se encontró la factura'
            ];
        }

        if ($detalleOpa->factura_unida == 1) {
            return [
                'success' => false,
                'message' => 'No puede agrupar esta factura porque ya está agrupada'
            ];
        }

        if ($detalleOpa->id_orden_pago == $request->idOrdenPago) {
            return [
                'success' => false,
                'message' => 'La factura ya pertenece a esta OPA'
            ];
        }

        $opa = TesOrdenPagoEntity::where('id_orden_pago', $detalleOpa->id_orden_pago)->first();
        $getOpa = TesOrdenPagoEntity::where('id_orden_pago', $request->idOrdenPago)->first();

        if (!$opa || !$getOpa) {
            return [
                'success' => false,
                'message' => 'No se encontró la OPA'
            ];
        }

        if ($this->tienePagosVivos($opa->id_orden_pago)) {
            return [
                'success' => false,
                'message' => 'No se puede agrupar esta factura: su orden de pago ya tiene pagos asociados.'
            ];
        }

        // Todo el movimiento va en una sola transacción. Antes no lo estaba: si fallaba entre
        // el borrado del detalle y el de la cabecera, la OPA origen quedaba viva SIN detalle
        // y sin id_factura — imposible de rastrear. Se detectaron 8 casos así en producción
        // por $22.873.016. (2026-08-11)
        $newOpa = DB::transaction(function () use ($opa, $getOpa, $detalleOpa) {
            $detalleCreado = TesOrdenPagoDetalleEntity::create([
                'id_orden_pago' => $getOpa->id_orden_pago,
                'id_factura' => $detalleOpa->id_factura,
                'monto_factura' => $detalleOpa->monto_factura,
                'tipo_factura' => $detalleOpa->tipo_factura,
                'factura_unida' => 1
            ]);

            // Se borra SOLO la fila migrada, no todo el detalle de la OPA origen: el borrado
            // masivo hacía desaparecer las demás facturas dejando su monto sumado acá. (2026-08-11)
            TesOrdenPagoDetalleEntity::where('id_orden_pago', $detalleOpa->id_orden_pago)
                ->where('id_factura', $detalleOpa->id_factura)
                ->delete();

            $quedanEnOrigen = TesOrdenPagoDetalleEntity::where('id_orden_pago', $opa->id_orden_pago)->count();
            if ($quedanEnOrigen === 0) {
                TesOrdenPagoEntity::where('id_orden_pago', $opa->id_orden_pago)->delete();
            } else {
                $this->recalcularMontoDesdeDetalle($opa->id_orden_pago);
            }

            // Ensure the existing details in the target OPA are marked as grouped
            TesOrdenPagoDetalleEntity::where('id_orden_pago', $getOpa->id_orden_pago)->update(['factura_unida' => 1]);

            // El monto se recalcula desde el detalle en vez de acumularse con `+=`. (2026-08-11)
            $this->recalcularMontoDesdeDetalle($getOpa->id_orden_pago);

            return $detalleCreado;
        });

        return [
            'success' => true,
            'message' => 'Se agregó una factura a la OPA',
            'data' => $newOpa
        ];
    }

    public function findRemoveFacturaMultiple($request)
    {
        // Se filtra TAMBIÉN por id_orden_pago: buscar solo por id_factura tomaba una fila
        // cualquiera (sin orden definido) y, si la factura estaba en más de una OPA, desagrupaba
        // de la OPA equivocada. Si no viene idOrdenPago se mantiene el comportamiento anterior
        // por compatibilidad con las llamadas existentes.
        $detalleOpa = TesOrdenPagoDetalleEntity::where('id_factura', $request->idFactura)
            ->when(
                !empty($request->idOrdenPago),
                fn($q) => $q->where('id_orden_pago', $request->idOrdenPago)
            )
            ->first();

        if (!$detalleOpa) {
            throw new \Exception("No se encontró el detalle de la factura {$request->idFactura} en la orden de pago indicada.");
        }

        $opa = TesOrdenPagoEntity::where('id_orden_pago', $detalleOpa->id_orden_pago)->first();

        $newopa = TesOrdenPagoEntity::create([
            'id_proveedor' => $opa->id_proveedor,
            'id_prestador' => $opa->id_prestador,
            'monto_orden_pago' => $detalleOpa->monto_factura,
            'id_moneda' => $opa->id_moneda,
            'fecha_emision' => $opa->fecha_emision,
            'fecha_vencimiento' => $opa->fecha_vencimiento,
            'fecha_probable_pago' => $opa->fecha_probable_pago,
            'id_estado_orden_pago' => 1,
            'monto_anticipado' => $opa->monto_anticipado,
            'observaciones' => $opa->observaciones,
            'cod_usuario' => $this->user->cod_usuario,
            'fecha_genera' => $this->fechaActual,
            'id_factura' => $detalleOpa->id_factura,
            'tipo_factura' => $detalleOpa->tipo_factura
        ]);

        TesOrdenPagoDetalleEntity::create([
            'id_orden_pago' => $newopa->id_orden_pago,
            'id_factura' => $detalleOpa->id_factura,
            'monto_factura' => $detalleOpa->monto_factura,
            'tipo_factura' => $detalleOpa->tipo_factura,
            'factura_unida' => 0
        ]);

        // El monto de la OPA origen se recalcula desde su detalle más abajo, una vez quitada
        // la fila. Antes se hacía `-=` sobre la cabecera, que se desincronizaba del detalle
        // ante cualquier fallo intermedio. (2026-08-11)

        // Delete the detail from the original OPA
        TesOrdenPagoDetalleEntity::where('id_factura', $request->idFactura)->where('id_orden_pago', $detalleOpa->id_orden_pago)->delete();
        
        // If the original OPA has no more details, delete it. If it has 1 detail left, mark it as unida=0
        $remainingDetails = TesOrdenPagoDetalleEntity::where('id_orden_pago', $opa->id_orden_pago)->get();
        if ($remainingDetails->count() == 0) {
            // Guarda: no borrar físicamente una OPA con pagos, el pago quedaría sin respaldo. (2026-08-11)
            if ($this->tienePagosVivos($opa->id_orden_pago)) {
                throw new \Exception(
                    "No se puede desagrupar: la orden de pago {$opa->id_orden_pago} quedaría vacía y tiene pagos asociados."
                );
            }
            $opa->delete();
        } else {
            if ($remainingDetails->count() == 1) {
                $firstDetalle = $remainingDetails->first();
                $firstDetalle->factura_unida = 0;
                $firstDetalle->save();
            }
            $this->recalcularMontoDesdeDetalle($opa->id_orden_pago);
        }

        return $newopa;
    }
}
