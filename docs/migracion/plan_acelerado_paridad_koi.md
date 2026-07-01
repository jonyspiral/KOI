# Plan acelerado de paridad KOI

## Objetivo

Definir un carril de migracion por paquetes funcionales, con separacion estricta de fuentes:

- No produccion: fuente formal obligatoria `SQL Server encinitas`.
- Produccion: fuente formal obligatoria `SQL Server spiral`.

El orden de trabajo se organiza por flujos completos y pantallas operativas, no por tablas aisladas.

## Regla de procedencia

### Roles de auditoria

- Referencia funcional: `MySQL koi1_stage`, solo lectura, usada para comportamiento validado.
- Destino de prueba: `MySQL encinitas_test`, solo lectura en auditoria, ambiente que debe alcanzar paridad.
- Fuente formal de recarga no productiva: `SQL Server encinitas` via `sqlsrv_encinitas`, usada para trazabilidad, conteos y futuros applies aprobados.
- Fuente formal productiva: `SQL Server spiral` via `sqlsrv_spiral`, usada solo para carril productivo.

Regla dura:

- nunca mezclar estos tres roles en un mismo DSN
- `encinitas_test` no es DSN ODBC ni motor de referencia
- `koi1_stage` no es origen de recarga

### No produccion

- Ambitos: `koi1_stage`, `encinitas_test`, entornos de ensayo funcional.
- Fuente formal de recarga: `SQL Server encinitas`.
- Objetos productivos prohibidos en este carril, incluso si existen en `koi1_stage`.

### Produccion

- Ambitos: futuros lotes productivos y reconstruccion de datos productivos.
- Fuente formal de recarga: `SQL Server spiral`.
- Objetos no productivos no deben mezclarse con este carril.

## Ciclo obligatorio por etapa

Cada etapa se ejecuta siempre en cuatro compuertas:

1. Auditoria
   - Resolver nombres reales antes de comparar datos.
   - Relevar dependencias reales desde codigo (`Factory.php`, `Mapper.php`, clases, endpoints y views/SP llamados).
   - Registrar mayusculas/minusculas y tipo real (`tabla`, `view`, `procedure`, `function`) segun el runtime legacy.
   - Bloquear la auditoria si el manifiesto no coincide con `information_schema` del destino.
   - Modo `parity`: comparar baseline `MySQL koi1_stage` contra target `MySQL encinitas_test`.
   - Modo `provenance`: comparar target `MySQL encinitas_test` contra `SQL Server encinitas` para no produccion y contra `SQL Server spiral` para produccion.
   - Detectar objetos faltantes, vistas distintas, filas que rompen joins y diferencias por clave funcional.
2. Aprobacion
   - Revisar reporte humano + JSON/CSV.
   - Confirmar fuente SQL Server correcta segun el carril.
   - Aprobar lista puntual de objetos a aplicar.
3. Apply
   - Ejecutar solo sobre objetos aprobados.
   - No mezclar objetos de `spiral` con `--source=sqlsrv_encinitas`.
   - No correr clears masivos ni acciones destructivas por defecto.
4. Validacion
   - Validar views, conteos, joins criticos y endpoint/pantalla legacy.
   - Adjuntar evidencia de salida y observaciones.

## Paquetes no productivos

### ET01 - Base de sesión y ABM Clientes

Fuente formal: `SQL Server encinitas`

Flujos y pantallas:

- `premaster.php` -> `UsuarioLogin::login()`
- `/content/sistema/usuarios/abm/index.php`
- `/content/sistema/usuarios/abm/agregar.php`
- `/content/sistema/usuarios/abm/editar.php`
- `/content/abm/clientes/index.php`
- `/content/abm/clientes/buscar.php`
- `/content/abm/clientes/agregar.php`
- `/content/abm/clientes/editar.php`
- `/content/abm/clientes/getSucursal.php`
- `/content/abm/clientes/getContactos.php`
- `/content/abm/clientes/validarCuit.php`

Objetos eje:

- usar siempre el nombre exacto consultado por el codigo legacy
- no normalizar a alias genericos si el runtime usa otra capitalizacion
- `users`, `roles`, `roles_por_usuario`, `roles_por_usuario_v`, `funcionalidades_por_rol`
- `personal`, `Operadores`, `operadores_v`
- `Clientes`, `sucursales_clientes`, `sucursales_v`, `Contactos`
- `areas_empresa`, `condiciones_iva`, `Formas_pago`, `grupo_empresa`, `Grupos_clientes`
- `Paises`, `Provincias`, `localidades`
- `autorizaciones_personas`, `autorizaciones_tipos`
- `koi_sessions` como diagnostico funcional de sesion persistida

Validacion minima:

