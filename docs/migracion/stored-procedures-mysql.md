# Stored procedures en MySQL - KOI1 Encinitas

## Problema
Parte del código legacy seguía armando consultas de stored procedures con sintaxis SQL Server:

```sql
EXEC nombre_sp ...
```

En el entorno Docker `encinitas` el motor real es MySQL 8, por lo que la ejecución correcta debe pasar por:

```sql
CALL nombre_sp(...)
```

## Causa raíz detectada (2026-05-12)
En `factory/Factory.php`:

- `getArrayFromStoredProcedure()`
- `getListObjectFromStoredProcedure()`

seguían usando:

- `Mapper::getQueryStoredProcedure()`
- `Datos::EjecutarSQL(...)`

Eso armaba `EXEC ...`, incompatible con MySQL.

## Fix aplicado
Ambos métodos ahora delegan a:

- `Datos::EjecutarStoredProcedure($storedProcedureName, $parametros)`

De este modo el runtime MySQL usa `CALL ...` y el parsing de parámetros queda centralizado en `Datos.php`.

## Stored procedures extraídos desde SQL Server
- `saldo_proveedores_a_fecha`
- `saldo_clientes_a_fecha`

Definiciones legacy confirmadas desde Ubuntu vía KOI2 (`sqlsrv_encinitas` por ODBC/FreeTDS hacia `192.168.2.100`).

## Port inicial a MySQL
Scripts creados:

- `docs/migracion/sql/saldo_proveedores_a_fecha.mysql.sql`
- `docs/migracion/sql/saldo_clientes_a_fecha.mysql.sql`

Criterios del port:

- aceptan fecha legacy `dd/mm/yyyy` y también ISO `yyyy-mm-dd`
- reemplazan `dbo.relativeDate(@fecha, 'last', 0)` por:
  - `d.fecha < DATE_ADD(DATE(v_fecha), INTERVAL 1 DAY)`
  - esto conserva la semántica de “hasta el final del día” sin depender de una función `relativeDate`
- mantienen la lógica de signo original:
  - `NDB` y `FAC` suman
  - el resto resta

## Impacto esperado
Módulos que usan SP desde `Factory` en el repo Docker deberían dejar de fallar por sintaxis `EXEC`, por ejemplo:

- `content/administracion/proveedores/gestion_proveedores/buscar.php`
- `content/administracion/cobranzas/gestion_cobranza/buscar.php`
- módulos de stock a fecha

## Validación pendiente
- Crear ambos SP en `koi1_stage`
- Confirmar búsqueda con `saldoFechaHasta` en Docker para:
  - gestión proveedores
  - gestión cobranza
- Extraer y portar después:
  - `sp_stock_a_fecha`
  - `sp_stock_mp_a_fecha`
  - `movimientos_caja_sp`
