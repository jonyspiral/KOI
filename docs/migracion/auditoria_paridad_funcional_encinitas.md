# Auditoria de Paridad Funcional Encinitas (ET01 + ET05)

## Objetivo
Verificar paridad funcional entre:

- Baseline MySQL: `koi1_stage`
- Candidato MySQL: `encinitas_test`
- Fuente formal no productiva: SQL Server `encinitas` (`sqlsrv_encinitas`)
- Fuente exclusiva de modulos productivos: SQL Server `spiral` (`sqlsrv_spiral`)

sin ejecutar escrituras ni cambios de esquema.

## Problema comprobado que motiva el auditor
Se detecto catalogo cliente vacio en `encinitas_test` pese a existir secciones/familias/articulos. La causa fue ausencia de `lineas_productos`, dependencia de:

- `content/cliente/menu.php`
- cadena funcional: `catalogo_secciones.cod_linea_producto -> lineas_productos.cod_linea_nro -> lineas_productos.titulo_catalogo`

El auditor implementado detecta este faltante como `BLOCKER` antes de publicar.

## Entregables implementados
- Script read-only: `scripts/koi-functional-parity.php`
- Manifiesto de dependencias: `resources/migration-manifests/encinitas_funcional_dependencias.tsv`
- Salida por corrida: `storage/app/parity-runs/parity_<run_id>.json` y `.tsv`
- Alias de ultima corrida: `storage/app/parity-runs/latest.json` y `latest.tsv`

## Alcance funcional auditado
### ET01 Sesion y ABM Clientes
Dependencias auditadas:
- `users`
- `roles_por_usuario`
- `roles`
- `funcionalidades_por_rol`
- `contactos`
- `Clientes`
- `sucursales_clientes`
- `operadores_v`
- `personal`

Controles relacionales clave:
- `roles_por_usuario.cod_usuario -> users.cod_usuario`
- `roles_por_usuario.cod_rol -> roles.cod_rol`
- `funcionalidades_por_rol.cod_rol -> roles.cod_rol`
- `users.cod_contacto -> contactos.cod_contacto`

### ET05 Catalogo Cliente
Dependencias auditadas:
- `catalogos`
- `catalogo_secciones`
- `lineas_productos`
- `catalogo_seccion_familias`
- `familias_producto`
- `catalogo_seccion_familia_articulos`
- `articulos`
- `colores_por_articulo`
- `tipo_producto_stock`
- `stock_menos_pendiente_vw`
- `articulos_imagenes_v`

Controles ET05 explicitamente requeridos:
- `catalogo_secciones.cod_linea_producto -> lineas_productos.cod_linea_nro`
- `catalogo_seccion_familias.cod_familia_producto -> familias_producto.id`
- `catalogo_seccion_familia_articulos.(cod_articulo,cod_color_articulo) -> articulos/colores_por_articulo`
- `lineas_productos.titulo_catalogo` no vacio para secciones enlazables
- comparacion de conjunto funcional de categorias/menu entre baseline y target

## Criterio de severidad
- `BLOCKER`: objeto requerido faltante en target o imposibilidad de validar flujo critico
- `ERROR`: objeto presente pero no enlazable (huerfanos), incompatibilidad fuerte o categoria faltante en target
- `WARNING`: divergencia no bloqueante (conteo menor, extras no criticos)
- `OK`: dependencia alineada

El script retorna codigo no cero si existe al menos un `BLOCKER` o `ERROR`.

## Regla de compatibilidad tabla/view por nombre logico
El auditor acepta como compatible (sin error) que un nombre logico resuelva como `BASE TABLE` o `VIEW` cuando aplica compatibilidad legacy de runtime/case.

## Inspeccion de codigo real incluida
El auditor recorre y usa evidencia en:
- `content/cliente/menu.php`
- `content/cliente/catalogo/index.php`
- `factory/Factory.php`
- `factory/Mapper.php`
- clases de catalogo/cliente/usuario/rol/contacto/sucursal/operador/personal

Incluye en reporte:
- archivo PHP y linea que exige la dependencia (`required_by_files`)
- joins detectados en Mapper (`mapper_joins_detected`)

## Matriz operacional (flujo > dependencia > baseline > target > estado)
La matriz se genera por corrida en:
- JSON: `results[]`
- TSV: columnas `flow_id`, `dependency_id`, `logical_object`, `baseline_*`, `target_*`, `status`, `severity`

Consulta rapida (PowerShell) sobre la ultima corrida:

```powershell
Import-Csv storage/app/parity-runs/latest.tsv -Delimiter "`t" |
  Select-Object flow_id,dependency_id,logical_object,baseline_count,target_count,status,severity |
  Format-Table -AutoSize
```

## Lista ordenada de cargas necesarias (sin ejecucion)
Se emite automaticamente en `suggested_loads` para todo `BLOCKER/ERROR`.

Formato de comando sugerido (no ejecutado):

```bash
php artisan koi:import-legacy-object --source=sqlsrv_spiral --object=lineas_productos --target=mysql_encinitas_test --dry-run
```

### Fuente obligatoria por flujo
- ET01: `sqlsrv_encinitas`
- ET05: `sqlsrv_spiral`

## Ejecucion del auditor

```bash
php scripts/koi-functional-parity.php \
  --flows=ET01,ET05 \
  --manifest=resources/migration-manifests/encinitas_funcional_dependencias.tsv \
  --out-dir=storage/app/parity-runs
```

Variables de entorno MySQL requeridas:
- Baseline: `KOI_BASELINE_MYSQL_DSN` o `KOI_BASELINE_MYSQL_HOST/PORT/DB/USER/PASS`
- Target: `KOI_TARGET_MYSQL_DSN` o `KOI_TARGET_MYSQL_HOST/PORT/DB/USER/PASS`

Opcionales de trazabilidad de fuente formal:
- `KOI_SQLSRV_ENCINITAS_DSN`
- `KOI_SQLSRV_SPIRAL_DSN`

## Orden recomendado de remediacion (sin automatizar)
1. Resolver `BLOCKER` de objetos fisicos faltantes (prioridad ET05: `lineas_productos`).
2. Resolver `ERROR` de integridad (huerfanos/no enlazables).
3. Revalidar conjunto de categorias ET05 (comparacion baseline vs target).
4. Resolver `WARNING` de conteos residuales.
5. Re-ejecutar auditor hasta `BLOCKER=0` y `ERROR=0`.

## Diseno para extender a nuevos modulos
### ET02 Cobranzas
Agregar al manifiesto:
- vistas y SP de cobranzas
- checks relacionales cliente/documento/aplicaciones
- comparacion de saldos (muestras controladas)

### ET03 Proveedores
Agregar:
- maestros proveedor + documentos proveedor
- checks FK proveedor/tipos/condicion_iva
- vistas de gestion

### ET04 Tesoreria
Agregar:
- movimientos caja/bancos/cheques
- SP y vistas de resumen
- checks de conciliacion de claves de operacion

### ET06 Produccion
Agregar:
- rutas, secciones, tareas, ordenes, stock/consumos
- checks de encadenamiento plan->orden->tarea->movimiento
- validaciones de existencia y enlazabilidad por flujo

## Restricciones operativas respetadas
- Sin `INSERT`, `UPDATE`, `DELETE`, `CREATE`, `ALTER`, `DROP`
- Sin migraciones ni cambios de esquema
- Sin tocar empresa, runtime ni configuraciones
- Solo lectura + generacion de reportes locales