- Login y carga de usuario sin anomalias de roles.
- ABM usuarios: alta/edicion con lectura consistente de `roles_por_usuario_v`.
- ABM clientes: busqueda, alta, edicion, sucursales y contactos.
- Caso obligatorio:
  - Cliente `204`
  - `cod_vendedor = V00358`
  - `Operadores.cod_personal = 358`
  - `personal.cod_personal = 358`
  - consistencia de `operadores_v`

### ET02 - Cobranzas y seguimiento clientes

Fuente formal: `SQL Server encinitas`

Flujos y pantallas:

- `/content/administracion/cobranzas/gestion_cobranza/buscar.php`
- `/content/administracion/cobranzas/seguimiento_clientes/*`
- `/content/administracion/cobranzas/reportes/aplicaciones_pendientes/buscar.php`

Objetos eje:

- `clientes_v`
- `saldo_clientes_a_fecha`
- `gestiones_clientes_cobranza`
- `cambios_situacion_cliente`
- `pendientes_aplicacion_clientes_v`

Validacion minima:

- Saldo a fecha por muestra de clientes.
- Seguimiento visible desde reporte y detalle.
- Sin joins rotos entre cliente, vendedor y saldos.

### ET03 - Proveedores y documentos no productivos

Fuente formal: `SQL Server encinitas`

Flujos y pantallas:

- `/content/administracion/proveedores/gestion_proveedores/buscar.php`
- circuitos de documentos de proveedor y saldo historico no productivo

Objetos eje:

- `proveedores_datos`
- `documento_proveedor_c`
- `documento_proveedor_d`
- `documento_proveedor_h`
- `saldo_proveedores_a_fecha`
- `gestion_proveedores`

Validacion minima:

- Busqueda de proveedor.
- Saldo a fecha coherente.
- Vista `gestion_proveedores*` sin diferencias estructurales.

### ET04 - Tesoreria no productiva

Fuente formal: `SQL Server encinitas`

Flujos y pantallas:

- `/content/administracion/cajas/movimientos_caja/buscar.php`
- `/content/administracion/cajas/resumen_bancario/buscar.php`
- reportes de cheques y ordenes de pago no productivas

Objetos eje:

- `movimientos_caja_sp`
- `movimientos_caja_v_noanul`
- `movimientos_caja_v_chq`
- `resumen_bancario_v`
- `chequera_c`, `chequera_d`, `chequera_v`

Validacion minima:

- Coincidencia de movimientos por rango.
- Conteos de cheques y saldos consistentes.
- Sin referencias colgantes de caja/banco/importe.

### ET05 - E-commerce y catalogo comercial

Fuente formal: `SQL Server encinitas`

Flujos y pantallas:

- `/content/comercial/ecommerce/*`
- `/content/cliente/catalogo/*`
- `/content/cliente/favoritos/*`

Objetos eje:

- `ecommerce_orders`, `ecommerce_order_details`, `ecommerce_payments`
- `ecommerce_customers`, `ecommerce_order_status`, `ecommerce_payment_methods`
- `articulos_imagenes_v`, `ruta_imagenes`, `stock_menos_pendiente_vw`

Validacion minima:

- Reporte de ventas e-commerce.
- Catalogo y favoritos.
- Join consistente entre orden, cliente, pago y detalle.

### ET06 - Contabilidad y cierres no productivos

Fuente formal: `SQL Server encinitas`

Flujos y pantallas:

- `/content/administracion/contabilidad/*`
- `/content/administracion/finanzas/reportes/*`

Objetos eje:

- `asientos_contables`, `filas_asientos_contables`, `filas_asientos_contables_v`
- `plan_cuentas`
- `periodos_fiscales_cierres`, `periodos_fiscales_tipos`
- `reporte_facturacion_v`, `reporte_articulos_v`

Validacion minima:

- Libro diario, sumas y saldos, consulta de mayores.
- Reportes con conteos equivalentes.

### ET07 - Seguridad, notificaciones y soporte transversal

Fuente formal: `SQL Server encinitas`

Flujos y pantallas:

- `/content/sistema/notificaciones/*`
- `/content/sistema/indicadores/*`
- `/content/sistema/usuarios/por_*/*`

Objetos eje:

- `tipos_notificacion`
- `roles_por_tipo_notificacion`
- `usuarios_por_tipo_notificacion`
- `indicadores_por_rol`
- `usuarios_por_area_empresa`, `usuarios_por_area_empresa_v`

Validacion minima:

- Indicadores segun rol.
- Usuarios notificados por rol/usuario.
- Sin perdida de permisos transversales.

## Carril productivo spiral sin apply

Estado actual: documentado y bloqueado para apply automatico.

### PT01 - Preparacion de fuente productiva

Cambios requeridos en el migrador:

- Agregar `sqlsrv_spiral` como fuente explicita junto a `sqlsrv_encinitas`.
- Exigir que cada etapa/manifiesto declare `origen_sqlserver` esperado.
- Registrar en tiempo de auditoria y apply:
  - fuente pedida
  - fuente declarada por etapa
  - lista de objetos
  - fecha y aprobador

