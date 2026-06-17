# Plan: Multi Razón Social en Contabilidad

**Rama:** `fran-contable`  
**Fecha:** 2026-06-17

---

## Objetivo

Agregar soporte multi razón social (`id_razon`) al módulo de contabilidad, tanto en backend (Laravel) como en frontend (Angular). Esto permite que cada entidad del grupo (Alba, etc.) tenga sus propias relaciones contables, períodos y asientos aislados por razón social.

---

## 0. Tablas base con `id_razon` — modelos principales

Antes de las relaciones, las tablas estructurales del módulo también recibieron `id_razon`.

### 0.1 `tb_cont_planes_cuentas` — Plan de cuentas

**`app/Models/Contabilidad/PlanesCuentaEntity.php`**
- `'id_razon'` agregado a `$fillable`
- Relación `razonSocial()` → `RazonSocialModelo` via `id_razon`

**`app/Http/Controllers/Contabilidad/Repository/PlanesCuentasRepository.php`**
- `findByCrear`: agrega `'id_razon' => $params->id_razon ?? null`

**DB:**
```sql
ALTER TABLE tb_cont_planes_cuentas ADD COLUMN id_razon INT NULL;
```

> Esta es la tabla origen del backfill de las relaciones. Debe tener `id_razon` completo antes de correr los UPDATEs de la sección 2.

---

### 0.2 `tb_cont_periodos_contables` — Períodos contables

**`app/Models/Contabilidad/PeriodosContablesEntity.php`**
- `'id_razon'` agregado a `$fillable`

**DB:**
```sql
ALTER TABLE tb_cont_periodos_contables ADD COLUMN id_razon INT NULL;
```

---

### 0.3 `tb_cont_asientos_contables` — Asientos contables

**`app/Models/Contabilidad/AsientosContablesEntity.php`**
- `'id_razon'` agregado a `$fillable`
- Relación `razonSocial()` → `RazonSocialModelo` via `id_razon`

**`app/Http/Controllers/Contabilidad/Repository/AsientoContableRepository.php`**

`findByCrearAsiento()` — nuevo parámetro `$id_razon = null`:
```php
public function findByCrearAsiento($id_tipo_asiento, $asiento_modelo, $asiento_leyenda,
    $numero, $id_periodo_contable, $numero_referencia, $vigente, $id_razon = null)
```
El campo `'id_razon'` se persiste en el `create()`.

`findByListar()` — filtro por `id_razon`:
```php
if (!is_null($params->id_razon ?? null) && !empty($params->id_razon)) {
    $query->where('id_razon', $params->id_razon);
}
```

`crearAsientoFactura()` — valida `id_razon` obligatorio y lo pasa a `findByCrearAsiento()`:
```php
$idRazon = $datosFactura['id_razon'] ?? null;
if (!$idRazon) {
    throw new Exception("Falta la razón social para registrar el asiento de la factura...");
}
```

`crearAsientoPago()` — mismo patrón de validación y propagación.

`crearAsientoReintegro()` y `crearAsientoPagoReintegros()` — también pasan `$datosReintegro['id_razon'] ?? null` a `findByCrearAsiento()`.

**`app/Http/Controllers/Contabilidad/Services/AsientoContableController.php`**
- Alta manual de asiento: pasa `$request->id_razon` al llamar `findByCrearAsiento()`

**`app/Http/Controllers/facturacion/FacturacionProcesosController.php`**
- Alta de factura: valida `$cabecera->id_razon` antes de crear asiento (HTTP 422 si falta), luego lo incluye en `$datosFactura`
- Modificación de factura: mismo guard + propagación de `id_razon`

**`app/Http/Controllers/Tesoreria/Services/TesPagosController.php`**
- Confirmación de pago: valida `$params->id_razon` antes de crear asiento (rollback + HTTP 422 si falta), luego lo incluye en `$datosPago`

**`app/Http/Controllers/Contabilidad/Repository/LibroDiarioRepository.php`**
- `findByLibroDiario()`: filtro por `id_razon`:
```php
if (isset($filters->id_razon) && !empty($filters->id_razon)) {
    $query->where('id_razon', $filters->id_razon);
}
```

**DB:**
```sql
ALTER TABLE tb_cont_asientos_contables ADD COLUMN id_razon INT NULL;
```

---

## 1. Backend — Correcciones y cambios

