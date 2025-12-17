<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\BancoCuentasContableEntity;
use App\Models\Contabilidad\DetalleAsientosContablesEntity;
use App\Models\Contabilidad\FamiliaCuentaContableEntity;
use App\Models\Contabilidad\ImputacionesCuentaContableEntity;
use App\Models\Contabilidad\ProveedorCuentaContableEntity;
use App\Models\Contabilidad\FormasPagoCuentasContableEntity;
use App\Models\Tesoreria\TesCuentasBancariasEntity;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AsientoContableRepository
{

    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now();
    }

    public function findByCrearAsiento($id_tipo_asiento, $asiento_modelo, $asiento_leyenda, $numero, $id_periodo_contable, $numero_referencia, $vigente)
    {
        return AsientosContablesEntity::create([
            'id_tipo_asiento' => $id_tipo_asiento,
            'fecha_asiento' => now()->toDateString(),
            'asiento_modelo' => $asiento_modelo,
            'asiento_leyenda' => $asiento_leyenda,
            'numero' => $numero,
            'numero_referencia' => $numero_referencia,
            'id_periodo_contable' => $id_periodo_contable,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'vigente' => $vigente
        ]);
    }

    public function findByCrearDetalleAsiento($params)
    {
        return DetalleAsientosContablesEntity::create([
            'id_asiento_contable' => $params['id_asiento_contable'],
            'id_proveedor_cuenta_contable' => $params['id_proveedor_cuenta_contable'] ?? null,
            'id_forma_pago_cuenta_contable' => $params['id_forma_pago_cuenta_contable'] ?? null,
            'id_familia_cuenta_contable' => $params['id_familia_cuenta_contable'] ?? null,
            'id_cuenta_bancaria_cuenta_contable' => $params['id_cuenta_bancaria_cuenta_contable'] ?? null,
            'monto_debe' => $params['monto_debe'],
            'monto_haber' => $params['monto_haber'],
            'observaciones' => $params['observaciones'],
            'id_detalle_plan' => $params['id_detalle_plan'],
            'recursor' => (int) $params['monto_debe'] > 0 ? '1' : '0'
        ]);
    }

    public function findByUpdateAsiento(
        $id_tipo_asiento,
        $fecha_asiento,
        $asiento_modelo,
        $asiento_leyenda,
        $numero,
        $id_periodo_contable,
        $numero_referencia,
        $asiento_observaciones,
        $idAsiento
    ) {
        $asiento = AsientosContablesEntity::find($idAsiento);
        $asiento->id_tipo_asiento = $id_tipo_asiento;
        $asiento->fecha_asiento = $fecha_asiento;
        $asiento->asiento_modelo = $asiento_modelo;
        $asiento->asiento_leyenda = $asiento_leyenda;
        $asiento->numero = $numero;
        $asiento->numero_referencia = $numero_referencia;
        $asiento->asiento_observaciones = $asiento_observaciones;
        $asiento->id_periodo_contable = $id_periodo_contable;
        $asiento->cod_usuario_modifica = $this->user->cod_usuario;
        $asiento->fecha_modifica = $this->fechaActual;
        return $asiento->update();
    }

    public function findByUpdateDetalleItemAsiento($params, $id)
    {
        $item = DetalleAsientosContablesEntity::find($id);
        $item->id_asiento_contable = $params['id_asiento_contable'];
        $item->id_proveedor_cuenta_contable = $params['id_proveedor_cuenta_contable'] ?? null;
        $item->id_forma_pago_cuenta_contable = $params['id_forma_pago_cuenta_contable'];
        $item->id_familia_cuenta_contable = $params['id_familia_cuenta_contable'] ?? null;
        $item->id_cuenta_bancaria_cuenta_contable = $params['id_cuenta_bancaria_cuenta_contable'] ?? null;
        $item->monto_debe = $params['monto_debe'];
        $item->monto_haber = $params['monto_haber'];
        $item->observaciones = $params['observaciones'];
        $item->id_detalle_plan = $params['id_detalle_plan'];
        return $item->update();
    }

    public function findByListar($params)
    {
        $query = AsientosContablesEntity::with(['tipo']);

        if (!is_null($params->id_periodo_contable)) {
            $query->where('id_periodo_contable', [$params->id_periodo_contable]);
        }

        if (!is_null($params->numero)) {
            $query->where('numero', 'LIKE', "%$params->numero");
        }

        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $query->whereBetween('fecha_asiento', [$params->desde, $params->hasta]);
        }

        if (!is_null($params->leyenda)) {
            $query->where('asiento_leyenda', 'LIKE', "%$params->leyenda%");
        }

        $query->orderByDesc('numero');

        return $query->get();
    }

    public function findById($id)
    {
        return AsientosContablesEntity::with(['detalle', 'detalle.planCuenta'])
            ->find($id);
    }

    public function findByDeleteDetalleId($id)
    {
        return DetalleAsientosContablesEntity::find($id)->delete();
    }

    public function findByAnularAsientoContableId($id, $vigente)
    {
        $asiento = AsientosContablesEntity::find($id);
        $asiento->vigente = $vigente;
        return $asiento->update();
    }

    public function findByContraAsientoContableId($numero, $numero_referencia, $vigente)
    {
        $asiento = AsientosContablesEntity::where('numero', [$numero])->first();
        $asiento->numero_referencia = $numero_referencia;
        $asiento->vigente = $vigente;
        return $asiento->update();
    }

    //Metodos Franco

    public function verificarProveedorTieneCuentaContable($idProveedor)
    {
        return ProveedorCuentaContableEntity::where('id_proveedor', $idProveedor)
            ->where('vigente', 1)
            ->exists();
    }

    public function verificarMetodoPagoTieneCuentaContable($idMetodoPago)
    {
        return FormasPagoCuentasContableEntity::where('id_forma_pago', $idMetodoPago)
            ->where('vigente', 1)
            ->exists();
    }
    public function verificarFamiliaTieneCuentaContable($idFamilia)
    {
        return FamiliaCuentaContableEntity::where('id_tipo_factura', $idFamilia)
            ->where('vigente', 1)
            ->exists();
    }
    public function verificarCuentaBancariaTieneCuentaContable($idCuentaBancaria)
    {
        // Verificar si existe una relación en la tabla de banco-cuentas contables
        return BancoCuentasContableEntity::where('id_cuenta_bancaria', $idCuentaBancaria)
            ->where('vigente', '1')
            ->exists();
    }

    public function obtenerCuentaContableProveedor($idProveedor)
    {
        return ProveedorCuentaContableEntity::where('id_proveedor', $idProveedor)
            ->where('vigente', 1)
            ->first();
    }

    public function obtenerCuentaContableMetodoPago($idMetodoPago)
    {
        return FormasPagoCuentasContableEntity::where('id_forma_pago', $idMetodoPago)
            ->where('vigente', 1)
            ->first();
    }
    public function obtenerCuentaContableFamilia($idFamilia)
    {
        return FamiliaCuentaContableEntity::where('id_tipo_factura', $idFamilia)
            ->where('vigente', 1)
            ->first();
    }
    public function obtenerCuentaContableByCuentaBancaria($idCuentaBancaria)
    {
        return BancoCuentasContableEntity::where('id_cuenta_bancaria', $idCuentaBancaria)
            ->where('vigente', '1')
            ->first();
    }

    /**
     * Obtener cuenta bancaria asociada a una cuenta contable
     */
    public function obtenerCuentaBancariaPorPlanContable($idDetallePlan)
    {
        return BancoCuentasContableEntity::where('id_detalle_plan', $idDetallePlan)
            ->where('vigente', '1')
            ->first();
    }

    /**
     * Obtener cuenta bancaria asociada a una cuenta contable
     */
    public function obtenerImputacionPorPlanContable($idDetallePlan)
    {
        return ImputacionesCuentaContableEntity::where('id_detalle_plan', $idDetallePlan)
            ->where('vigente', '1')
            ->first();
    }

    //Obtener cuenta contable de imputacion con el id de imputacion
    public function obtenerCuentaContableImputacion($idImputacionHaber)
    {
        return ImputacionesCuentaContableEntity::where('id_imputacion_cuenta_contable', $idImputacionHaber)
            ->where('vigente', '1')
            ->first();
    }

    public function obtenerSiguienteNumeroAsiento()
    {
        $ultimoAsiento = AsientosContablesEntity::orderBy('numero', 'desc')->first();
        return $ultimoAsiento ? $ultimoAsiento->numero + 1 : 1;
    }

    /**
     * Crear un asiento contable para una factura
     * Etapa 1: Verificar que el proveedor tenga una cuenta contable asignada
     * Etapa 2: Crear el asiento contable con los detalles correspondientes (Verificar Debe id_detalle_plan)
     * Asiento creado para proveedores ok
     */
    public function crearAsientoFactura($datosFactura, $idPeriodoContable)
    {
        // Las validaciones ya se realizaron en el controlador de facturación
        // Solo obtenemos la cuenta del proveedor
        // $cuentaProveedor = $this->obtenerCuentaContableProveedor($datosFactura['id_proveedor']);
        // $cuentaFamilia = $this->obtenerCuentaContableFamilia($datosFactura['id_tipo_factura']);
        $detalleImputaciones = $datosFactura['ImputacionDebe']; // Ensure 'ImputacionDebe' exists, default to an empty array
        // $ImputacionDebe = $this->obtenerCuentaContableImputacion($detalleImputaciones['idImputacionDebe']);
        $ImputacionHaber = $this->obtenerCuentaContableImputacion($datosFactura['idImputacionHaber']);

        // Crear leyenda: CUIT - NOMBRE - NUMERO_FACTURA
        $leyenda = $datosFactura['cuit'] . ' - ' .
            $datosFactura['nombre'] . ' - ' .
            $datosFactura['numero_factura'];

        // Obtener siguiente número correlativo
        $numeroCorrelativo = $this->obtenerSiguienteNumeroAsiento();

        // Crear asiento (sin numero_referencia para facturas normales)
        $asiento = $this->findByCrearAsiento(
            1, // ID tipo asiento para facturas (ajustar según tu configuración)
            'FACTURA',
            $leyenda,
            $numeroCorrelativo,
            $idPeriodoContable,
            null, // numero_referencia como null para facturas normales
            'ACTIVO'
        );

        // Iterar sobre las imputaciones para llenar el debe
        foreach ($detalleImputaciones as $imputacion) {
            $ImputacionDebe = $this->obtenerCuentaContableImputacion($imputacion['idImputacionDebe']);
            $this->findByCrearDetalleAsiento([
                'id_asiento_contable' => $asiento->id_asiento_contable,
                'id_forma_pago_cuenta_contable' => null,
                'id_familia_cuenta_contable' => null,
                'monto_debe' => $imputacion['totalImporteDebe'], // Use array syntax
                'monto_haber' => 0,
                'observaciones' => 'Registro de factura',
                'id_detalle_plan' => $ImputacionDebe['id_detalle_plan'] // Use array syntax
            ]);
        }

        // Crear el haber
        $this->findByCrearDetalleAsiento([
            'id_asiento_contable' => $asiento->id_asiento_contable,
            'id_proveedor_cuenta_contable' => null,
            'id_forma_pago_cuenta_contable' => null,
            'id_familia_cuenta_contable' => null,
            'monto_debe' => 0,
            'monto_haber' => $datosFactura['total_factura'],
            'observaciones' => 'Cuenta por pagar proveedor',
            'id_detalle_plan' => $ImputacionHaber['id_detalle_plan']
        ]);

        return $asiento;
    }

    public function crearAsientoPago($datosPago, $idPeriodoContable)
    {
        // Las validaciones ya se realizaron en el controlador de pagos
        // Solo obtenemos las cuentas necesarias
        // $cuentaProveedor = $this->obtenerCuentaContableProveedor($datosPago['id']);
        // $cuentaMetodoPago = $this->obtenerCuentaContableMetodoPago($datosPago['id_metodo_pago']);

        $ImputacionDebe = $this->obtenerCuentaContableImputacion($datosPago['idImputacionDebe']);
        $ImputacionHaber = $datosPago['ImputacionHaber']; // objeto 
        // Obtener cuentas contables
        $cuentaOrigen = $this->obtenerCuentaContableByCuentaBancaria($datosPago['id_cuenta_bancaria']);


        // Crear leyenda: CUIT - NOMBRE - NUMERO_PAGO
        $leyenda = $datosPago['cuit'] . ' - ' .
            $datosPago['nombre'] . ' - ' .
            $datosPago['numero_pago'];

        // Obtener siguiente número correlativo
        $numeroCorrelativo = $this->obtenerSiguienteNumeroAsiento();

        // Crear asiento (sin numero_referencia para pagos normales)
        $asiento = $this->findByCrearAsiento(
            1, // ID tipo asiento para pagos (ajustar según tu configuración)
            'PAGO',
            $leyenda,
            $numeroCorrelativo,
            $idPeriodoContable,
            null, // numero_referencia como null para pagos normales
            'ACTIVO'
        );

        // Crear detalle del asiento
        // DEBE: Cuenta del proveedor (disminuye el pasivo)
        $this->findByCrearDetalleAsiento([
            'id_asiento_contable' => $asiento->id_asiento_contable,
            'id_proveedor_cuenta_contable' => null,
            'id_forma_pago_cuenta_contable' => null,
            'id_familia_cuenta_contable' => null,
            'monto_debe' => $ImputacionHaber['totalImporteHaber'],
            'monto_haber' => 0,
            'observaciones' => 'Pago a proveedor/prestador',
            'id_detalle_plan' => $ImputacionDebe['id_detalle_plan']
        ]);

        // HABER: Cuenta del método de pago (caja, banco, etc.)
        // $consultaCuentaHaber = $this->obtenerCuentaContableImputacion($ImputacionHaber['idImputacionHaber']);
        $this->findByCrearDetalleAsiento([
            'id_asiento_contable' => $asiento->id_asiento_contable,
            'id_proveedor_cuenta_contable' => null,
            'id_forma_pago_cuenta_contable' => null,
            'id_familia_cuenta_contable' => null,
            'monto_debe' => 0,
            'monto_haber' => $ImputacionHaber['totalImporteHaber'],
            'observaciones' => 'Salida de fondos',
            // 'id_detalle_plan' => $consultaCuentaHaber['id_detalle_plan']
            'id_detalle_plan' => $cuentaOrigen['id_detalle_plan']
        ]);


        return $asiento;
    }


    public function crearAsientoTransaccion($datosTransaccion, $idPeriodoContable)
    {
        // Mapeo de tipo de transacción
        // (Podés ajustar los IDs según tu configuración real)
        $tipos = [
            1 => 'INGRESO',
            2 => 'EGRESO',
            3 => 'MOVIMIENTO ENTRE CUENTAS'
        ];

        $tipoId = $datosTransaccion['id_tipo_transaccion'];
        $tipo = isset($tipos[$tipoId]) ? $tipos[$tipoId] : 'DESCONOCIDO';

        // Obtener cuentas contables
        $cuentaOrigen = $this->obtenerCuentaContableByCuentaBancaria($datosTransaccion['id_cuenta_bancaria']);
        $cuentaDestino = null;

        if (!empty($datosTransaccion['id_cuenta_bancaria_destino'])) {
            $cuentaDestino = $this->obtenerCuentaContableByCuentaBancaria($datosTransaccion['id_cuenta_bancaria_destino']);
        }

        $montoOperacion = (float) $datosTransaccion['monto_operacion'];
        $montoRetencion = (float) $datosTransaccion['monto_retencion'];
        $montoTotal = $montoOperacion - $montoRetencion;

        // Leyenda descriptiva
        $leyenda = strtoupper($tipo) . ' - ' .
            'Cuenta Origen: ' . $datosTransaccion['id_cuenta_bancaria'] .
            (!empty($datosTransaccion['id_cuenta_bancaria_destino']) ?
                ' → Destino: ' . $datosTransaccion['id_cuenta_bancaria_destino'] : '') .
            ' | Monto: $' . number_format($montoOperacion, 2) .
            (!empty($datosTransaccion['num_factura']) ? ' | Factura: ' . $datosTransaccion['num_factura'] : '');

        // Obtener número correlativo
        $numeroCorrelativo = $this->obtenerSiguienteNumeroAsiento();

        // Crear asiento principal
        $asiento = $this->findByCrearAsiento(
            id_tipo_asiento: 1, // ID del tipo de asiento
            asiento_modelo: $tipo,
            asiento_leyenda: $leyenda,
            numero: $numeroCorrelativo,
            id_periodo_contable: $idPeriodoContable,
            numero_referencia: $datosTransaccion['id_operacion'],
            vigente: 'ACTIVO'
        );

        // === CREACIÓN DE DETALLES ===
        switch ($tipo) {
            case 'INGRESO':
                // DEBE: Caja/Banco (entra dinero)
                $this->findByCrearDetalleAsiento([
                    'id_asiento_contable' => $asiento->id_asiento_contable,
                    'monto_debe' => $montoTotal,
                    'monto_haber' => 0,
                    'observaciones' => 'Ingreso de fondos',
                    'id_detalle_plan' => $cuentaOrigen['id_detalle_plan']
                ]);

                // HABER: Cuenta de ingresos o contraparte
                if ($cuentaDestino) {
                    $this->findByCrearDetalleAsiento([
                        'id_asiento_contable' => $asiento->id_asiento_contable,
                        'monto_debe' => 0,
                        'monto_haber' => $montoTotal,
                        'observaciones' => 'Reconocimiento de ingreso',
                        'id_detalle_plan' => $cuentaDestino['id_detalle_plan']
                    ]);
                }
                break;

            case 'EGRESO':
                // DEBE: Gasto o proveedor
                if ($cuentaDestino) {
                    $this->findByCrearDetalleAsiento([
                        'id_asiento_contable' => $asiento->id_asiento_contable,
                        'monto_debe' => $montoTotal,
                        'monto_haber' => 0,
                        'observaciones' => 'Registro de egreso',
                        'id_detalle_plan' => $cuentaDestino['id_detalle_plan']
                    ]);
                }

                // HABER: Caja o Banco (sale dinero)
                $this->findByCrearDetalleAsiento([
                    'id_asiento_contable' => $asiento->id_asiento_contable,
                    'monto_debe' => 0,
                    'monto_haber' => $montoTotal,
                    'observaciones' => 'Salida de fondos',
                    'id_detalle_plan' => $cuentaOrigen['id_detalle_plan']
                ]);
                break;

            case 'MOVIMIENTO ENTRE CUENTAS':
                if (!$cuentaDestino) {
                    throw new Exception('Debe indicar la cuenta destino para un movimiento entre cuentas.');
                }

                // DEBE: Cuenta destino (recibe dinero)
                $this->findByCrearDetalleAsiento([
                    'id_asiento_contable' => $asiento->id_asiento_contable,
                    'monto_debe' => $montoTotal,
                    'monto_haber' => 0,
                    'observaciones' => 'Transferencia recibida',
                    'id_detalle_plan' => $cuentaDestino['id_detalle_plan']
                ]);

                // HABER: Cuenta origen (sale dinero)
                $this->findByCrearDetalleAsiento([
                    'id_asiento_contable' => $asiento->id_asiento_contable,
                    'monto_debe' => 0,
                    'monto_haber' => $montoTotal,
                    'observaciones' => 'Transferencia emitida',
                    'id_detalle_plan' => $cuentaOrigen['id_detalle_plan']
                ]);
                break;
        }

        return $asiento;
    }


}