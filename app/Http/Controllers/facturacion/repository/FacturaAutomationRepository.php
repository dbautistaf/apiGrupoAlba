<?php

namespace App\Http\Controllers\facturacion\repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacturaAutomationRepository
{
    /**
     * Guarda una factura automatizada completa (cabecera + detalle)
     * 
     * Replica el comportamiento del código manual que NO envía num_liquidacion
     * y deja que el trigger lo genere automáticamente
     */
    public function guardarFacturaAutomatizada(array $cabecera, array $detalle): int
    {
        return DB::transaction(function () use ($cabecera, $detalle) {
            
            // 1. Verificar duplicado por CAE
            $this->verificarDuplicadoPorCAE($cabecera['cae_cai']);
            
            // 2. Verificar duplicado por número/sucursal/proveedor
            $this->verificarDuplicadoPorNumero(
                $cabecera['numero'],
                $cabecera['sucursal'],
                $cabecera['id_proveedor'],
                $cabecera['id_tipo_factura']
            );
            
            // 3. Preparar cabecera SIN num_liquidacion (igual que el código manual)
            // El trigger tg_asignar_numero_liquidacion lo genera automáticamente
            $cabeceraCompleta = array_merge($cabecera, [
                'fecha_registra' => now(),
                'fecha_actualiza' => null,
                'estado' => 1,
                'total_debitado_liquidacion' => 0,
                'total_aprobado_liquidacion' => 0,
                'total_facturado_liquidacion' => $cabecera['total_neto'],
                'estado_pago' => 0,
            ]);
            
            // 4. Insertar cabecera (el trigger asigna num_liquidacion)
            DB::table('tb_facturacion_datos')->insert($cabeceraCompleta);
            $facturaId = DB::getPdo()->lastInsertId();
            
            // 5. Obtener el num_liquidacion generado por el trigger
            $factura = DB::table('tb_facturacion_datos')
                ->select('num_liquidacion')
                ->where('id_factura', $facturaId)
                ->first();
            
            Log::info("Factura automática creada", [
                'id_factura' => $facturaId,
                'num_liquidacion' => $factura->num_liquidacion ?? 'N/A',
                'cae' => $cabecera['cae_cai'],
                'proveedor_id' => $cabecera['id_proveedor'],
                'total' => $cabecera['total_neto']
            ]);

            // 6. Insertar detalle
            $cantidadItems = 0;
            foreach ($detalle as $item) {
                $detalleCompleto = array_merge($item, [
                    'id_factura' => $facturaId,
                ]);
                
                DB::table('tb_facturacion_detalle')->insert($detalleCompleto);
                $cantidadItems++;
            }
            
            Log::info("Detalle insertado: {$cantidadItems} items");
            
            return (int) $facturaId;
        });
    }

    /**
     * Verifica si existe factura con el mismo CAE
     */
    private function verificarDuplicadoPorCAE(string $cae): void
    {
        $existe = DB::table('tb_facturacion_datos')
            ->where('cae_cai', $cae)
            ->exists();
            
        if ($existe) {
            throw new \Exception("Factura duplicada: CAE {$cae} ya existe en el sistema");
        }
    }

    /**
     * Verifica si existe factura con el mismo número/sucursal/proveedor
     */
    private function verificarDuplicadoPorNumero(
        string $numero,
        string $sucursal,
        int $idProveedor,
        int $idTipoFactura
    ): void {
        $existe = DB::table('tb_facturacion_datos')
            ->where('numero', $numero)
            ->where('sucursal', $sucursal)
            ->where('id_proveedor', $idProveedor)
            ->where('id_tipo_factura', $idTipoFactura)
            ->exists();
            
        if ($existe) {
            throw new \Exception(
                "Factura duplicada: N° {$numero} sucursal {$sucursal} del mismo proveedor ya existe"
            );
        }
    }

    /**
     * Busca o crea un proveedor automáticamente
     */
    public function buscarOCrearProveedor(string $cuit, array $datosProveedor): int
    {
        // Buscar por CUIT
        $proveedor = DB::table('tb_proveedor')
            ->where('cuit', $cuit)
            ->first();
        
        if ($proveedor) {
            Log::info("Proveedor encontrado", [
                'cod_proveedor' => $proveedor->cod_proveedor,
                'cuit' => $cuit,
                'razon_social' => $proveedor->razon_social
            ]);
            return $proveedor->cod_proveedor;
        }
        
        // Verificar si está habilitada la creación automática
        if (!config('facturacion.proveedores.crear_automaticamente', true)) {
            throw new \Exception("Proveedor con CUIT {$cuit} no existe y la creación automática está deshabilitada");
        }
        
        // Crear proveedor
        Log::info("📝 Creando nuevo proveedor automáticamente", [
            'cuit' => $cuit,
            'razon_social' => $datosProveedor['razon_social']
        ]);
        
        $codProveedor = DB::table('tb_proveedor')->insertGetId([
            'cuit' => $cuit,
            'razon_social' => $datosProveedor['razon_social'],
            'nombre_fantasia' => $datosProveedor['razon_social'],
            'direccion' => $datosProveedor['domicilio'] ?? '',
            'fecha_alta' => now(),
            'vigente' => config('facturacion.proveedores.estado_default', 1),
            'cod_tipo_impuesto' => config('facturacion.proveedores.cod_tipo_impuesto_default', 1),
            'cod_tipo_iva' => config('facturacion.proveedores.cod_tipo_iva_default', 1),
            'id_proveedor_tipo' => config('facturacion.proveedores.id_proveedor_tipo_default', 1),
            'cod_usuario' => config('facturacion.defaults.cod_usuario_bot', 999),
            'observaciones' => 'Proveedor creado automáticamente desde bot de facturas'
        ]);
        
        Log::info("Proveedor creado", ['cod_proveedor' => $codProveedor]);
        
        return $codProveedor;
    }

    /**
     * Obtiene una factura por ID
     */
    public function obtenerFacturaPorId(int $facturaId): ?object
    {
        return DB::table('tb_facturacion_datos')
            ->where('id_factura', $facturaId)
            ->first();
    }

    /**
     * Obtiene detalle de una factura
     */
    public function obtenerDetallePorFactura(int $facturaId): array
    {
        return DB::table('tb_facturacion_detalle')
            ->where('id_factura', $facturaId)
            ->orderBy('id_detalle')
            ->get()
            ->toArray();
    }

    /**
     * Verifica si existe una factura por CAE
     */
    public function existeFacturaPorCAE(string $cae): bool
    {
        return DB::table('tb_facturacion_datos')
            ->where('cae_cai', $cae)
            ->exists();
    }
}