<?php
/**
 * Configuración de conexión (pensada para PHP 5.6 y legacy).
 * Si querés testear sólo contra MySQL, alcanza con completar las credenciales MySQL.
 * Para SQL Server tenés dos opciones:
 *   - Usar un CSV con la lista de tablas (por ejemplo, exportado desde SSMS).
 *   - Conectar directamente vía mssql_* (FreeTDS) si tu PHP tiene php5.6-sybase instalado.
 */

// Driver preferido para la comparación del lado "origen SQL":
//  - 'csv'  -> leerá un archivo CSV con una columna 'table' (o primera columna = nombre tabla).
//  - 'mssql'-> intentará conectarse a SQL Server usando funciones mssql_* (dblib/freetds).
// Recomendado: 'csv' para entornos legacy sin extensiones cargadas.
define('ORIGIN_DRIVER', 'csv');

/* =========================
   PARÁMETROS MySQL (KOI2)
   ========================= */
define('MYSQL_HOST',    '192.168.2.210');
define('MYSQL_PORT',    3306);
define('MYSQL_USER',    'koiuser');
define('MYSQL_PASS',    'Route667?');
define('MYSQL_DB',      'koi1_stage'); // usa 'koi2_v1' si probás contra dev
define('MYSQL_CHARSET', 'utf8');       // clave para PHP 5.6

/* =========================
   PARÁMETROS SQL SERVER
   ========================= */
// Sólo si usás ORIGIN_DRIVER = 'mssql'
define('MSSQL_HOST', '192.168.2.100');    // ej: 192.168.2.100 o srv-sql:1433
define('MSSQL_USER', 'sa');
define('MSSQL_PASS', 'password');
define('MSSQL_DB',   'spiral');

// Ruta al CSV con la lista de tablas de SQL Server (si ORIGIN_DRIVER = 'csv').
define('SQLSERVER_TABLES_CSV', __DIR__ . '/table.CSV');

// Nombre de la columna del CSV que contiene los nombres de tabla.
// Si es NULL, se usa la primera columna.
define('CSV_TABLES_COLUMN', null);

/* =========================
   AJUSTES DE COMPARACIÓN
   ========================= */
// true => exige igualdad EXACTA de mayúsculas/minúsculas.
define('CASE_SENSITIVE', true);

// Si querés además detectar coincidencias por nombre case-insensitive pero con diferencia de mayúsculas:
define('REPORT_CASE_MISMATCH', true);

// Rutas de salida de reportes
define('OUT_DIR', __DIR__ . '/out'); // se crean CSV/JSON aquí
