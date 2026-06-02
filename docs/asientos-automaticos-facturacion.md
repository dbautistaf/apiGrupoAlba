# Asientos Automáticos en Facturación

## Estado: En desarrollo — parcialmente implementado

---

## Resumen

Sistema de generación automática de asientos contables al operar facturas (alta, modificación, anulación). El asiento se crea, modifica o revierte de forma transparente dentro de la misma transacción que procesa la factura, sin afectar el flujo de facturación existente.

---

## Archivos involucrados

| Archivo | Rol |
|---|---|
| `app/Http/Controllers/facturacion/FacturacionProcesosController.php` | Orquesta el flujo: llama a creación/modificación/anulación de asientos |
| `app/Http/Controllers/Contabilidad/Repository/AsientoContableRepository.php` | Crea el asiento y sus líneas de detalle |
| `app/Http/Controllers/Contabilidad/Repository/AsientosFacturacionHistorialRepository.php` | Gestiona historial, contraasientos y búsqueda del asiento vigente |
| `app/Models/Contabilidad/ImputacionesCuentaContableEntity.php` | Imputaciones contables de **prestadores** |
| `app/Models/Contabilidad/ImputacionesProveedoresCuentaContableEntity.php` | Imputaciones contables de **proveedores** *(nuevo)* |
| `app/Http/Controllers/Contabilidad/Repository/ImputacionProveedoresCuentaContableRepository.php` | CRUD imputaciones proveedores *(nuevo)* |
| `app/Http/Controllers/Contabilidad/Services/ImputacionProveedoresCuentaContableController.php` | Endpoints imputaciones proveedores *(nuevo)* |

---

## Tablas de base de datos

### Renombradas
| Tabla anterior | Tabla actual |
|---|---|
| `tb_cont_imputacion_cuenta_contable` | `tb_cont_imputacion_prestadores_cuenta_contable` |

### Creadas
```sql
CREATE TABLE tb_cont_imputacion_proveedores_cuenta_contable (
    id_imputacion_proveedor_cuenta_contable INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_detalle_plan       INT(11)      NULL,
    imputacion            VARCHAR(250) NULL,
    codigo                VARCHAR(20)  NULL,
    vigente               TINYINT(1)   NULL DEFAULT 1,
    cod_usuario           INT(11)      NULL,
    fecha_modifica        DATETIME     NULL,
    cod_usuario_modifica  INT(11)      NULL,
    fecha_registra        DATETIME     NULL
);
```

---

## Lógica del asiento de factura

### Datos requeridos desde el frontend (`$cabecera`)

| Campo | Tipo | Descripción |
|---|---|---|
| `idImputacionDebe` | int | ID de la imputación contable para la línea DEBE |

### Estructura del asiento generado

```
DEBE:  monto = total_neto de la factura
       id_detalle_plan → lookup dinámico por idImputacionDebe
       Prestador → tabla tb_cont_imputacion_prestadores_cuenta_contable
       Proveedor → tabla tb_cont_imputacion_proveedores_cuenta_contable

HABER: monto = total_neto de la factura
       id_detalle_plan → HARDCODEADO
       Prestador → 35 (DEUDAS PRESTACIONALES  2.1.01.01.00)
       Proveedor → 36 (PROVEEDORES             2.1.01.02.00)
```

### Método: `crearAsientoFactura` en `AsientoContableRepository`

```
1. Determinar si es prestador o proveedor
2. Validar que venga idImputacionDebe
3. Buscar imputación en tabla correspondiente → obtener id_detalle_plan
4. Si no existe → Exception con mensaje claro para el usuario
5. Construir leyenda: "FACTURA - CUIT - NOMBRE - NRO_FACTURA - FECHA"
6. Crear cabecera del asiento (numero_referencia = id_factura)
7. Crear línea DEBE con id_detalle_plan dinámico
8. Crear línea HABER con id_detalle_plan hardcodeado (35 o 36)
```

### Validaciones y errores

| Condición | Mensaje | HTTP |
|---|---|---|
| `idImputacionDebe` ausente | "Falta la imputación contable (DEBE)... contacte con Contabilidad" | 423 |
| Imputación sin cuenta asignada | "La imputación contable seleccionada no tiene una cuenta contable asignada... contacte con Contabilidad" | 423 |
| Sin proveedor ni prestador | "No se pudo determinar si la factura es de prestador o proveedor... contacte con Contabilidad" | 423 |
| Sin período contable activo | "No se encontró un período contable activo..." | 404 |

