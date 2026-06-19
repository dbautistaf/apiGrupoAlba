<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\AsientosDiscapacidadHistorialEntity;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsientosDiscapacidadHistorialRepository
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

    /**
     * Obtener asiento vigente de una prestación de discapacidad
     */
    public function obtenerAsientoVigenteDiscapacidad($idDiscapacidad)
    {
        return AsientosDiscapacidadHistorialEntity::with(['asientoContable', 'asientoContable.detalle'])
            ->where('id_discapacidad', $idDiscapacidad)
            ->where('tipo_evento', 'ALTA')
            ->where('es_contraasiento', false)
            ->whereHas('asientoContable', function ($query) {
                $query->where('vigente', 'ACTIVO');
            })
            ->first();
    }

    /**
     * Generar contraasiento para anulación/modificación
     */
    public function generarContraasiento($idAsientoOriginal, $tipoEvento, $idDiscapacidad, $observacion = null)
    {
        // Obtener el asiento original con sus detalles
        $asientoOriginal = AsientosContablesEntity::with('detalle')->find($idAsientoOriginal);

        if (!$asientoOriginal) {
            throw new Exception("No se encontró el asiento contable original");
        }

        // Obtener siguiente número correlativo
        $numeroCorrelativo = $this->asientoContableRepository->obtenerSiguienteNumeroAsiento();

        // Crear contraasiento con valores invertidos
        $contraasiento = $this->asientoContableRepository->findByCrearAsiento(
            2, // Tipo 2 = Contraasiento
            'CONTRAASIENTO - ' . $asientoOriginal->asiento_modelo,
            'CONTRAASIENTO - ' . $asientoOriginal->asiento_leyenda . ' - ' . strtoupper($tipoEvento),
            $numeroCorrelativo,
            $asientoOriginal->id_periodo_contable,
            $asientoOriginal->numero, // Referencia al asiento original
            'ACTIVO'
        );

        // Crear detalles del contraasiento con valores invertidos
        foreach ($asientoOriginal->detalle as $detalleOriginal) {
            $this->asientoContableRepository->findByCrearDetalleAsiento([
                'id_asiento_contable' => $contraasiento->id_asiento_contable,
                'cod_prestador' => $detalleOriginal->cod_prestador,
                'id_proveedor_cuenta_contable' => $detalleOriginal->id_proveedor_cuenta_contable,
                'id_tipo_prestador_cuenta_contable' => $detalleOriginal->id_tipo_prestador_cuenta_contable,
                'id_forma_pago_cuenta_contable' => $detalleOriginal->id_forma_pago_cuenta_contable,
                'id_familia_cuenta_contable' => $detalleOriginal->id_familia_cuenta_contable,
                'id_cuenta_bancaria_cuenta_contable' => $detalleOriginal->id_cuenta_bancaria_cuenta_contable,
                'monto_debe' => $detalleOriginal->monto_haber, // Invertir debe por haber
                'monto_haber' => $detalleOriginal->monto_debe, // Invertir haber por debe
                'observaciones' => 'CONTRAASIENTO - ' . $detalleOriginal->observaciones,
                'id_detalle_plan' => $detalleOriginal->id_detalle_plan
            ]);
        }

        // Cambiar estado del asiento original a CONTRAASIENTO
        $this->asientoContableRepository->findByAnularAsientoContableId($idAsientoOriginal, 'CONTRAASIENTO');

        // Guardar en historial
        $this->guardarHistorial(
            $idDiscapacidad,
            $contraasiento->id_asiento_contable,
            $tipoEvento,
            true, // es_contraasiento
            $idAsientoOriginal,
            $observacion ?? "Contraasiento generado por $tipoEvento de prestación de discapacidad"
        );

        return $contraasiento;
    }

    /**
     * Guardar historial de asiento de discapacidad
     */
    public function guardarHistorial($idDiscapacidad, $idAsientoContable, $tipoEvento, $esContraasiento = false, $idAsientoOrigen = null, $observacion = null)
    {
        return AsientosDiscapacidadHistorialEntity::create([
            'id_discapacidad' => $idDiscapacidad,
            'id_asiento_contable' => $idAsientoContable,
            'tipo_evento' => $tipoEvento,
            'es_contraasiento' => $esContraasiento,
            'id_asiento_origen' => $idAsientoOrigen,
            'observacion' => $observacion,
            'cod_usuario' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    /**
     * Obtener historial completo de una prestación de discapacidad
     */
    public function obtenerHistorialDiscapacidad($idDiscapacidad)
    {
        return AsientosDiscapacidadHistorialEntity::with([
            'asientoContable',
            'asientoOrigen',
            'discapacidad'
        ])
            ->where('id_discapacidad', $idDiscapacidad)
            ->orderBy('fecha_registra', 'desc')
            ->get();
    }

    /**
     * Procesar anulación de prestación de discapacidad
     */
    public function procesarAnulacionDiscapacidad($idDiscapacidad, $observacion = null)
    {
        // Obtener asiento vigente
        $historialVigente = $this->obtenerAsientoVigenteDiscapacidad($idDiscapacidad);

        if (!$historialVigente) {
            throw new Exception("No se encontró un asiento contable vigente para esta prestación de discapacidad");
        }

        // Generar contraasiento
        $contraasiento = $this->generarContraasiento(
            $historialVigente->id_asiento_contable,
            'ANULACION',
            $idDiscapacidad,
            $observacion
        );

        return $contraasiento;
    }

    /**
     * Procesar modificación de prestación de discapacidad
     */
    public function procesarModificacionDiscapacidad($idDiscapacidad, $nuevosDatosDiscapacidad, $idPeriodoContable, $observacion = null)
    {
        \Log::info("Iniciando proceso de modificación contable para discapacidad ID: {$idDiscapacidad}");
        \Log::info("Nuevos datos de discapacidad: " . json_encode($nuevosDatosDiscapacidad));

        // Obtener asiento vigente
        $historialVigente = $this->obtenerAsientoVigenteDiscapacidad($idDiscapacidad);

        if (!$historialVigente) {
            throw new Exception("No se encontró un asiento contable vigente para esta prestación de discapacidad");
        }

        // Generar contraasiento del asiento original
        $this->generarContraasiento(
            $historialVigente->id_asiento_contable,
            'MODIFICACION',
            $idDiscapacidad,
            'Contraasiento por modificación de prestación de discapacidad'
        );

        // Crear nuevo asiento con los datos actualizados
        $nuevoAsiento = $this->asientoContableRepository->crearAsientoDiscapacidad(
            $nuevosDatosDiscapacidad,
            $idPeriodoContable
        );

        // Guardar en historial el nuevo asiento
        $this->guardarHistorial(
            $idDiscapacidad,
            $nuevoAsiento->id_asiento_contable,
            'ALTA',
            false,
            null,
            $observacion ?? 'Nuevo asiento por modificación de prestación de discapacidad'
        );

        return $nuevoAsiento;
    }

    /**
     * Verificar si una prestación de discapacidad tiene asientos contables
     */
    public function discapacidadTieneAsientos($idDiscapacidad)
    {
        return AsientosDiscapacidadHistorialEntity::where('id_discapacidad', $idDiscapacidad)->exists();
    }

    /**
     * Obtener último asiento de una prestación de discapacidad por tipo de evento
     */
    public function obtenerUltimoAsientoPorTipo($idDiscapacidad, $tipoEvento)
    {
        return AsientosDiscapacidadHistorialEntity::with('asientoContable')
            ->where('id_discapacidad', $idDiscapacidad)
            ->where('tipo_evento', $tipoEvento)
            ->orderBy('fecha_registra', 'desc')
            ->first();
    }

    /**
     * Listar prestaciones de discapacidad con problemas contables
     */
    public function listarDiscapacidadConProblemasContables()
    {
        return DB::select("
            SELECT d.id_discapacidad, d.cuil_beneficiario, d.monto_solicitado, d.fecha_registra,
                   COUNT(h.id_discapacidad_asiento) as total_asientos,
                   SUM(CASE WHEN ac.vigente = 'ACTIVO' THEN 1 ELSE 0 END) as asientos_activos
            FROM tb_discapacidad d
            LEFT JOIN tb_cont_asientos_discapacidad_historial h ON d.id_discapacidad = h.id_discapacidad
            LEFT JOIN tb_cont_asientos_contables ac ON h.id_asiento_contable = ac.id_asiento_contable
            GROUP BY d.id_discapacidad, d.cuil_beneficiario, d.monto_solicitado, d.fecha_registra
            HAVING asientos_activos != 1 OR asientos_activos IS NULL
            ORDER BY d.fecha_registra DESC
        ");
    }
}