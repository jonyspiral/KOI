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

## Impacto esperado
Módulos que usan SP desde `Factory` en el repo Docker deberían dejar de fallar por sintaxis `EXEC`, por ejemplo:

- `content/administracion/proveedores/gestion_proveedores/buscar.php`
- `content/administracion/cobranzas/gestion_cobranza/buscar.php`
- módulos de stock a fecha

## Validación pendiente
- Confirmar búsqueda con `saldoFechaHasta` en Docker para:
  - gestión proveedores
  - gestión cobranza