### 1.1 Validación de períodos contables por razón social

**Problema:** `findByExistsPeriodoMensual` y `findByExistsPeriodoAnual` no filtraban por `id_razon`, causando un 409 al crear un período para una razón social distinta si el mes/año ya existía para otra.

**Archivos modificados:**

**`app/Http/Controllers/Contabilidad/Repository/PeriodosContablesRepository.php`**
- `findByExistsPeriodoMensual($anio, $mes, $idRazon = null)` — agrega `WHERE id_razon = ?` cuando se provee `$idRazon`
- `findByExistsPeriodoAnual($anio, $idRazon = null)` — mismo patrón

**`app/Http/Controllers/Contabilidad/Services/PeriodosContablesService.php`**
- `getProcesar`: pasa `$request->id_razon` a ambos métodos de validación
- Mensaje de error actualizado: `"...ya existe para esta razón social."`

---

### 1.2 Agregar `id_razon` (desnormalizado) a las 9 tablas de relaciones

**Decisión arquitectónica:** desnormalizar `id_razon` directamente en las tablas de relaciones para filtrado directo sin JOIN encadenados. El mismo proveedor/banco puede mapear a cuentas distintas para razones sociales distintas.

#### Tablas afectadas y sus modelos

| Tabla | Modelo |
|---|---|
| `tb_cont_proveedor_cuenta_contable` | `ProveedorCuentaContableEntity` |
| `tb_cont_banco_cuenta_contable` | `BancoCuentasContableEntity` |
| `tb_cont_familia_cuenta_contable` | `FamiliaCuentaContableEntity` |
| `tb_cont_impuesto_cuenta_contable` | `ImpuestoCuentaContableEntity` |
| `tb_cont_retenciones_cuenta_contable` | `RetencionCuentasContablesEntity` |
| `tb_cont_formas_pago_cuenta_contable` | `FormasPagoCuentasContableEntity` |
| `tb_cont_tipo_prestador_cuenta_contable` | `TipoPrestadorCuentaContableEntity` |
| `tb_cont_imputacion_prestadores_cuenta_contable` | `ImputacionesCuentaContableEntity` |
| `tb_cont_imputacion_proveedores_cuenta_contable` | `ImputacionesProveedoresCuentaContableEntity` |

#### Cambios en modelos

En cada uno de los 9 modelos se agregó `'id_razon'` al array `$fillable`, después de `'id_detalle_plan'`.

**Archivos:**
- `app/Models/Contabilidad/ProveedorCuentaContableEntity.php`
- `app/Models/Contabilidad/BancoCuentasContableEntity.php`
- `app/Models/Contabilidad/FamiliaCuentaContableEntity.php`
- `app/Models/Contabilidad/ImpuestoCuentaContableEntity.php`
- `app/Models/Contabilidad/RetencionCuentasContablesEntity.php`
- `app/Models/Contabilidad/FormasPagoCuentasContableEntity.php`
- `app/Models/Contabilidad/TipoPrestadorCuentaContableEntity.php`
- `app/Models/Contabilidad/ImputacionesCuentaContableEntity.php`
- `app/Models/Contabilidad/ImputacionesProveedoresCuentaContableEntity.php`

#### Cambios en repositories

En cada uno de los 9 repositories:
- `findByCrear`: agrega `'id_razon' => $params->id_razon ?? null`
- `findByUpdate`: agrega `$obj->id_razon = $params->id_razon ?? null`

**Archivos:**
- `app/Http/Controllers/Contabilidad/Repository/ProveedorPlanesCuentaRepository.php`
- `app/Http/Controllers/Contabilidad/Repository/BancoCuentaContableRepository.php`
- `app/Http/Controllers/Contabilidad/Repository/FamiliaPlanesCuentaRepository.php`
- `app/Http/Controllers/Contabilidad/Repository/ImpuestoCuentaContableRepository.php`
- `app/Http/Controllers/Contabilidad/Repository/RetencionCuentaContableRepository.php`
- `app/Http/Controllers/Contabilidad/Repository/FormaPagoCuentaContableRepository.php`
- `app/Http/Controllers/Contabilidad/Repository/TipoPrestadorPlanesCuentaRepository.php`
- `app/Http/Controllers/Contabilidad/Repository/ImputacionCuentaContableRepository.php`
- `app/Http/Controllers/Contabilidad/Repository/ImputacionProveedoresCuentaContableRepository.php`

