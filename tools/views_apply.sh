#!/usr/bin/env bash
set -euo pipefail

SQL_SRC="/var/www/encinitas/tmp/mysql_views_auto.fixed.sql"
DB="koi1_stage"
TMP="/var/www/encinitas/tmp"
OUT="$TMP/views_split"
LOG="$TMP/views_apply.log"
ERR="$TMP/views_errors.log"

if [ ! -s "$SQL_SRC" ]; then
  echo "No existe $SQL_SRC"; exit 1
fi

rm -rf "$OUT"; mkdir -p "$OUT"
:> "$LOG"; :> "$ERR"

echo "== Normalizando SQL (quitando DROP VIEW y corrigiendo DATEDIFF) =="
# 1) sacar cualquier DROP multi-línea o en línea
# 2) mapear DATEDIFF estilo SQL Server a MySQL (dd/hh/mi/ss/mm/yy)
#    - dd → DATEDIFF(Y, X) (en días)
#    - hh/mi/ss/mm/yy → TIMESTAMPDIFF(unidad, X, Y)
sed -E '
  s/^\s*DROP\s+VIEW\s+IF\s+EXISTS\b[^;]*;//Ig
  s/GETDATE\(\)/NOW()/Ig
' "$SQL_SRC" \
| sed -E '
  s/DATEDIFF\(\s*dd\s*,\s*([^,]+)\s*,\s*([^)]+)\)/DATEDIFF(\2,\1)/Ig;
  s/DATEDIFF\(\s*hh\s*,\s*([^,]+)\s*,\s*([^)]+)\)/TIMESTAMPDIFF(HOUR,\1,\2)/Ig;
  s/DATEDIFF\(\s*mi\s*,\s*([^,]+)\s*,\s*([^)]+)\)/TIMESTAMPDIFF(MINUTE,\1,\2)/Ig;
  s/DATEDIFF\(\s*ss\s*,\s*([^,]+)\s*,\s*([^)]+)\)/TIMESTAMPDIFF(SECOND,\1,\2)/Ig;
  s/DATEDIFF\(\s*mm\s*,\s*([^,]+)\s*,\s*([^)]+)\)/TIMESTAMPDIFF(MONTH,\1,\2)/Ig;
  s/DATEDIFF\(\s*yy\s*,\s*([^,]+)\s*,\s*([^)]+)\)/TIMESTAMPDIFF(YEAR,\1,\2)/Ig
' > "$OUT/_normalized.sql"

echo "== Partiendo archivo por CREATE OR REPLACE VIEW =="
awk 'BEGIN{RS="CREATE OR REPLACE VIEW"; ORS=""}
NR==1{next}
{
  fn=sprintf("%s/%06d.sql","'"$OUT"'", NR);
  print "CREATE OR REPLACE VIEW"$0 > fn; close(fn)
}' "$OUT/_normalized.sql"

echo "== Aplicando vistas por pasadas hasta converger =="
pass=1
while : ; do
  echo "== PASS $pass ==" | tee -a "$LOG"
  progressed=0
  :> "$OUT/_all.list"; ls "$OUT"/[0-9][0-9][0-9][0-9][0-9][0-9].sql 2>/dev/null | sort > "$OUT/_all.list" || true
  [ ! -s "$OUT/_all.list" ] && echo "No hay vistas pendientes. Listo." && break
  :> "$OUT/_failed.list"

  while read -r f; do
    # intentar descubrir nombre "esquema.vista" sólo para el log
    name=$(sed -nE 's/^CREATE OR REPLACE VIEW[[:space:]]+`?([^` .]+)`?(\.`?([^`]+)`?)?.*/\1.\3/p; q' "$f")
    [ -z "$name" ] && name=$(basename "$f")
    if mysql --default-character-set=utf8mb4 "$DB" < "$f" 2>>"$ERR"; then
      echo "OK   $name" | tee -a "$LOG"
      rm -f "$f"
      progressed=$((progressed+1))
    else
      echo "FAIL $name" | tee -a "$LOG"
      echo "$f" >> "$OUT/_failed.list"
    fi
  done < "$OUT/_all.list"

  if [ ! -s "$OUT/_failed.list" ]; then
    echo "== DONE: todas las vistas creadas =="; break
  fi
  if [ $progressed -eq 0 ]; then
    echo "== STOP: no hubo progreso; quedan dependencias o errores de SQL =="; break
  fi
  pass=$((pass+1))
done

echo
echo "== Top 30 faltantes/errores más frecuentes =="
grep -E "Unknown table|doesn.t exist|Unknown column|Incorrect parameter|You have an error" "$ERR" \
| sed -E "s/.*'(koi1_stage\.)?([A-Za-z0-9_]+)'.*/\2/" \
| sort | uniq -c | sort -nr | head -30 || true

echo
echo "== Resumen =="
echo "Log detalle: $LOG"
echo "Errores crudos: $ERR"
[ -d "$OUT" ] && echo "Vistas aún pendientes (si quedaron): $(ls "$OUT"/*.sql 2>/dev/null | wc -l)"
