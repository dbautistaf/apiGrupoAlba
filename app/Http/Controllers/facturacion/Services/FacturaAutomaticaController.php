<?php

namespace App\Http\Controllers\facturacion\Services;

use App\Http\Controllers\facturacion\repository\FacturaAutomationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FacturaAutomaticaController
{
    public function __construct(
        private FacturaAutomationRepository $facturaRepo
    ) {}

    /**
     * Recibe y procesa factura desde el bot
     * 
     * POST /api/facturacion/automation
     */
    public function recibirFactura(Request $request): JsonResponse
    {
        try {
            Log::info('🤖 Bot envió factura', [
                'content_type' => $request->header('Content-Type'),
                'ip' => $request->ip()
            ]);
            
            // 1. Validar estructura JSON
            $validated = $this->validarRequest($request);
            
            // 2. Validar que sea para Alba
            $this->validarCuitReceptor($validated['CUIT_DNI_receptor']);
            
            // 3. Transformar JSON del bot → formato Alba
            $datosAlba = $this->transformarBotAAlba($validated);

            // 4. Guardar en BD
            $facturaId = $this->facturaRepo->guardarFacturaAutomatizada(
                $datosAlba['cabecera'], 
                $datosAlba['detalle']
            );

            Log::info('✅ Factura automática procesada exitosamente', [
                'id_factura' => $facturaId,
                'cae' => $validated['CAE'],
                'proveedor_cuit' => $validated['CUIT_emisor']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Factura procesada exitosamente',
                'data' => [
                    'id_factura' => $facturaId,
                    'numero_completo' => sprintf(
                        '%s-%s-%s',
                        $validated['Tipo_de_factura'],
                        str_pad($validated['Punto_de_venta'], 5, '0', STR_PAD_LEFT),
                        $validated['Comp_nro']
                    ),
                    'cae' => $validated['CAE'],
                    'proveedor' => $validated['Razon_social'],
                    'total' => $validated['Importe_total']
                ]
            ], 201);

        } catch (ValidationException $e) {
            Log::warning('Validación fallida', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error procesando factura automática', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valida el JSON que envía el bot
     */
    private function validarRequest(Request $request): array
    {
        $data = $request->json()->all();
        
        $validator = Validator::make($data, [
            // Identificación del emisor
            'Tipo_de_factura' => 'required|string|in:A,B,C,E',
            'Razon_social' => 'required|string|max:200',
            'Domicilio_comercial' => 'nullable|string|max:245',
            'Condicion_frente_al_IVA' => 'required|string|max:100',
            'CUIT_emisor' => 'required|numeric|digits:11',
            'Ingresos_brutos' => 'nullable|numeric',
            'Fecha_de_inicio_de_actividades' => 'nullable|date_format:d/m/Y',
            
            // Datos del comprobante
            'Punto_de_venta' => 'required|integer|min:1|max:99999',
            'Comp_nro' => 'required|string|max:8',
            'Fecha_de_emision' => 'required|date_format:d/m/Y',
            'Fecha_de_vto_para_el_pago' => 'nullable|date_format:d/m/Y',
            
            // Períodos (null para facturas de productos)
            'Periodo_facturado_desde' => 'nullable|date_format:d/m/Y',
            'Periodo_facturado_hasta' => 'nullable|date_format:d/m/Y',
            
            // Receptor
            'CUIT_DNI_receptor' => 'required|numeric|digits:11',
            'Apellido_y_nombre_razon_social' => 'nullable|string|max:200',
            'Condicion_de_venta' => 'nullable|string|max:100',
            
            // Detalle (arrays)
            'Codigo' => 'required|array|min:1',
            'Producto_servicio' => 'required|array|min:1',
            'Cantidad' => 'required|array|min:1',
            'U_medida' => 'nullable|array',
            'Precio_unit' => 'required|array|min:1',
            'Bonificacion' => 'nullable|array',
            'Importe_bonificado' => 'nullable|array',
            'Subtotal_de_producto_servicio' => 'required|array|min:1',
            
            // Totales
            'Subtotal' => 'required|string',
            'Importe_otros_tributos' => 'nullable|string',
            'Importe_total' => 'required|string',
            
            // CAE
            'CAE' => 'required|numeric|digits_between:13,14',
            'Fecha_de_vto_de_cae' => 'required|date_format:d/m/Y',
            
            // IVA (opcional)
            'IVA_discriminado' => 'nullable|string',
        ], [
            'Comp_nro.required' => 'El número de comprobante es obligatorio',
            'CUIT_DNI_receptor.digits' => 'El CUIT receptor debe tener 11 dígitos',
            'CAE.digits_between' => 'El CAE debe tener entre 13 y 14 dígitos',
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Valida que el receptor sea Alba
     */
    private function validarCuitReceptor(string $cuit): void
    {
        $cuitAlba = config('facturacion.cuit_alba');
        
        if ($cuit !== $cuitAlba) {
            throw new \Exception("CUIT receptor ({$cuit}) no corresponde a Alba ({$cuitAlba})");
        }
    }

    /**
     * Transforma el JSON del bot al formato de Alba
     */
    private function transformarBotAAlba(array $botData): array
    {
        // 1. Buscar o crear proveedor
        $idProveedor = $this->facturaRepo->buscarOCrearProveedor(
            (string) $botData['CUIT_emisor'],
            [
                'razon_social' => $botData['Razon_social'],
                'domicilio' => $botData['Domicilio_comercial'] ?? null
            ]
        );
        
        // 2. Calcular impuestos
        $impuestos = $this->calcularImpuestos($botData);
        
        // 3. Formatear número y sucursal
        $numeroFactura = str_pad($botData['Comp_nro'], 8, '0', STR_PAD_LEFT);
        $sucursal = str_pad($botData['Punto_de_venta'], 5, '0', STR_PAD_LEFT);

        return [
            'cabecera' => [
                'id_tipo_factura' => $this->mapearTipoFactura($botData['Tipo_de_factura']),
                'cod_sindicato' => (int) config('facturacion.defaults.cod_sindicato'),
                'id_tipo_comprobante' => (int) config('facturacion.defaults.id_tipo_comprobante'),
                'fecha_comprobante' => $this->formatearFecha($botData['Fecha_de_emision']),
                'id_proveedor' => (int) $idProveedor,
                'periodo' => $this->generarPeriodo($botData),
                'tipo_letra' => (string) $botData['Tipo_de_factura'],
                'fecha_vencimiento' => $this->generarFechaVencimiento($botData),
                'sucursal' => (string) $sucursal,
                'numero' => (string) $numeroFactura,
                'cod_usuario' => (int) config('facturacion.defaults.cod_usuario_bot'),
                'total_iva' => (float) $impuestos['total_iva'],
                'total_neto' => (float) $impuestos['total_neto'],
                'subtotal' => (float) $impuestos['subtotal'],
                'cae_cai' => (string) $botData['CAE'],
                'id_locatorio' => (int) config('facturacion.defaults.id_locatorio'),
                'tipo_carga_detalle' => 'Automation',
                'observaciones_resumen' => (string) $this->generarObservaciones($botData),
                'refacturacion' => 0,
            ],
            'detalle' => $this->transformarDetalle($botData, $impuestos)
        ];
    }

    /**
     * Calcula los impuestos según el tipo de factura
     */
    private function calcularImpuestos(array $botData): array
    {
        $tipoFactura = $botData['Tipo_de_factura'];
        $subtotal = $this->limpiarImporte($botData['Subtotal']);
        $importeTotal = $this->limpiarImporte($botData['Importe_total']);
        
        // Factura tipo C: IVA incluido en el total
        if ($tipoFactura === 'C') {
            return [
                'total_iva' => 0.00,
                'subtotal' => $importeTotal,
                'total_neto' => $importeTotal,
                'tipo_iva' => 1, // IVA incluido
            ];
        }
        
        // Facturas A, B, E: IVA discriminado
        $iva = $importeTotal - $subtotal;
        
        return [
            'total_iva' => round($iva, 2),
            'subtotal' => round($subtotal, 2),
            'total_neto' => round($importeTotal, 2),
            'tipo_iva' => 2, // IVA discriminado
        ];
    }

    /**
     * Transforma el detalle del bot al formato Alba
     */
    private function transformarDetalle(array $botData, array $impuestos): array
    {
        $detalle = [];
        $cantidadItems = count($botData['Codigo']);
        
        for ($i = 0; $i < $cantidadItems; $i++) {
            $precioUnit = $this->limpiarImporte($botData['Precio_unit'][$i] ?? '0');
            $cantidad = (float) ($botData['Cantidad'][$i] ?? 1);
            $subtotal = $this->limpiarImporte($botData['Subtotal_de_producto_servicio'][$i] ?? '0');
            $bonificacion = isset($botData['Bonificacion'][$i]) 
                ? $this->limpiarImporte($botData['Bonificacion'][$i]) 
                : 0;
            
            // Calcular IVA por item
            $ivaItem = 0;
            $porcentajeIva = 0;
            $totalItem = $subtotal;
            
            if ($botData['Tipo_de_factura'] !== 'C' && $impuestos['total_iva'] > 0) {
                // Prorratear IVA según subtotal
                $proporcion = $subtotal / $impuestos['subtotal'];
                $ivaItem = round($impuestos['total_iva'] * $proporcion, 2);
                $porcentajeIva = $subtotal > 0 ? round(($ivaItem / $subtotal) * 100, 2) : 0;
                $totalItem = $subtotal + $ivaItem;
            }
            
            // id_articulo: usar el código del bot o el genérico si no viene
            $idArticulo = null;
            
            if (isset($botData['Codigo'][$i]) && is_numeric($botData['Codigo'][$i])) {
                // Intentar usar el código que envía el bot
                $idArticulo = (int) $botData['Codigo'][$i];
            }
            
            // Si no tiene código o la tabla no permite NULL, usar el genérico
            if ($idArticulo === null || $idArticulo === 0) {
                $idArticulo = (int) config('facturacion.articulos.id_articulo_generico', 1);
            }
            
            $detalle[] = [
                'id_articulo' => $idArticulo,
                'cantidad' => $cantidad,
                'precio_neto' => round($precioUnit, 2),
                'iva' => $porcentajeIva,
                'subtotal' => round($subtotal, 2),
                'monto_iva' => round($ivaItem, 2),
                'total_importe' => round($totalItem, 2),
                'id_tipo_iva' => $impuestos['tipo_iva'],
                'observaciones' => $botData['Producto_servicio'][$i] ?? "Item " . ($i + 1),
            ];
        }
        
        return $detalle;
    }

    /**
     * Genera el período en formato YYYY-MM
     */
    private function generarPeriodo(array $botData): string
    {
        // Si tiene período facturado, usar esa fecha
        if (!empty($botData['Periodo_facturado_desde'])) {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $botData['Periodo_facturado_desde'])
                ->format('Y-m');
        }
        
        // Si no (facturas de productos), usar fecha de emisión
        return \Carbon\Carbon::createFromFormat('d/m/Y', $botData['Fecha_de_emision'])
            ->format('Y-m');
    }

    /**
     * Genera la fecha de vencimiento
     */
    private function generarFechaVencimiento(array $botData): string
    {
        // Si tiene fecha de vencimiento para el pago, usar esa
        if (!empty($botData['Fecha_de_vto_para_el_pago'])) {
            return $this->formatearFecha($botData['Fecha_de_vto_para_el_pago']);
        }
        
        // Si no, usar fecha de vencimiento del CAE (caso facturas de productos)
        return $this->formatearFecha($botData['Fecha_de_vto_de_cae']);
    }

    /**
     * Genera observaciones automáticas
     */
    private function generarObservaciones(array $botData): string
    {
        $obs = ['Factura ingresada automáticamente desde bot'];
        
        // Si tiene período facturado
        if (!empty($botData['Periodo_facturado_desde']) && !empty($botData['Periodo_facturado_hasta'])) {
            $obs[] = sprintf(
                'Período facturado: %s al %s',
                $botData['Periodo_facturado_desde'],
                $botData['Periodo_facturado_hasta']
            );
        }
        
        // Condición de venta
        if (!empty($botData['Condicion_de_venta'])) {
            $obs[] = 'Condición: ' . $botData['Condicion_de_venta'];
        }
        
        // Condición IVA del emisor
        if (!empty($botData['Condicion_frente_al_IVA'])) {
            $obs[] = 'IVA Emisor: ' . $botData['Condicion_frente_al_IVA'];
        }
        
        return implode(' | ', $obs);
    }

    /**
     * Limpia y convierte importes a float
     */
    private function limpiarImporte(string $importe): float
    {
        // Remover caracteres no numéricos excepto punto y coma
        $limpio = preg_replace('/[^0-9,.]/', '', $importe);
        
        // Convertir coma a punto decimal
        $limpio = str_replace(',', '.', $limpio);
        
        return (float) $limpio;
    }

    /**
     * Formatea fecha de d/m/Y a Y-m-d
     */
    private function formatearFecha(string $fecha): string
    {
        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Fecha inválida: {$fecha}. Formato esperado: dd/mm/yyyy");
        }
    }

    /**
     * Mapea letra de factura a ID
     */
    private function mapearTipoFactura(string $tipo): int
    {
        $map = config('facturacion.tipo_factura_map');
        
        if (!isset($map[$tipo])) {
            throw new \Exception("Tipo de factura desconocido: {$tipo}");
        }
        
        return $map[$tipo];
    }
}