---

### 1.3 Corrección de métodos `findByBuscarRelacion*` rotos

**Problema:** Los 7 métodos `findByBuscarRelacion*` filtraban por `whereHas('detallePlan', fn($q) => $q->where('id_periodo_contable', $idPeriodo))`, pero `id_periodo_contable` no existe en `tb_cont_planes_cuentas_detalle`. Eran código muerto.

**Fix:** Se reemplazó el filtro por `id_razon` directamente sobre la tabla de relación.

Métodos corregidos:
- `ProveedorPlanesCuentaRepository::findByBuscarRelacionProveedor($idProveedor, $idRazon = null)`
- `FormaPagoCuentaContableRepository::findByBuscarRelacionFormaPago($idFormaPago, $idRazon = null)`
- `BancoCuentaContableRepository::findByBuscarRelacionBanco($idCuentaBancaria, $idRazon = null)`
- `FamiliaPlanesCuentaRepository::findByBuscarRelacionFamilia($idTipoFamilia, $idRazon = null)`
- `ImpuestoCuentaContableRepository::findByBuscarRelacionImpuesto($idImpuesto, $idRazon = null)`
- `RetencionCuentaContableRepository::findByBuscarRelacionRetencion($id_retencion, $idRazon = null)`
- `TipoPrestadorPlanesCuentaRepository::findByBuscarRelacionTipoPrestador($cod, $idRazon = null)` + `findByPlanCuentaPorTipoPrestador($cod, $idRazon = null)`

---

## 2. Base de datos — Queries necesarias

### 2.1 Agregar columna `id_razon`

```sql
ALTER TABLE tb_cont_proveedor_cuenta_contable               ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
ALTER TABLE tb_cont_banco_cuenta_contable                   ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
ALTER TABLE tb_cont_familia_cuenta_contable                 ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
ALTER TABLE tb_cont_impuesto_cuenta_contable                ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
ALTER TABLE tb_cont_retenciones_cuenta_contable             ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
ALTER TABLE tb_cont_formas_pago_cuenta_contable             ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
ALTER TABLE tb_cont_tipo_prestador_cuenta_contable          ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
ALTER TABLE tb_cont_imputacion_prestadores_cuenta_contable  ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
ALTER TABLE tb_cont_imputacion_proveedores_cuenta_contable  ADD COLUMN id_razon INT NULL AFTER id_detalle_plan;
```

### 2.2 Backfill desde la cadena de planes

Deriva `id_razon` desde `id_detalle_plan → tb_cont_planes_cuentas_detalle → tb_cont_planes_cuentas.id_razon`:

```sql
UPDATE tb_cont_proveedor_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;

UPDATE tb_cont_banco_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;

UPDATE tb_cont_familia_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;

UPDATE tb_cont_impuesto_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;

UPDATE tb_cont_retenciones_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;

UPDATE tb_cont_formas_pago_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;

UPDATE tb_cont_tipo_prestador_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;

UPDATE tb_cont_imputacion_prestadores_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;

UPDATE tb_cont_imputacion_proveedores_cuenta_contable t
JOIN tb_cont_planes_cuentas_detalle d ON d.id_detalle_plan = t.id_detalle_plan
JOIN tb_cont_planes_cuentas          p ON p.id_plan_cuenta  = d.id_plan_cuenta
SET t.id_razon = p.id_razon;
```

### 2.3 Verificar backfill

```sql
SELECT 'proveedor' AS tabla, COUNT(*) AS sin_razon FROM tb_cont_proveedor_cuenta_contable           WHERE id_razon IS NULL
UNION ALL SELECT 'banco',      COUNT(*) FROM tb_cont_banco_cuenta_contable                           WHERE id_razon IS NULL
UNION ALL SELECT 'familia',    COUNT(*) FROM tb_cont_familia_cuenta_contable                         WHERE id_razon IS NULL
UNION ALL SELECT 'impuesto',   COUNT(*) FROM tb_cont_impuesto_cuenta_contable                        WHERE id_razon IS NULL
UNION ALL SELECT 'retencion',  COUNT(*) FROM tb_cont_retenciones_cuenta_contable                     WHERE id_razon IS NULL
UNION ALL SELECT 'forma_pago', COUNT(*) FROM tb_cont_formas_pago_cuenta_contable                     WHERE id_razon IS NULL
UNION ALL SELECT 'tipo_prest', COUNT(*) FROM tb_cont_tipo_prestador_cuenta_contable                  WHERE id_razon IS NULL
UNION ALL SELECT 'imp_prest',  COUNT(*) FROM tb_cont_imputacion_prestadores_cuenta_contable          WHERE id_razon IS NULL
UNION ALL SELECT 'imp_prov',   COUNT(*) FROM tb_cont_imputacion_proveedores_cuenta_contable          WHERE id_razon IS NULL;
```

