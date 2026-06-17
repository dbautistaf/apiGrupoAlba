# Asientos Automáticos — Facturación y Pagos

## Estado: En desarrollo — parcialmente implementado

---

## Resumen

Sistema de generación automática de asientos contables al operar facturas y pagos (alta, modificación, anulación). El asiento se crea, modifica o revierte de forma transparente dentro de la misma transacción que procesa cada operación, sin afectar los flujos existentes.

---

## Archivos involucrados

### Facturación

| Archivo | Rol |
|---|---|
| `app/Http/Controllers/facturacion/FacturacionProcesosController.php` | Orquesta el flujo: llama a creación/modificación/anulación de asientos |
| `app/Http/Controllers/Contabilidad/Repository/AsientoContableRepository.php` | Crea asientos y líneas de detalle (`crearAsientoFactura`, `crearAsientoPago`) |
| `app/Http/Controllers/Contabilidad/Repository/AsientosFacturacionHistorialRepository.php` | Historial, contraasientos y búsqueda del asiento vigente de facturas |
| `app/Models/Contabilidad/ImputacionesCuentaContableEntity.php` | Imputaciones contables de **prestadores** |
| `app/Models/Contabilidad/ImputacionesProveedoresCuentaContableEntity.php` | Imputaciones contables de **proveedores** |
| `app/Http/Controllers/Contabilidad/Repository/ImputacionProveedoresCuentaContableRepository.php` | CRUD imputaciones proveedores |
| `app/Http/Controllers/Contabilidad\Services/ImputacionProveedoresCuentaContableController.php` | Endpoints imputaciones proveedores |

### Pagos

| Archivo | Rol |
|---|---|
| `app/Http/Controllers/Tesoreria/Services/TesPagosController.php` | Orquesta el flujo: llama a creación/anulación de asientos de pago |
| `app/Http/Controllers/Contabilidad/Repository/AsientosPagoHistorialRepository.php` | Historial, contraasientos y búsqueda del asiento vigente de pagos |
| `app/Models/Contabilidad/AsientosPagoHistorialEntity.php` | Model → `tb_cont_asientos_pago_historial` |

---

## Tablas de base de datos

### Renombradas
| Tabla anterior | Tabla actual |
|---|---|
| `tb_cont_imputacion_cuenta_contable` | `tb_cont_imputacion_prestadores_cuenta_contable` |

### Columnas agregadas
```sql
-- Detalle de asientos: referencia a la imputación usada
ALTER TABLE tb_cont_asientos_contables_detalle
    ADD COLUMN id_imputacion_proveedor INT(11) NULL AFTER cod_proveedor,
    ADD COLUMN id_imputacion_prestador INT(11) NULL AFTER cod_prestador;
```

### Creadas
```sql
-- Imputaciones de proveedores
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

-- Historial de asientos de pagos
CREATE TABLE tb_cont_asientos_pago_historial (
    id_pago_asiento      INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_pago              INT(11)      NOT NULL,
    id_asiento_contable  INT(11)      NOT NULL,
    tipo_evento          VARCHAR(20)  NOT NULL,  -- ALTA, ANULACION
    es_contraasiento     TINYINT(1)   NOT NULL DEFAULT 0,
    id_asiento_origen    INT(11)      NULL,
    observacion          VARCHAR(500) NULL,
    cod_usuario          INT(11)      NULL,
    fecha_registra       DATETIME     NULL
);
```

---

## Lógica del asiento de factura

### Datos requeridos desde el frontend (`$cabecera`)

| Campo | Tipo | Descripción |
|---|---|---|
| `idImputacionDebe` | int | ID de la imputación contable para la línea DEBE |

### Estructura del asiento

```
DEBE:  monto = total_neto de la factura
       id_detalle_plan → lookup dinámico por idImputacionDebe
       Prestador → tb_cont_imputacion_prestadores_cuenta_contable
       Proveedor → tb_cont_imputacion_proveedores_cuenta_contable
       Guarda: cod_proveedor + id_imputacion_proveedor  (si tipo_factura == 16)
               cod_prestador + id_imputacion_prestador  (otros tipos)

HABER: monto = total_neto de la factura
       id_detalle_plan → HARDCODEADO
       Prestador → 35 (DEUDAS PRESTACIONALES  2.1.01.01.00)
       Proveedor → 36 (PROVEEDORES             2.1.01.02.00)
       Guarda: cod_proveedor (si tipo_factura == 16) / cod_prestador (otros)

numero_referencia = id_factura
```

### Validaciones y errores

| Condición | Mensaje | HTTP |
|---|---|---|
| `idImputacionDebe` ausente | "Falta la imputación contable (DEBE)... contacte con Contabilidad" | 423 |
| Imputación sin cuenta asignada | "La imputación contable seleccionada no tiene una cuenta contable asignada... contacte con Contabilidad" | 423 |
| Sin proveedor ni prestador | "No se pudo determinar si la factura es de prestador o proveedor... contacte con Contabilidad" | 423 |
| Sin período contable activo | "No se encontró un período contable activo..." | 404 |

### Circuito por operación

**Alta** (`POST /api/v1/facturacion/procesar` — sin `id_factura`)
```
1. Guardar factura + detalle + impuestos + descuentos + archivos
2. Si viene idImputacionDebe:
   a. Verificar período contable activo
   b. crearAsientoFactura() → guardarHistorial(ALTA)
3. Falla → rollback completo
```

