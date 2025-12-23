<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | CUIT de Alba (Empresa Receptora)
    |--------------------------------------------------------------------------
    */
    'cuit_alba' => env('FACTURACION_CUIT_ALBA', '30117454909'),
    
    /*
    |--------------------------------------------------------------------------
    | Valores por defecto para facturas automáticas
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'cod_sindicato' => 1,
        'id_tipo_comprobante' => 1,
        'id_locatorio' => 1,
        'cod_usuario_bot' => 999, // Usuario del sistema para el bot
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Mapeo de tipos de factura (Letra → ID)
    |--------------------------------------------------------------------------
    */
    'tipo_factura_map' => [
        'A' => 1,
        'B' => 2,
        'C' => 3,
        'E' => 4,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Configuración de proveedores
    |--------------------------------------------------------------------------
    */
    'proveedores' => [
        'crear_automaticamente' => true, // Crear proveedor si no existe
        'estado_default' => 1, // Vigente
        'cod_tipo_impuesto_default' => 1,
        'cod_tipo_iva_default' => 1,
        'id_proveedor_tipo_default' => 1,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Configuración de artículos/servicios
    |--------------------------------------------------------------------------
    */
    'articulos' => [
        'permitir_sin_articulo' => false, // La tabla NO permite NULL en id_articulo
        'id_articulo_generico' => 1, // ID del artículo genérico para servicios sin código específico
    ],
    
];