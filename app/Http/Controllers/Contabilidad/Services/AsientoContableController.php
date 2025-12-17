<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\AsientoContableRepository;
use App\Http\Controllers\Contabilidad\Repository\PeriodosContablesRepository;
use App\Http\Controllers\Tesoreria\Repository\TesCuentasBancariasRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AsientoContableController extends Controller
{

    private $asientoContableRepository;
    private $periodoContableRepositorio;
    private $periodoContableActivo;
    private $tesCuentasBancariasRepository;

    public function __construct(
        PeriodosContablesRepository $periodoContableRepositorio,
        AsientoContableRepository $asientoContableRepository,
        TesCuentasBancariasRepository $tesCuentasBancariasRepository
    ) {
        $this->asientoContableRepository = $asientoContableRepository;
        $this->periodoContableRepositorio = $periodoContableRepositorio;
        $this->periodoContableActivo = $periodoContableRepositorio->findByPeriodoContableActivo();
        $this->tesCuentasBancariasRepository = $tesCuentasBancariasRepository;
    }


    public function getListar(Request $request)
    {
        $data = $this->asientoContableRepository->findByListar($request);
        return response()->json($data);
    }

    public function getBuscarId(Request $request)
    {
        return response()->json($this->asientoContableRepository->findById($request->id));
    }

    public function getEliminarDetalleId(Request $request)
    {
        $this->asientoContableRepository->findByDeleteDetalleId($request->id);
        return response()->json(['message' => 'Registro eliminado correctamente']);
    }

    public function getAnularAsientoContableId(Request $request)
    {
        $this->asientoContableRepository->findByAnularAsientoContableId($request->id, $request->estado);
        return response()->json(['message' => 'Registro anulado correctamente']);
    }

    public function getProcesar(Request $request)
    {
        DB::beginTransaction();
        try {

            if (is_null($request->id_asiento_contable)) {

                $numeroCorrelativo = $this->asientoContableRepository->obtenerSiguienteNumeroAsiento();
                // $idPeriodoActivo = $this->periodoContableActivo->id_periodo_contable;

                $asiento = $this->asientoContableRepository->findByCrearAsiento(
                    $request->id_tipo_asiento,
                    $request->asiento_modelo,
                    $request->asiento_leyenda,
                    $numeroCorrelativo,
                    $request->id_periodo_contable,
                    $request->numero_referencia,
                    $request->vigente
                );

                foreach ($request->detalle as $key) {
                    // Verificar si existe una cuenta bancaria asociada a esta cuenta contable
                    //hacer nuevo front que envie un campo especial con la cuenta bancaria a buscar,si no hay le devolvemos como que si 
                    //o si necesita generar una, 
                    $cuentaBancaria = $this->asientoContableRepository->obtenerCuentaBancariaPorPlanContable($key['id_detalle_plan']);

                    // Si hay cuenta bancaria, verificar que tenga cuenta contable asociada
                    if ($cuentaBancaria) {
                        if (!$this->asientoContableRepository->verificarCuentaBancariaTieneCuentaContable($cuentaBancaria->id_cuenta_bancaria)) {
                            DB::rollBack();
                            return response()->json([
                                'error_type' => 'BANK_ACCOUNT_NO_ACCOUNTING_ASSOCIATION',
                                'message' => 'La cuenta bancaria asociada a esta cuenta contable no tiene una relación configurada. Por favor, configure la asociación antes de continuar.',
                                'id_cuenta_bancaria' => $cuentaBancaria->id_cuenta_bancaria,
                                'numero_cuenta' => $cuentaBancaria->numero_cuenta ?? 'N/A',
                                'id_detalle_plan' => $key['id_detalle_plan']
                            ], 400);
                        }
                    }

                    $this->asientoContableRepository->findByCrearDetalleAsiento(
                        [
                            "id_asiento_contable" => $asiento->id_asiento_contable,
                            "id_proveedor_cuenta_contable" => $key['id_proveedor_cuenta_contable'] ?? null,
                            "id_forma_pago_cuenta_contable" => $key['id_forma_pago_cuenta_contable'] ?? null,
                            "id_familia_cuenta_contable" => $key['id_familia_cuenta_contable'] ?? null,
                            "id_cuenta_bancaria_cuenta_contable" => $key['id_cuenta_bancaria_cuenta_contable'] ?? null,
                            "monto_debe" => $key['monto_debe'],
                            "monto_haber" => $key['monto_haber'],
                            "observaciones" => $key['observaciones'],
                            "id_detalle_plan" => $key['id_detalle_plan'],
                            "asiento_modelo" => $request['asiento_modelo']
                        ]
                    );
                    //=========================== 
                    // NUEVO: Manejo especial para asientos tipo 4 (Manual) y que impactan en Fisca
                    //=========================== 

                    if ($request->id_tipo_asiento == '4' && $key['monto_haber'] > 0 && $request['asiento_modelo'] != 'ASIENTO MANUAL') {
                        if ($cuentaBancaria) {
                            // Verificar fondos suficientes para el egreso
                            $montoEgreso = $key['monto_haber'];
                            if (!$this->tesCuentasBancariasRepository->findByVerificarSaldoCuenta($cuentaBancaria->id_cuenta_bancaria, $montoEgreso)) {
                                DB::rollBack();
                                return response()->json([
                                    'error_type' => 'INSUFFICIENT_FUNDS_MANUAL',
                                    'message' => 'Fondos insuficientes en la cuenta bancaria para el asiento manual',
                                    'cuenta_bancaria' => $cuentaBancaria->numero_cuenta ?? 'N/A',
                                    'plan_cuenta' => $key['plan_cuenta'] ?? 'N/A',
                                    'monto_requerido' => $montoEgreso
                                ], 400);
                            }

                            // Realizar el retiro de la cuenta
                            $this->tesCuentasBancariasRepository->findByRetiroCuenta($cuentaBancaria->id_cuenta_bancaria, $montoEgreso);

                            // Registrar el movimiento
                            $this->tesCuentasBancariasRepository->findByRegistrarMovimiento(
                                $cuentaBancaria->id_cuenta_bancaria,
                                $montoEgreso,
                                'EGRESO',
                                null,
                                $asiento->id_asiento_contable,
                                'ASIENTO MANUAL - ' . $request->asiento_modelo . ' - ' . $request->asiento_leyenda
                            );
                        } else {
                            // No hay cuenta bancaria asociada, devolver aviso
                            DB::rollBack();
                            return response()->json([
                                'error_type' => 'NO_BANK_ACCOUNT_ASSOCIATED',
                                'message' => 'La cuenta contable no tiene una cuenta bancaria asociada para realizar el movimiento de tesorería',
                                'plan_cuenta' => $key['plan_cuenta'] ?? 'N/A',
                                'codigo_cuenta' => $key['codigo_cuenta'] ?? 'N/A',
                                'id_detalle_plan' => $key['id_detalle_plan'],
                                'monto_haber' => $key['monto_haber']
                            ], 400);
                        }
                    }

                    if ($cuentaBancaria && $request->id_tipo_asiento != '4') {
                        // Solo procesar movimientos de tesorería si existe la asociación
                        try {
                            // Registrar movimiento en tesorería
                            $monto = $key['monto_haber'] > 0 ? $key['monto_haber'] : $key['monto_debe'];
                            $tipoMovimiento = $key['monto_haber'] > 0 ? 'EGRESO' : 'INGRESO';

                            // Verificar fondos suficientes para egresos
                            if ($tipoMovimiento === 'EGRESO') {
                                if (!$this->tesCuentasBancariasRepository->findByVerificarSaldoCuenta($cuentaBancaria->id_cuenta_bancaria, $monto)) {
                                    throw new \Exception("INSUFFICIENT_FUNDS");
                                }
                                $this->tesCuentasBancariasRepository->findByRetiroCuenta($cuentaBancaria->id_cuenta_bancaria, $monto);
                            } else {
                                $this->tesCuentasBancariasRepository->findByDepositoCuenta($cuentaBancaria->id_cuenta_bancaria, $monto);
                            }

                            // Registrar el movimiento
                            $this->tesCuentasBancariasRepository->findByRegistrarMovimiento(
                                $cuentaBancaria->id_cuenta_bancaria,
                                $monto,
                                $tipoMovimiento,
                                null,
                                $asiento->id_asiento_contable,
                                'ASIENTO MANUAL - ' . $request->asiento_modelo . ' - ' . $request->asiento_leyenda
                            );
                        } catch (\Exception $e) {
                            if ($e->getMessage() === "INSUFFICIENT_FUNDS") {
                                DB::rollBack();
                                return response()->json([
                                    'error_type' => 'INSUFFICIENT_FUNDS',
                                    'message' => 'Fondos insuficientes en la cuenta bancaria para realizar el egreso',
                                    'cuenta_bancaria' => $cuentaBancaria->numero_cuenta ?? 'N/A',
                                    'monto_requerido' => $monto
                                ], 400);
                            }
                            throw $e;
                        }
                    }
                }

                //@CONTRAASIENTO
                if (!is_null($request->numero_referencia)) {
                    try {
                        $this->asientoContableRepository->findByContraAsientoContableId($request->numero_referencia, $numeroCorrelativo, 'CONTRAASIENTO');
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'error_type' => 'CONTRAASIENTO_ERROR',
                            'message' => 'Error al procesar el contraasiento',
                            'numero_referencia' => $request->numero_referencia
                        ], 400);
                    }
                }

                DB::commit();
                return response()->json(['message' => 'Registro procesado correctamente'], 200);
            } else {
                // Validar que el asiento existe
                $asientoExistente = $this->asientoContableRepository->findById($request->id_asiento_contable);
                if (!$asientoExistente) {
                    return response()->json([
                        'error_type' => 'ASIENTO_NOT_FOUND',
                        'message' => 'El asiento contable no existe',
                        'id_asiento' => $request->id_asiento_contable
                    ], 404);
                }

                $this->asientoContableRepository->findByUpdateAsiento(
                    $request->id_tipo_asiento,
                    $request->fecha_asiento,
                    $request->asiento_modelo,
                    $request->asiento_leyenda,
                    $request->numero,
                    $request->id_periodo_contable,
                    $request->numero_referencia,
                    $request->asiento_observaciones,
                    $request->id_asiento_contable
                );

                foreach ($request->detalle as $key) {
                    $this->asientoContableRepository->findByUpdateDetalleItemAsiento(
                        [
                            "id_asiento_contable" => $key['id_asiento_contable'],
                            "id_proveedor_cuenta_contable" => $key['id_proveedor_cuenta_contable'] ?? null,
                            "id_forma_pago_cuenta_contable" => $key['id_forma_pago_cuenta_contable'] ?? null,
                            "id_familia_cuenta_contable" => $key['id_familia_cuenta_contable'] ?? null,
                            "id_cuenta_bancaria_cuenta_contable" => $key['id_cuenta_bancaria_cuenta_contable'] ?? null,
                            "monto_debe" => $key['monto_debe'],
                            "monto_haber" => $key['monto_haber'],
                            "observaciones" => $key['observaciones'],
                            "id_detalle_plan" => $key['id_detalle_plan']
                        ],
                        $key['id_asiento_contable_detalle']
                    );
                }
                DB::commit();
                return response()->json(['message' => 'Registro modificado correctamente'], 200);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_type' => 'DATABASE_ERROR',
                'message' => 'Error en la base de datos',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error_type' => 'GENERAL_ERROR',
                'message' => 'Error interno del servidor',
                'details' => $th->getMessage()
            ], 500);
        }
    }


    //Metodos Franco -> asientos en caso de facturas y pagos
    public function asientoFactura(Request $request)
    {
        DB::beginTransaction();
        try {

            // Crear asiento contable automático
            $datosFactura = [
                'id_proveedor' => $request->id_proveedor,
                'cuit_proveedor' => $request->cuit_proveedor,
                'nombre_proveedor' => $request->nombre_proveedor,
                'numero_factura' => $request->numero_factura,
                'total_factura' => $request->total_factura,
                'id_cuenta_gasto' => $request->id_cuenta_gasto // Cuenta donde se imputa el gasto--revisar
            ];

            $this->asientoContableRepository->crearAsientoFactura($datosFactura, $this->periodoContableActivo->id_periodo_contable);

            DB::commit();
            return response()->json(['message' => 'Factura procesada con asiento contable'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function asientoPago(Request $request)
    {
        DB::beginTransaction();
        try {

            // Crear asiento contable automático
            $datosPago = [
                'id_proveedor' => $request->id_proveedor,
                'cuit_proveedor' => $request->cuit_proveedor,
                'nombre_proveedor' => $request->nombre_proveedor,
                'numero_pago' => $request->numero_pago,
                'monto_pago' => $request->monto_pago,
                'id_metodo_pago' => $request->id_metodo_pago
            ];

            $this->asientoContableRepository->crearAsientoPago($datosPago, $this->periodoContableActivo->id_periodo_contable);

            DB::commit();
            return response()->json(['message' => 'Pago procesado con asiento contable'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
