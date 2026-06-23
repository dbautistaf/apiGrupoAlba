<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\AsientosReintegrosHistorialEntity;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsientosReintegrosHistorialRepository
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
     * 1. Obtener asiento vigente de un reintegro
     */
    public function obtenerAsientoVigenteReintegro($idReintegro)
    {
        return AsientosReintegrosHistorialEntity::with(['asientoContable', 'asientoContable.detalle'])
            ->where('id_reintegro', $idReintegro)
            ->where('tipo_evento', 'ALTA')
            ->where('es_contraasiento', false)
            ->whereHas('asientoContable', function ($query) {
                $query->where('vigente', 'ACTIVO');
            })
            ->first();
    }

    /**
     * 2. Generar contraasiento para anulación/modificación
     */
    public function generarContraasiento($idAsientoOriginal, $tipoEvento, $idReintegro, $observacion = null)
    {
        DB::beginTransaction();
        try {
            // Obtener el asiento original con sus detalles
            $asientoOriginal = AsientosContablesEntity::with('detalle')->find($idAsientoOriginal);

            if (!$asientoOriginal) {
                throw new Exception("No se encontró el asiento contable original");
            }

            // Obtener siguiente número correlativo
            $numeroCorrelativo = $this->asientoContableRepository->obtenerSiguienteNumeroAsiento();

            // Crear contraasiento con valores invertidos
            $contraasiento = $this->asientoContableRepository->findByCrearAsiento(
                $asientoOriginal->id_tipo_asiento,
                'CONTRAASIENTO - ' . $asientoOriginal->asiento_modelo,
                'CONTRAASIENTO - ' . $asientoOriginal->asiento_leyenda . ' - ' . strtoupper($tipoEvento),
                $numeroCorrelativo,
                $asientoOriginal->id_periodo_contable,
                $asientoOriginal->numero, // Referencia al asiento original
                'ACTIVO',
                $asientoOriginal->id_razon
            );

            // Crear detalles del contraasiento con valores invertidos
            foreach ($asientoOriginal->detalle as $detalleOriginal) {
                $this->asientoContableRepository->findByCrearDetalleAsiento([
                    'id_asiento_contable' => $contraasiento->id_asiento_contable,
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

            // Cambiar estado del asiento original a CONTRAASIENTO (no ANULADO)
            $this->asientoContableRepository->findByAnularAsientoContableId($idAsientoOriginal, 'CONTRAASIENTO');

            // Guardar en historial
            $this->guardarHistorial(
                $idReintegro,
                $contraasiento->id_asiento_contable,
                $tipoEvento,
                true, // es_contraasiento
                $idAsientoOriginal,
                $observacion ?? "Contraasiento generado por $tipoEvento de reintegro"
            );

            DB::commit();
            return $contraasiento;

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al generar contraasiento: " . $e->getMessage());
        }
    }

    /**
     * 3. Guardar historial de asiento de reintegro
     */
    public function guardarHistorial($idReintegro, $idAsientoContable, $tipoEvento, $esContraasiento = false, $idAsientoOrigen = null, $observacion = null)
    {
        return AsientosReintegrosHistorialEntity::create([
            'id_reintegro' => $idReintegro,
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
     * 4. Procesar anulación de reintegro
     */
    public function procesarAnulacionReintegro($idReintegro, $observacion = null)
    {
        DB::beginTransaction();
        try {
            // Obtener asiento vigente del reintegro
            $asientoVigente = $this->obtenerAsientoVigenteReintegro($idReintegro);

            if (!$asientoVigente) {
                throw new Exception("No se encontró asiento vigente para el reintegro");
            }

            // Generar contraasiento
            $contraasiento = $this->generarContraasiento(
                $asientoVigente->id_asiento_contable,
                'ANULACION',
                $idReintegro,
                $observacion ?? 'Reintegro anulado por el usuario'
            );

            DB::commit();
            return $contraasiento;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 5. Procesar modificación de reintegro (contraasiento + nuevo asiento)
     */
    public function procesarModificacionReintegro($idReintegro, $nuevoDatosReintegro, $idPeriodoContable, $observacion = null)
    {
        DB::beginTransaction();
        try {
            // Obtener asiento vigente
            $asientoVigente = $this->obtenerAsientoVigenteReintegro($idReintegro);

            if ($asientoVigente) {
                // Generar contraasiento del asiento anterior
                $this->generarContraasiento(
                    $asientoVigente->id_asiento_contable,
                    'MODIFICACION',
                    $idReintegro,
                    $observacion ?? 'Modificación de reintegro'
                );
            }

            // Crear nuevo asiento con los datos actualizados
            $nuevoAsiento = $this->asientoContableRepository->crearAsientoReintegro(
                $nuevoDatosReintegro,
                $idPeriodoContable
            );

            // Guardar en historial el nuevo asiento
            $this->guardarHistorial(
                $idReintegro,
                $nuevoAsiento->id_asiento_contable,
                'ALTA',
                false,
                null,
                $observacion ?? 'Nuevo asiento por modificación de reintegro'
            );

            DB::commit();
            return $nuevoAsiento;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Verificar si un reintegro tiene asientos contables
     */
    public function reintegroTieneAsientos($idReintegro)
    {
        return AsientosReintegrosHistorialEntity::where('id_reintegro', $idReintegro)->exists();
    }

    /**
     * Obtener todos los asientos de un reintegro (incluyendo contraasientos)
     */
    public function obtenerHistorialCompleto($idReintegro)
    {
        return AsientosReintegrosHistorialEntity::with(['asientoContable', 'asientoContable.detalle', 'asientoOrigen'])
            ->where('id_reintegro', $idReintegro)
            ->orderBy('fecha_registra', 'desc')
            ->get();
    }
}