**Modificación** (`POST /api/v1/facturacion/procesar` — con `id_factura`)
```
1. Actualizar factura + detalle + impuestos + descuentos + archivos
2. Si tiene asientos Y viene idImputacionDebe:
   a. procesarModificacionFactura():
      - Contraasiento del asiento vigente
      - Nuevo asiento con datos actualizados → guardarHistorial(ALTA)
3. Falla → rollback completo
```

**Anulación** (`DELETE`)
```
1. Si tiene asientos → procesarAnulacionFactura():
   - Contraasiento del asiento vigente → guardarHistorial(ANULACION)
2. Cambiar estado factura → 4 (anulada)
3. Si existe discapacidad vinculada → anular su asiento también
```

---

## Lógica del asiento de pago

### Datos tomados de la OPA (sin depender de las facturas vinculadas)

| Campo | Origen |
|---|---|
| `id_proveedor` / `id_prestador` | `$opaFactus->id_proveedor` / `$opaFactus->id_prestador` |
| CUIT / nombre | `$opaFactus->proveedor` o `$opaFactus->prestador` |
| `id_cuenta_bancaria` | `$params->id_cuenta_bancaria` |
| `monto_total` | Calculado en el controller (suma de lista_pagos) |
| Período contable | `$this->periodoContableActivo` (inyectado en constructor) |

> La OPA puede tener múltiples facturas vinculadas (`tb_tes_opa_factura`). Por eso los datos del asiento se toman **de la OPA directamente**, no de ninguna factura en particular.

### Estructura del asiento

```
DEBE:  monto = monto_total del pago
       id_detalle_plan → HARDCODEADO (reduce el pasivo)
       Proveedor → 36 (PROVEEDORES             2.1.01.02.00)
       Prestador → 35 (DEUDAS PRESTACIONALES   2.1.01.01.00)
       Guarda: cod_proveedor o cod_prestador

HABER: monto = monto_total del pago
       id_detalle_plan → DINÁMICO vía BancoCuentasContableEntity
       Guarda: id_cuenta_bancaria_cuenta_contable

numero_referencia = id_pago
```

### Circuito por operación

**Confirmación de pago** (`POST /api/v1/tesoreria/confirmar-pago`)
```
1. Validar saldo cuenta bancaria
2. Confirmar pago + actualizar OPA
3. Retiro de cuenta bancaria + movimiento
4. Si OPA no es null:
   a. crearAsientoPago()
   b. guardarHistorial(ALTA) en tb_cont_asientos_pago_historial
5. Falla → rollback completo
```

**Anulación de pago** (`DELETE /api/v1/tesoreria/anular-pago`)
```
1. Si tiene asientos (pagoTieneAsientos):
   - procesarAnulacionPago():
     - Busca asiento vigente en historial por id_pago
     - Genera contraasiento → guardarHistorial(ANULACION)
   - Si no tiene asiento → se ignora, se permite anular igual
2. Anular pago + OPA (flujo existente)
3. Falla → rollback completo
```

> No hay edición de pago implementada. Si se implementa en el futuro: contraasiento + nuevo asiento, igual que modificación de factura.

---

## Imputaciones contables — separación prestadores / proveedores

### Modelos

| Model | Tabla | PK |
|---|---|---|
| `ImputacionesCuentaContableEntity` | `tb_cont_imputacion_prestadores_cuenta_contable` | `id_imputacion_cuenta_contable` |
| `ImputacionesProveedoresCuentaContableEntity` | `tb_cont_imputacion_proveedores_cuenta_contable` | `id_imputacion_proveedor_cuenta_contable` |

### Métodos en `AsientoContableRepository`

```php
obtenerCuentaContableImputacion($id)          // prestadores
obtenerCuentaContableImputacionProveedor($id) // proveedores
```

### Endpoints prestadores (sin cambios)

| Método | Ruta | Acción |
|---|---|---|
| GET | `/get-imputaciones-contables` | Listar con filtros |
| GET | `/get-imputaciones-prestadores` | Listar con filtros (alias) |
| POST | `/relacionar-imputacion-plan-cuenta` | Crear / modificar |

### Endpoints proveedores (nuevos)

| Método | Ruta | Acción |
|---|---|---|
| GET | `/get-imputaciones-proveedores` | Listar con filtros |
| GET | `/editar-imputacion-proveedor/{id}` | Obtener por ID |
| POST | `/relacionar-imputacion-proveedor-plan-cuenta` | Crear / modificar |
| DELETE | `/eliminar-imputacion-proveedor` | Baja lógica |

---

## Patrón general de historial (factura y pago)

Ambos flujos siguen el mismo patrón:

```
Alta    → crearAsiento*() + guardarHistorial(ALTA)
Modif.  → contraasiento del vigente + crearAsiento*() + guardarHistorial(ALTA)
Anulac. → contraasiento del vigente + guardarHistorial(ANULACION)

Búsqueda del vigente:
  Historial WHERE id_entidad = X
           AND tipo_evento = 'ALTA'
           AND es_contraasiento = false
           AND asientoContable.vigente = 'ACTIVO'
```

Los repositorios de historial **no gestionan transacción propia** — toda la atomicidad recae en el `DB::beginTransaction()` del controlador.

---

## Pendiente de implementar

- [x] ~~Transacciones anidadas~~ — resuelto: historial repos sin transacción propia
- [x] ~~Asientos automáticos de pagos~~ — implementado con historial
- [ ] ABM de imputaciones de proveedores desde el frontend
- [ ] ABM de imputaciones de prestadores desde el frontend (verificar si ya existe)
- [ ] Tests del flujo completo: alta → modificación → anulación (facturas y pagos)
- [ ] Validar comportamiento cuando `idImputacionDebe` no viene (factura sin imputación — ¿se permite sin asiento?)