### PT02 - Guarda de procedencia obligatoria

La guarda a disenar debe impedir:

- cualquier objeto productivo si `--source=sqlsrv_encinitas`
- cualquier etapa no productiva si `--source=sqlsrv_spiral`
- cualquier apply si la fuente efectiva no coincide con el manifiesto

Regla sugerida:

- Catalogo `productivo_spiral` con objetos y etapas permitidas solo para `sqlsrv_spiral`
- Catalogo `no_productivo_encinitas` con objetos y etapas permitidas solo para `sqlsrv_encinitas`
- Falla dura antes del apply si un objeto cae fuera del catalogo compatible con la fuente

### PT03 - Recuperacion futura de datos productivos

Sin ejecutar apply ahora, debe quedar previsto:

1. Backup previo de datos productivos hoy cargados por error desde `encinitas`.
2. Inventario de tablas afectadas y su procedencia incorrecta.
3. Reconstruccion futura desde `SQL Server spiral`.
4. Validacion obligatoria por tabla y por etapa antes de promover.

## Auditoria automatizable

Herramienta propuesta: `scripts/koi-parity-audit.php`

Objetivo:

- comparar baseline `koi1_stage` contra target `encinitas_test` por flujo funcional en `--mode=parity`
- comparar target `encinitas_test` contra fuente formal SQL Server en `--mode=provenance`
- emitir salida humana y JSON/CSV
- detectar:
  - desalineacion entre manifiesto y `information_schema` del destino
  - objetos inexistentes
  - views con definicion distinta
  - tablas base faltantes
  - filas faltantes que rompen joins o views
  - diferencias de cantidad
  - diferencias por clave funcional

Ejemplo de paridad para ET01:

```bash
php scripts/koi-parity-audit.php \
  --check-manifest-only \
  --flow=abm_clientes \
  --baseline-engine=mysql \
  --baseline-dsn='mysql:host=127.0.0.1;dbname=koi1_stage;charset=utf8mb4' \
  --target-engine=mysql \
  --target-dsn='mysql:host=127.0.0.1;dbname=encinitas_test;charset=utf8mb4' \
  --format=all \
  --json-out=/tmp/abm_clientes_manifest.json \
  --csv-out=/tmp/abm_clientes_manifest.csv
```

Ejemplo de paridad de datos para ET01:

```bash
php scripts/koi-parity-audit.php \
  --mode=parity \
  --flow=abm_clientes \
  --client-id=204 \
  --expected-vendedor=V00358 \
  --expected-personal=358 \
  --baseline-engine=mysql \
  --baseline-dsn='mysql:host=127.0.0.1;dbname=koi1_stage;charset=utf8mb4' \
  --target-engine=mysql \
  --target-dsn='mysql:host=127.0.0.1;dbname=encinitas_test;charset=utf8mb4' \
  --format=all \
  --json-out=/tmp/abm_clientes_audit.json \
  --csv-out=/tmp/abm_clientes_audit.csv
```

Ejemplo de procedencia no productiva:

```bash
php scripts/koi-parity-audit.php \
  --mode=provenance \
  --flow=abm_clientes \
  --client-id=204 \
  --expected-vendedor=V00358 \
  --expected-personal=358 \
  --target-engine=mysql \
  --target-dsn='mysql:host=127.0.0.1;dbname=encinitas_test;charset=utf8mb4' \
  --source-role=encinitas \
  --source-engine=odbc \
  --source-dsn='odbc:Driver=FreeTDS;Server=sqlserver;Port=1433;Database=encinitas;TDS_Version=8.0' \
  --format=human
```

Salida esperada de alto nivel:

- bloqueo inmediato si el manifiesto no coincide con el nombre real o tipo real del destino
- `users`, `roles`, `roles_por_usuario_v`, `Clientes`, `operadores_v`, `sucursales_v` existen en ambos lados
- mismatch si la definicion de `operadores_v` o `roles_por_usuario_v` difiere
- mismatch si cliente `204` no resuelve `V00358 -> 358 -> operadores_v`
- mismatch si hay usuarios sin roles o vendedores sin `personal`

## Evidencia obligatoria por etapa

Antes de cerrar cada etapa deben quedar:

- comando de auditoria ejecutado
- salida humana resumida
- artefactos JSON/CSV
- aprobacion humana del lote
- endpoints/pantallas validados
- observaciones y pendientes

## Pendientes de aprobacion humana

- Confirmar DSN read-only de `koi1_stage` y `encinitas_test` para auditoria de paridad.
- Confirmar DSN read-only SQL Server para `sqlsrv_encinitas` y `sqlsrv_spiral` en modo procedencia.
- Confirmar catalogo final de objetos productivos que quedaran bloqueados con `sqlsrv_encinitas`.
- Aprobar el primer lote ET01 antes de cualquier apply manual.
