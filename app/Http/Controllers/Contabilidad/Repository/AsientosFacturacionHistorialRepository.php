<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\AsientosFacturacionHistorialEntity;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsientosFacturacionHistorialRepository
{
    private $user;
    private $fechaActual;
    private $asientoContableRepository;

    public function __construct(AsientoContableRepository $asientoContableRepository)
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $this->asientoContableRepository = $asientoContableRepository;
    }

    public function obtenerAsientoVigenteFactura($idFactura)
    {
        return AsientosFacturacionHistorialEntity::with(['asientoContable', 'asientoContable.detalle'])
            ->where('id_factura', $idFactura)
            ->where('tipo_evento', 'ALTA')
            ->where('es_contraasiento', false)
            ->whereHas('asientoContable', function ($query) {
                $query->where('vigente', 'ACTIVO');
            })
            ->first();
    }

    /**
     * Genera un contraasiento invirtiendo DEBE ↔ HABER del asiento original.
     * No gestiona transacción propia — depende de la transacción del controlador.
     */
    public function generarContraasiento($idAsientoOriginal, $tipoEvento, $idFactura, $observacion = null)
    {
        $asientoOriginal = AsientosContablesEntity::with('detalle')->find($idAsientoOriginal);

        if (!$asientoOriginal) {
            throw new Exception("No se encontró el asiento contable original (ID: {$idAsientoOriginal}).");
        }

        $numeroCorrelativo = $this->asientoContableRepository->obtenerSiguienteNumeroAsiento();

        $contraasiento = $this->asientoContableRepository->findByCrearAsiento(
            $asientoOriginal->id_tipo_asiento,
            'CONTRAASIENTO - ' . $asientoOriginal->asiento_modelo,
            'CONTRAASIENTO - ' . $asientoOriginal->asiento_leyenda . ' - ' . strtoupper($tipoEvento),
            $numeroCorrelativo,
            $asientoOriginal->id_periodo_contable,
            $asientoOriginal->numero,
            'ACTIVO',
            $asientoOriginal->id_razon
        );

        foreach ($asientoOriginal->detalle as $detalleOriginal) {
            $this->asientoContableRepository->findByCrearDetalleAsiento([
                'id_asiento_contable'               => $contraasiento->id_asiento_contable,
                'id_proveedor_cuenta_contable'      => $detalleOriginal->id_proveedor_cuenta_contable,
                'id_forma_pago_cuenta_contable'     => $detalleOriginal->id_forma_pago_cuenta_contable,
                'id_familia_cuenta_contable'        => $detalleOriginal->id_familia_cuenta_contable,
                'id_cuenta_bancaria_cuenta_contable'=> $detalleOriginal->id_cuenta_bancaria_cuenta_contable,
                'monto_debe'                        => $detalleOriginal->monto_haber,
                'monto_haber'                       => $detalleOriginal->monto_debe,
                'observaciones'                     => 'CONTRAASIENTO - ' . $detalleOriginal->observaciones,
                'id_detalle_plan'                   => $detalleOriginal->id_detalle_plan
            ]);
        }

        $this->asientoContableRepository->findByAnularAsientoContableId($idAsientoOriginal, 'CONTRAASIENTO');

        $this->guardarHistorial(
            $idFactura,
            $contraasiento->id_asiento_contable,
            $tipoEvento,
            true,
            $idAsientoOriginal,
            $observacion ?? "Contraasiento generado por {$tipoEvento} de factura"
        );

        return $contraasiento;
    }

    public function guardarHistorial($idFactura, $idAsientoContable, $tipoEvento, $esContraasiento = false, $idAsientoOrigen = null, $observacion = null)
    {
        return AsientosFacturacionHistorialEntity::create([
            'id_factura'         => $idFactura,
            'id_asiento_contable'=> $idAsientoContable,
            'tipo_evento'        => $tipoEvento,
            'es_contraasiento'   => $esContraasiento,
            'id_asiento_origen'  => $idAsientoOrigen,
            'observacion'        => $observacion,
            'cod_usuario'        => $this->user->cod_usuario,
            'fecha_registra'     => $this->fechaActual
        ]);
    }

    public function obtenerHistorialFactura($idFactura)
    {
        return AsientosFacturacionHistorialEntity::with([
            'asientoContable',
            'asientoOrigen',
            'factura'
        ])
            ->where('id_factura', $idFactura)
            ->orderBy('fecha_registra', 'desc')
            ->get();
    }

    /**
     * Procesa la anulación contable de una factura.
     * No gestiona transacción propia — depende de la transacción del controlador.
     */
    public function procesarAnulacionFactura($idFactura, $observacion = null)
    {
        $historialVigente = $this->obtenerAsientoVigenteFactura($idFactura);

        if (!$historialVigente) {
            throw new Exception("No se encontró un asiento contable vigente para la factura ID: {$idFactura}.");
        }

        return $this->generarContraasiento(
            $historialVigente->id_asiento_contable,
            'ANULACION',
            $idFactura,
            $observacion
        );
    }

    /**
     * Procesa la modificación contable de una factura: contraasiento + nuevo asiento.
     * No gestiona transacción propia — depende de la transacción del controlador.
     */
    public function procesarModificacionFactura($idFactura, $nuevoDatosFactura, $idPeriodoContable, $observacion = null)
    {
        $historialVigente = $this->obtenerAsientoVigenteFactura($idFactura);

        if (!$historialVigente) {
            throw new Exception("No se encontró un asiento contable vigente para la factura ID: {$idFactura}.");
        }

        $this->generarContraasiento(
            $historialVigente->id_asiento_contable,
            'MODIFICACION',
            $idFactura,
            'Contraasiento por modificación de factura'
        );

        $nuevoAsiento = $this->asientoContableRepository->crearAsientoFactura(
            $nuevoDatosFactura,
            $idPeriodoContable
        );

        $this->guardarHistorial(
            $idFactura,
            $nuevoAsiento->id_asiento_contable,
            'ALTA',
            false,
            null,
            $observacion ?? 'Nuevo asiento por modificación de factura'
        );

        return $nuevoAsiento;
    }

    public function facturaTieneAsientos($idFactura)
    {
        return AsientosFacturacionHistorialEntity::where('id_factura', $idFactura)->exists();
    }

    public function obtenerUltimoAsientoPorTipo($idFactura, $tipoEvento)
    {
        return AsientosFacturacionHistorialEntity::with('asientoContable')
            ->where('id_factura', $idFactura)
            ->where('tipo_evento', $tipoEvento)
            ->orderBy('fecha_registra', 'desc')
            ->first();
    }

    public function listarFacturasConProblemasContables()
    {
        return DB::select("
            SELECT f.id_factura, f.numero, f.total_neto, f.fecha_comprobante,
                   COUNT(h.id_facturacion_asiento) as total_asientos,
                   SUM(CASE WHEN ac.vigente = 'ACTIVO' THEN 1 ELSE 0 END) as asientos_activos
            FROM tb_facturacion_datos f
            LEFT JOIN tb_cont_asientos_facturacion_historial h ON f.id_factura = h.id_factura
            LEFT JOIN tb_cont_asientos_contables ac ON h.id_asiento_contable = ac.id_asiento_contable
            GROUP BY f.id_factura, f.numero, f.total_neto, f.fecha_comprobante
            HAVING asientos_activos != 1 OR asientos_activos IS NULL
            ORDER BY f.fecha_comprobante DESC
        ");
    }
}
