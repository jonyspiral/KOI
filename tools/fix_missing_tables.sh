#!/usr/bin/env bash
set -euo pipefail
DB="${1:-koi1_stage}"
MISS="${2:-/var/www/encinitas/tmp/missing_tables.txt}"
SQL="${3:-/var/www/encinitas/tmp/mysql_views_auto.sql}"
OUT="/var/www/encinitas/tmp"
mkdir -p "$OUT"

# 1) Aplanar SQL para extraer tokens limpios
perl -0777 -pe '
  s/--[^\n]*\n/ /g;          # comentarios --
  s#/\*.*?\*/# #gs;          # comentarios /* ... */
  s/`/"/g;                   # normalizar quotes
  s/[\r\n]+/ /g;             # quitar saltos
  s/\s+/ /g;                 # colapsar espacios
' "$SQL" > "$OUT/sql_flat.sql"

# 2) Universo de candidatos desde FROM/JOIN
grep -Poi '\b(from|join)\s+("?[A-Za-z0-9_]+"?(?:\."?[A-Za-z0-9_]+"?)?)' "$OUT/sql_flat.sql" \
| awk '{print $2}' \
| sed -E 's/^"|"$//g; s/"\."/"."/g' \
| awk -F'.' '{print $NF}' \
| sed -E 's/[",;]$//' \
| tr 'A-Z' 'a-z' \
| sort -u > "$OUT/cand_from_sql_all.txt"

# 3) Vistas existentes
mysql -N -e "SELECT LOWER(table_name) FROM information_schema.views
             WHERE table_schema='${DB}'" \
> "$OUT/views_exist.txt" || true

> "$OUT/missing_tables.expanded.txt"
> "$OUT/missing_tables.ambiguous.txt"

# 4) Expandir prefijos y filtrar vistas
while IFS= read -r t; do
  t="$(echo "$t" | tr 'A-Z' 'a-z' | tr -d '\r')"
  [ -z "$t" ] && continue
  # Intento de expansión por prefijo
  matches=$(grep -E "^${t}(_|[a-z0-9])" "$OUT/cand_from_sql_all.txt" || true)
  count=$(printf "%s\n" "$matches" | sed -n '/./,$p' | wc -l | tr -d ' ')
  if [ "$count" = "1" ]; then
    cand="$matches"
  else
    cand="$t"
    if [ "$count" -gt 1 ]; then
      { echo "$t ->"; printf "%s\n" "$matches" | sed 's/^/   - /'; echo; } >> "$OUT/missing_tables.ambiguous.txt"
    fi
  fi

  # Si huele a vista, saltar (termina en _v/_vw o contiene _v_/_vw_)
  if echo "$cand" | grep -Eq '(_v($|_)|_vw($|_))'; then
    continue
  fi
  # O si existe como vista en MySQL, saltar
  if grep -Fxq "$cand" "$OUT/views_exist.txt"; then
    continue
  fi

  echo "$cand"
done < "$MISS" \
| sort -u > "$OUT/missing_tables.clean.txt"

echo "OK. Revisión terminada."
echo " - Expandido: $OUT/missing_tables.clean.txt"
[ -s "$OUT/missing_tables.ambiguous.txt" ] && echo " - Ambiguos:  $OUT/missing_tables.ambiguous.txt (revisar)"