---

## Circuito por operación

### Alta de factura (`POST /api/v1/facturacion/procesar` — sin `id_factura` en body)

```
1. Guardar factura + detalle + impuestos + descuentos + archivos
2. Si viene idImputacionDebe:
   a. Verificar período contable activo
   b. Llamar crearAsientoFactura()
   c. Registrar en historial como ALTA
3. Si el asiento falla → rollback completo de la factura
```

### Modificación de factura (`POST /api/v1/facturacion/procesar` — con `id_factura` en body)

```
1. Actualizar factura + detalle + impuestos + descuentos + archivos
2. Si tiene asientos previos (historialRepo.facturaTieneAsientos) Y viene idImputacionDebe:
   a. Verificar período contable activo
   b. procesarModificacionFactura():
      - Obtener asiento vigente desde historial por id_factura
      - Generar contraasiento (invierte DEBE ↔ HABER, marca original como CONTRAASIENTO)
      - Crear nuevo asiento con datos actualizados
      - Registrar nuevo asiento en historial como ALTA
3. Si el asiento falla → rollback completo
```

### Anulación de factura (`DELETE`)

```
1. Si tiene asientos (historialRepo.facturaTieneAsientos):
   a. procesarAnulacionFactura():
      - Obtener asiento vigente desde historial por id_factura
      - Generar contraasiento
      - Registrar en historial como ANULACION
2. Cambiar estado factura → 4 (anulada)
3. Si existe discapacidad vinculada → anular su asiento también
```

### Búsqueda del asiento vigente

Siempre se busca a través del historial, nunca directo:

```php
AsientosFacturacionHistorialEntity
    ->where('id_factura', $idFactura)
    ->where('tipo_evento', 'ALTA')
    ->where('es_contraasiento', false)
    ->whereHas('asientoContable', fn($q) => $q->where('vigente', 'ACTIVO'))
    ->first();
```

---

## Imputaciones contables — separación prestadores / proveedores

### Modelos

| Model | Tabla | PK |
|---|---|---|
| `ImputacionesCuentaContableEntity` | `tb_cont_imputacion_prestadores_cuenta_contable` | `id_imputacion_cuenta_contable` |
| `ImputacionesProveedoresCuentaContableEntity` | `tb_cont_imputacion_proveedores_cuenta_contable` | `id_imputacion_proveedor_cuenta_contable` |

### Métodos en `AsientoContableRepository`

```php
// Prestadores
obtenerCuentaContableImputacion($idImputacion)
// → ImputacionesCuentaContableEntity (tb_cont_imputacion_prestadores_cuenta_contable)

// Proveedores
obtenerCuentaContableImputacionProveedor($idImputacion)
// → ImputacionesProveedoresCuentaContableEntity (tb_cont_imputacion_proveedores_cuenta_contable)
```

### Endpoints existentes (prestadores — sin cambios)

| Método | Ruta | Acción |
|---|---|---|
| GET | `/get-imputaciones-contables` | Listar con filtros |
| GET | `/get-imputaciones-prestadores` | Listar con filtros (alias) |
| POST | `/relacionar-imputacion-plan-cuenta` | Crear / modificar |

### Endpoints nuevos (proveedores)

| Método | Ruta | Acción |
|---|---|---|
| GET | `/get-imputaciones-proveedores` | Listar con filtros |
| GET | `/editar-imputacion-proveedor/{id}` | Obtener por ID |
| POST | `/relacionar-imputacion-proveedor-plan-cuenta` | Crear / modificar |
| DELETE | `/eliminar-imputacion-proveedor` | Baja lógica |

---

## Pendiente de implementar

- [x] ~~Transacciones anidadas~~ — resuelto: `generarContraasiento`, `procesarAnulacionFactura` y `procesarModificacionFactura` ya no gestionan transacción propia. Toda la atomicidad recae en el `DB::beginTransaction()` del controlador.
- [ ] ABM de imputaciones de proveedores desde el frontend
- [ ] ABM de imputaciones de prestadores desde el frontend (verificar si ya existe)
- [ ] Tests del flujo completo: alta → modificación → anulación con asientos
- [ ] Validar comportamiento cuando `idImputacionDebe` no viene (factura sin imputación asignada — ¿se permite sin asiento?)
