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

**Decisión arquitectónica:** los períodos son globales (compartidos entre todas las razones sociales del grupo). Lo que varía por empresa es si ese período está abierto o cerrado, modelado en una tabla separada.

#### Modelo `tb_cont_periodos_contables` (global)

**`app/Models/Contabilidad/PeriodosContablesEntity.php`**
- `'id_razon'` **eliminado** del `$fillable`
- Relaciones agregadas:
  - `estadosRazon()` → `hasMany(PeriodoEstadoRazonEntity)`
  - `estadoRazon()` → `hasOne(PeriodoEstadoRazonEntity)`

#### Modelo nuevo `tb_cont_periodo_estado_razon` (por empresa)

**`app/Models/Contabilidad/PeriodoEstadoRazonEntity.php`** _(nuevo)_
- `$fillable`: `id_periodo_contable`, `id_razon`, `activo`, `vigente`, `cod_usuario`, `fecha_registra`, `cod_usuario_modifica`, `fecha_modifica`
- Relación `periodoContable()` → `belongsTo(PeriodosContablesEntity)`

#### `PeriodosContablesRepository.php` — cambios

| Método | Cambio |
|---|---|
| `findByCreate` | `firstOrCreate` para el período global; luego itera **todas las razones sociales** (`RazonSocialModelo::all()`) y crea un `PeriodoEstadoRazonEntity` para cada una via `firstOrCreate` |
| `findByUpdate` | Actualiza el período global + el estado de la razón específica en `PeriodoEstadoRazonEntity` |
| `findByList` | Si viene `id_razon`: JOIN con `tb_cont_periodo_estado_razon` para filtrar y traer `activo`/`vigente` de la empresa |
| `findByListAnual` | Ídem, con filtro adicional `id_tipo_periodo = 2` |
| `findByExistsPeriodoMensual` | Verifica si el período **ya existe globalmente** (un row en `tb_cont_periodos_contables`). Como se crea para todas las razones, si existe para una existe para todas — no tiene sentido crearlo de nuevo |
| `findByExistsPeriodoAnual` | Ídem anual |
| `findByExistsPeriodoActivo` | Verifica `activo = 1` en `estadosRazon` para la razón social dada |
| `findByPeriodoContableActivo` | Ídem |
| `findByPeriodoContableActivoNow` | Ídem + filtro por fechas |
| `toggleActivo($id, $idRazon)` | Si viene `$idRazon`: toglea en `PeriodoEstadoRazonEntity`; si no: toglea en la tabla principal |
| `toggleVigente($id, $idRazon)` | Ídem |
| `setActivoByAnio($anio, $activo, $idRazon)` | Si viene `$idRazon`: actualiza estados de esa razón; si no: actualiza tabla principal |

#### `PeriodosContablesService.php` — cambios

- `toggleActivo` y `toggleVigente`: leen `request()->id_razon` y lo pasan al repository

#### DB — secuencia de migración