Si alguna da > 0, primero completar `id_razon` en `tb_cont_planes_cuentas` y repetir el UPDATE.

---

## 3. Frontend — Angular (`frontGrupoAlbaSalud`)

### 3.1 Visores (páginas de listado)

Patrón aplicado en los 9 visores del módulo `relaciones-contables`:

**TS — propiedades y filtro:**
```typescript
dtListaRaw: any[] = []
dtListRazonSocial: any[] = []
frmFilters = this.fb.group({ id_razon: [1], ... })

// constructor: inject COnfiguracionService as srvLocatario

ngOnInit(): void {
  this.srvLocatario.getListRazonSocial().subscribe({
    next: (res) => { this.dtListRazonSocial = res; }, ...
  });
  this.frmFilters.get('id_razon')?.valueChanges.subscribe(() => this.aplicarFiltroRazon());
  this.onListarData()
}

aplicarFiltroRazon = (() => {
  const idRazon = this.frmFilters.value.id_razon;
  this.dtListaPadron = (!idRazon)
    ? [...this.dtListaRaw]
    : this.dtListaRaw.filter((i: any) => Number(i?.id_razon) === Number(idRazon));
  this.currentPage = 1;
  this.dtListaPaginate = this.dtListaPadron.slice(0, this.itemsPerPage);
  this.totalItems = this.dtListaPadron.length;
})

// onListarData: asigna a dtListaRaw y luego llama aplicarFiltroRazon()
```

**HTML — selector razón social:**
```html
<app-form-global-selected
  [isLabel]="'Razón Social'"
  [isId]="'cboRazonSocial'"
  [dtListaData]="dtListRazonSocial"
  [isKeyValue]="'id_razon'"
  [isTextLabel]="'razon_social'"
  formControlName="id_razon"
></app-form-global-selected>
```

**Visores actualizados:**
- `visor-relaciones` (imputaciones prestadores)
- `imputaciones-proveedores`
- `tipo-prestador`
- `impuestos`
- `retenciones`
- `formas-pago`
- `bancos`
- `familias`
- `proveedores`

---

### 3.2 Modales de alta / edición

Patrón aplicado en los modales de creación/edición:

**TS:**
```typescript
dtListRazonSocial: any[] = []

frmRelacion = this.fb.group({
  id_razon: [1, [Validators.required]],
  // ... resto de campos
})

// constructor: inject COnfiguracionService as srvLocatario
// ngOnInit: cargar getListRazonSocial()
// En modo edición: patchValue incluye id_razon: this.relacion.id_razon ?? 1
```

**HTML:**
```html
<app-form-global-selected
  [isLabel]="'Razón Social'"
  [isId]="'cboRazonSocialModal'"
  [dtListaData]="dtListRazonSocial"
  [isKeyValue]="'id_razon'"
  [isTextLabel]="'razon_social'"
  formControlName="id_razon"
></app-form-global-selected>
```

---

## 4. Notas importantes

- **Columnas nullable:** `id_razon` queda `NULL` hasta que corre el backfill. No convertir a `NOT NULL` hasta verificar que el backfill completó (resultado de la query de verificación = 0 para todas las tablas).
- **Filtrado en frontend:** El backend devuelve todos los registros; el frontend filtra por `id_razon` en cliente. El valor por defecto del filtro es `1` (Alba).
- **Rows históricas:** Filas anteriores al cambio tendrán `id_razon = NULL` y no aparecerán en los visores hasta completar el backfill.
- **Motor de asientos:** Los métodos `obtener*` que usa el motor ya están aislados por razón social a través de la cadena `id_detalle_plan → id_plan_cuenta → id_razon`. Los `findByBuscarRelacion*` corregidos son auxiliares y no afectan el flujo principal de asientos.
