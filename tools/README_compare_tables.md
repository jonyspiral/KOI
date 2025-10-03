# Comparador de tablas (SQL Server → MySQL)

Script PHP compatible con PHP 5.2+ para comparar **nombres de tablas** entre un origen (SQL Server o un CSV exportado) y un destino (MySQL), verificando coincidencia **case-sensitive**.

## Archivos
- `compare_tables.php`

## Requisitos
- PHP 5.2+ con `mysqli` (para MySQL).
- (Opcional) Extensión `mssql_*` si querés leer las tablas de SQL Server directamente.

## Configuración
Editá los `define()` al inicio del script para ajustar host/usuario/clave/DB de MySQL y, si usás `DB_DRIVER='mssql'`, los parámetros de SQL Server.

## Uso con CSV (recomendado)
1. Exportá desde SQL Server un CSV con la lista de tablas. La columna puede llamarse `TABLE_NAME` (o será tomada la primera columna si no existe).
2. Ejecutá:

```bash
php compare_tables.php /ruta/a/table.CSV
```

## Uso conectando directo a SQL Server (opcional)
1. Asegurate de tener la extensión `mssql_*` en tu PHP legacy.
2. Cambiá en el script:
   ```php
   define('DB_DRIVER', 'mssql');
   ```
3. Ejecutá:
   ```bash
   php compare_tables.php
   ```

## Salida
- **Faltantes en MySQL**: están en SQL Server pero no en MySQL (ni siquiera por case-insensitive).
- **Extras en MySQL**: están en MySQL pero no en SQL Server (ni siquiera por case-insensitive).
- **Mismatches de mayúsculas/minúsculas**: existen en ambos si ignorás el case, pero **no** coinciden exactamente en el nombre.

El script termina con exit code `0` si todo coincide exactamente; caso contrario `2`.