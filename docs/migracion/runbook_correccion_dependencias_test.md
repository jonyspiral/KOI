# Runbook de correccion minima de dependencias en test

## Alcance

Este runbook cubre correcciones minimas de dependencias funcionales en ambientes de test para modulos no productivos.

Reglas base:

- `koi1_stage` es solo referencia funcional y diagnostico.
- La fuente formal para recargar no produccion es `SQL Server encinitas`.
- No ejecutar acciones destructivas automaticamente.
- No tocar `empresa`.
- No tocar `/var/www/encinitas`.
- No tocar `koi1_stage` como origen de recarga.

## Cuándo usarlo

Usar este runbook si una auditoria detecta alguno de estos sintomas:

- tabla, view o procedure faltante
- definicion distinta de `roles_por_usuario_v`, `operadores_v`, `sucursales_v` u otra view critica
- filas faltantes que rompen joins entre `Clientes`, `Operadores`, `personal`, `users` o `roles`
- diferencias de cantidad o claves funcionales que afectan pantallas

## Secuencia obligatoria

### 1. Backup previo

Antes de cualquier apply aprobado, generar backup del ambiente objetivo de test.

Minimo exigido:

- backup logico de las tablas/objetos que serian intervenidos
- identificacion de fecha, host y base destino
- evidencia guardada fuera de este repo

Nunca:

- commitear dumps
- guardar passwords, DSN o secretos en archivos versionados

### 2. Auditoria read-only

Ejecutar primero la auditoria funcional.

Ejemplo recomendado para la etapa inicial:

```bash
php scripts/koi-parity-audit.php \
  --flow=abm_clientes \
  --client-id=204 \
  --expected-vendedor=V00358 \
  --expected-personal=358 \
  --stage-engine=mysql \
  --stage-dsn='mysql:host=127.0.0.1;dbname=koi1_stage;charset=utf8mb4' \
  --reference-engine=odbc \
  --reference-dsn='odbc:Driver=FreeTDS;Server=sqlserver;Port=1433;Database=encinitas_test;TDS_Version=8.0' \
  --format=all \
  --json-out=/tmp/abm_clientes_audit.json \
  --csv-out=/tmp/abm_clientes_audit.csv
```

La auditoria debe dejar evidencia de:

- objetos faltantes
- diferencias de views
- claves funcionales faltantes
- joins rotos
- caso obligatorio del cliente `204`

### 3. Aprobacion humana

No preparar apply hasta que alguien apruebe explicitamente:

- fuente `SQL Server encinitas`
- lista puntual de objetos
- ambiente destino
- ventana de ejecucion

La aprobacion debe rechazar:

- objetos productivos
- origen `spiral` mezclado con etapa no productiva
- clears o drops implicitos

### 4. Apply controlado

Este runbook no ejecuta apply automaticamente.

Si se aprueba un apply manual, debe respetar:

- origen formal `SQL Server encinitas`
- lote acotado a la etapa aprobada
- orden por dependencia funcional
- sin `INSERT`, `UPDATE`, `DELETE`, `DROP`, `--clear` automaticos desde esta herramienta

Orden sugerido para ET01:

1. `roles`, `funcionalidades_por_rol`
2. `users`, `roles_por_usuario`, `roles_por_usuario_v`
3. `personal`, `Operadores`, `operadores_v`
4. `Paises`, `Provincias`, `localidades`, `areas_empresa`
5. `condiciones_iva`, `Formas_pago`, `grupo_empresa`, `Grupos_clientes`
6. `Clientes`, `sucursales_clientes`, `sucursales_v`, `Contactos`
7. `autorizaciones_personas`, `autorizaciones_tipos`
8. `koi_sessions` solo si se decide reactivar persistencia de sesion

### 5. Verificacion de view

Despues del apply aprobado, verificar como minimo:

- `roles_por_usuario_v`
- `operadores_v`
- `sucursales_v`

Chequeos recomendados:

```sql
SELECT * FROM roles_por_usuario_v WHERE cod_usuario = 'usuario_muestra';
SELECT * FROM operadores_v WHERE cod_operador = 'V00358';
SELECT * FROM sucursales_v WHERE cod_cli = 204;
```

Si una view falla o devuelve columnas inconsistentes, detener la etapa.

### 6. Prueba de endpoint

Realizar smoke tests sobre endpoints legacy del flujo afectado.

Para ET01:

- `/content/sistema/usuarios/abm/buscar.php?idUsuario=...`
- `/content/abm/clientes/buscar.php?idCliente=204`
- `/content/abm/clientes/getSucursal.php?idCliente=204&idSucursal=...`
- `/content/abm/clientes/getContactos.php?idCliente=204`

Validaciones minimas:

- el usuario carga con sus roles
- el cliente carga con su vendedor
- la sucursal y el contacto responden sin nulls funcionales
- el caso `204 -> V00358 -> 358 -> operadores_v` queda consistente

## Caso obligatorio de prueba

El circuito obligatorio despues de cualquier correccion de dependencias de ET01 es:

```text
Cliente 204
-> cod_vendedor V00358
-> Operadores.cod_personal 358
-> personal.cod_personal 358
-> operadores_v
```

Se considera falla si:

- falta cualquiera de las filas
- `cod_vendedor` no coincide
- `operadores_v` no expone al vendedor
- la busqueda del cliente rompe por join o view

## Criterio de cierre

Una correccion minima en test solo puede darse por cerrada si existen:

- backup previo registrado
- auditoria read-only previa
- aprobacion humana explicita
- apply manual acotado
- verificacion de views
- prueba de endpoint
- evidencia archivada de resultados

## Lo que este runbook no permite

- usar `koi1_stage` como fuente de recarga
- reconstruir produccion desde `encinitas`
- correr migraciones reales sin aprobacion humana
- guardar secretos, DSN o dumps en el repo
- automatizar acciones destructivas
