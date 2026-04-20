#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

out="tools/reports"
mkdir -p "$out"

# índice de PHP
find . -type f -name "*.php" | sort > "$out/php_files.txt"

echo "[1/6] T-SQL en consultas…"
rg -n --hidden --glob '!vendor' -e 'WITH\s*\(\s*NOLOCK' \
   -e '\bGETDATE\s*\(' \
   -e '\bISNULL\s*\(' \
   -e '\bLEN\s*\(' \
   -e 'SELECT\s+TOP\s+\d+' \
   -e 'DATEDIFF\s*\(\s*day' \
   -e '\[[^\]]+\]' \
   > "$out/tsql_hits.txt" || true

echo "[2/6] rasgos PHP>5.6…"
rg -n --hidden --glob '!vendor' -e '\?\?' \
   -e '<=>|new\s+class' \
   -e 'declare\s*\(\s*strict_types' \
   -e 'function[^(]*\([^)]*\)\s*:\s*\w+' \
   -e ':\s*(int|string|array|bool|float)\s*;$' \
   > "$out/php56_incompat.txt" || true

echo "[3/6] funciones mssql/sqlsrv/mysql_* legacy…"
rg -n --hidden --glob '!vendor' -e '\bmssql_' -e '\bsqlsrv_' -e '\bmysql_' \
   > "$out/db_apis_legacy.txt" || true

echo "[4/6] FROM/JOIN con nombres “raros” (posible case sensitive)…"
rg -n --hidden --glob '!vendor' -e 'FROM\s+[`"]?[A-Z][A-Za-z0-9_]*' -e 'JOIN\s+[`"]?[A-Z][A-Za-z0-9_]*' \
   > "$out/case_sensitive_soup.txt" || true

echo "[5/6] UPDATE/INSERT a tablas clave (para NOT NULL sin default)…"
rg -n --hidden --glob '!vendor' -e 'INSERT\s+INTO\s+\w+' -e 'UPDATE\s+\w+' \
   > "$out/writes_map.txt" || true

echo "[6/6] uso de Mutex/semaforos…"
rg -n --hidden --glob '!vendor' -e 'new\s+Mutex\(' -e 'sem_get|sem_release' \
   > "$out/mutex_map.txt" || true

echo "✅ Listo. Mirá los archivos en $out/"