```sql
-- 1. Crear tabla de estado por razón social
CREATE TABLE tb_cont_periodo_estado_razon (
    id_periodo_estado_razon INT        NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_periodo_contable     INT        NOT NULL,
    id_razon                INT        NOT NULL,
    activo                  TINYINT(1) NOT NULL DEFAULT 0,
    vigente                 TINYINT(1) NOT NULL DEFAULT 1,
    cod_usuario             INT        NULL,
    fecha_registra          DATETIME   NULL,
    cod_usuario_modifica    INT        NULL,
    fecha_modifica          DATETIME   NULL,
    UNIQUE KEY uk_periodo_razon (id_periodo_contable, id_razon),
    CONSTRAINT fk_per_est_periodo FOREIGN KEY (id_periodo_contable)
        REFERENCES tb_cont_periodos_contables (id_periodo_contable),
    CONSTRAINT fk_per_est_razon FOREIGN KEY (id_razon)
        REFERENCES tb_razones_sociales (id_razon)
);

-- 2. Identificar el período canónico por grupo (el de id menor)
CREATE TEMPORARY TABLE tmp_canonicos AS
SELECT MIN(id_periodo_contable) AS id_canonico,
       periodo, anio_periodo, COALESCE(mes, 0) AS mes_key
FROM tb_cont_periodos_contables
GROUP BY periodo, anio_periodo, COALESCE(mes, 0);

-- 3. Backfill de estado para los períodos que ya tenían id_razon asignado
INSERT INTO tb_cont_periodo_estado_razon
    (id_periodo_contable, id_razon, activo, vigente, cod_usuario, fecha_registra)
SELECT c.id_canonico, p.id_razon, p.activo, p.vigente, p.cod_usuario_crea, p.fecha_registra
FROM tb_cont_periodos_contables p
JOIN tmp_canonicos c
    ON c.periodo = p.periodo AND c.anio_periodo = p.anio_periodo AND c.mes_key = COALESCE(p.mes, 0)
WHERE p.id_razon IS NOT NULL;

-- 3b. Completar backfill: crear estado para TODAS las razones × TODOS los períodos canónicos
--     (los que no estaban cubiertos por el paso 3, con activo/vigente = 1 por defecto)
INSERT IGNORE INTO tb_cont_periodo_estado_razon
    (id_periodo_contable, id_razon, activo, vigente, fecha_registra)
SELECT c.id_canonico, r.id_razon, 1, 1, NOW()
FROM tmp_canonicos c
CROSS JOIN tb_razones_sociales r;

-- 4. Redirigir FKs de asientos al período canónico
UPDATE tb_cont_asientos_contables a
JOIN tb_cont_periodos_contables p ON p.id_periodo_contable = a.id_periodo_contable
JOIN tmp_canonicos c
    ON c.periodo = p.periodo AND c.anio_periodo = p.anio_periodo AND c.mes_key = COALESCE(p.mes, 0)
SET a.id_periodo_contable = c.id_canonico
WHERE a.id_periodo_contable <> c.id_canonico;

-- 5. Eliminar períodos duplicados (no canónicos)
DELETE FROM tb_cont_periodos_contables
WHERE id_periodo_contable NOT IN (SELECT id_canonico FROM tmp_canonicos);

-- 6. Quitar id_razon de la tabla de períodos
ALTER TABLE tb_cont_periodos_contables DROP COLUMN id_razon;
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

**Contraasientos heredan `id_razon`:** en los 4 repos de historial (`AsientosFacturacionHistorialRepository`, `AsientosPagoHistorialRepository`, `AsientosReintegrosHistorialRepository`, `AsientosDiscapacidadHistorialRepository`), `generarContraasiento()` pasa `$asientoOriginal->id_razon` a `findByCrearAsiento()`. Sin esto, el contraasiento quedaba con `id_razon = NULL` y no aparecía en vistas filtradas por razón.

**Timezone:** la app está en `UTC`, pero la convención es guardar fechas en hora Argentina. `AsientoContableRepository` y los 4 repos de historial usan `Carbon::now('America/Argentina/Buenos_Aires')`; `fecha_asiento` se deriva de `$this->fechaActual->toDateString()`. Antes, `now()`/`Carbon::now()` en UTC corrían la fecha del asiento un día.

**Determinación proveedor/prestador en pagos:** `crearAsientoPago()` usa `$esFacturaProveedor = !empty($id_proveedor)` (dato real del pago), no `id_tipo_factura` (que no se envía en el payload de pago).

**`app/Http/Controllers/Contabilidad/Services/AsientoContableController.php`**
- Alta manual de asiento: pasa `$request->id_razon` al llamar `findByCrearAsiento()`

**`app/Http/Controllers/facturacion/FacturacionProcesosController.php`**
- **Mapeo `id_locatorio → id_razon`:** el frontend manda la razón social como `id_locatorio`. Apenas se decodifica la cabecera: `if (empty($cabecera->id_razon) && !empty($cabecera->id_locatorio)) $cabecera->id_razon = $cabecera->id_locatorio;`
- Alta de factura: valida `$cabecera->id_razon` antes de crear asiento (HTTP 422 si falta), luego lo incluye en `$datosFactura`
- Modificación de factura: mismo guard + propagación de `id_razon`
- `findByIdFactura()` eager-loadea `asientoContable.detalle.planCuenta` para que la edición pueda mostrar el código de cuenta de la imputación DEBE

**`app/Http/Controllers/Tesoreria/Services/TesPagosController.php`**
- Confirmación de pago: valida `$params->id_razon` antes de crear asiento (rollback + HTTP 422 si falta), luego lo incluye en `$datosPago`
- Período: usa `findByPeriodoContableActivoNow($params->id_razon)` — período **mensual** vigente de la fecha actual (antes usaba `findByPeriodoContableActivo`, que devolvía cualquier período activo y a veces agarraba el anual)

**`app/Http/Controllers/Contabilidad/Repository/LibroDiarioRepository.php`**
- `findListDetalleResumenDiario()` y `getTotalCount()`: filtro por `id_razon`:
```php
if (isset($filters->id_razon) && !empty($filters->id_razon)) {
    $query->where('id_razon', $filters->id_razon);
}
```
- **Fix de `vigente`:** estos dos métodos tenían `->where('vigente', 'ACTIVO' || 'CONTRAASIENTO')`. En PHP eso evalúa a `true` → `WHERE vigente = 1`, y como `vigente` es ENUM, `= 1` matchea solo el primer valor (`ACTIVO`), ocultando los asientos originales que quedaron en `CONTRAASIENTO`. Corregido a `->whereIn('vigente', ['ACTIVO', 'CONTRAASIENTO'])`.

**DB:**
```sql
ALTER TABLE tb_cont_asientos_contables ADD COLUMN id_razon INT NULL;
```

---

## 1. Backend — Correcciones y cambios

### 1.1 Validación de períodos contables

Con el modelo nuevo, el período es **global** (un solo row en `tb_cont_periodos_contables` por mes/año, compartido entre todas las razones). Crear el mismo período dos veces es redundante para todas las empresas, por lo que la validación es también global.

**`app/Http/Controllers/Contabilidad/Repository/PeriodosContablesRepository.php`**
- `findByExistsPeriodoMensual($anio, $mes, $idRazon = null)` — verifica si ya existe un row global para ese año/mes (el parámetro `$idRazon` se mantiene por compatibilidad de firma pero no se usa)
- `findByExistsPeriodoAnual($anio, $idRazon = null)` — ídem anual

**`app/Http/Controllers/Contabilidad/Services/PeriodosContablesService.php`**
- `getProcesar`: llama a ambos métodos de validación antes de intentar crear
- Mensaje de error: `"...ya existe para esta razón social."` (se puede refinar a "ya existe" en una futura iteración)

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

### 3.3 Filtros server-side por razón social (modales y combos)

A diferencia de los visores (filtrado en cliente), estos componentes filtran en el backend para no traer/mostrar datos de otras razones:

**Modal de selección de plan de cuentas** (`modal-planes-cuentas-principales`)
- Nuevo `@Input() idRazon`; lo agrega al params de `getListarCuentasCompleto` solo si está presente
- Backend: `PlanesCuentasRepository::findByDetalleCuentasPlanesCompleto($search, $idRazon)` filtra via `whereHas('plan', fn($q) => $q->where('id_razon', $idRazon))`
- Los **19 componentes de relación** que abren el modal pasan `modalRef.componentInstance.idRazon = this.frmRelacion.get('id_razon')?.value`

**Modal de imputaciones contables** (`modal-imputaciones-contables`, usado en carga de factura)
- Nuevo `@Input() idRazon`; lo agrega al filtro antes de elegir el endpoint proveedor/prestador (respeta el flag `isProveedor`)
- Backend: ambos repos (`ImputacionCuentaContableRepository`, `ImputacionProveedoresCuentaContableRepository`) ya filtran por `id_razon` en `findByListarConFiltros`
- `datos-factura.component.ts`: al abrir el modal pasa `idRazon` desde el control `id_locatorio`

**Combos de período** (`cbo-periodos-contables`, `cbo-periodos-anuales`)
- Nuevo `@Input() idRazon`; recargan en `ngOnChanges` y lo pasan como `id_razon` al GET
- En `tap-datos-de-asiento-contable.html` reciben `[idRazon]="fmrAsiento.get('id_razon')?.value"`

**Visor de períodos** (`visor-periodo-contable`)
- `onListar` pasa `{ id_razon: this.filterRazonSocial }`; toggles activo/vigente pasan `id_razon` al backend

---

## 4. Notas importantes

- **Columnas nullable:** `id_razon` queda `NULL` hasta que corre el backfill. No convertir a `NOT NULL` hasta verificar que el backfill completó (resultado de la query de verificación = 0 para todas las tablas).
- **Filtrado en frontend:** Los **visores** de relaciones (sección 3.1) filtran por `id_razon` en cliente (el backend devuelve todo). En cambio, los **modales de selección** filtran server-side (ver sección 3.3). El valor por defecto del filtro es `1` (Alba).
- **Rows históricas:** Filas anteriores al cambio tendrán `id_razon = NULL` y no aparecerán en los visores hasta completar el backfill.
- **Motor de asientos:** Los métodos `obtener*` que usa el motor ya están aislados por razón social a través de la cadena `id_detalle_plan → id_plan_cuenta → id_razon`. Los `findByBuscarRelacion*` corregidos son auxiliares y no afectan el flujo principal de asientos.
