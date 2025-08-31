#!/usr/bin/env bash
set -euo pipefail

DB="${1:-koi1_stage}"
SQL_IN="${2:-/var/www/encinitas/tmp/mysql_views_auto.sql}"
FAILLOG="${3:-/var/www/encinitas/tmp/views_fail.log}"

OUTDIR="/var/www/encinitas/tmp"
mkdir -p "$OUTDIR"

tmp="$(mktemp -d)"; trap 'rm -rf "$tmp"' EXIT

# --- Sanity
[ -s "$SQL_IN" ] || { echo "No existe SQL: $SQL_IN" >&2; exit 1; }

# --- 0) Aplanar SQL: quitar comentarios y colapsar espacios/nuevas líneas
#     (solo para extracción; NO modifica el archivo original)
perl -0777 -pe '
  s/--[^\n]*\n/ /g;                    # comentarios -- hasta fin de línea
  s#/\*.*?\*/# #gs;                    # comentarios /* ... */
  s/`/"/g;                             # homogeneizar quotes
  s/[\r\n]+/ /g;                       # colapsar saltos
  s/\s+/ /g;                           # colapsar espacios
' "$SQL_IN" > "$tmp/sql_flat.sql"

# --- 1) Candidatos desde FROM/JOIN (en el SQL ya aplanado)
# Capturamos el token que sigue a FROM/JOIN, con o sin schema, con comillas opcionales
grep -Poi '\b(from|join)\s+("?[A-Za-z0-9_]+"?(?:\."?[A-Za-z0-9_]+"?)?)' "$tmp/sql_flat.sql" \
| awk '{print $2}' \
| sed -E 's/^"|"$//g; s/"\."/"."/g' \
| awk -F'.' '{print $NF}' \
| sed -E 's/[",;]$//' \
| sed -E 's/[^A-Za-z0-9_].*$//' \
| sed -E '/^[[:space:]]*$/d' \
> "$tmp/cand_from_sql.txt" || true

# --- 2) Candidatos desde errores del apply (si existe)
: > "$tmp/cand_from_err.txt"
if [ -s "$FAILLOG" ]; then
  # Unknown table 'X'
  grep -oE "Unknown table '([^']+)'" "$FAILLOG" \
    | sed -E "s/.*'([^']+)'.*/\1/" >> "$tmp/cand_from_err.txt" || true
  # Table 'schema.X' doesn't exist
  grep -oE "Table '"$DB"\.([^']+)'.*doesn.t exist" "$FAILLOG" \
    | sed -E "s/.*'"$DB"\.([^']+)'.*/\1/" >> "$tmp/cand_from_err.txt" || true
  # Mensajes truncados con coma/; al final
  sed -i -E 's/[;,]+$//' "$tmp/cand_from_err.txt"
fi

# --- 3) Unir universo de candidatos y limpiar ruidos comunes
cat "$tmp/cand_from_sql.txt" "$tmp/cand_from_err.txt" \
  | tr 'A-Z' 'a-z' \
  | sed -E '/^[[:space:]]*$/d' \
  | sed -E '/^(a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z)$/d' \
  | sed -E '/^(and|or|on|as|in|is|no|si|with|inner|left|right|outer|where|group|order|limit)$/d' \
  | sed -E 's/[",;]+$//' \
  | sed -E 's/[^a-z0-9_].*$//' \
  | sed -E '/^[[:space:]]*$/d' \
  | sort -u > "$tmp/candidates_all.txt"

# --- 4) Existentes (tablas y vistas) en MySQL
mysql -N -e "SELECT table_name FROM information_schema.tables
             WHERE table_schema='${DB}' AND table_type='BASE TABLE';" \
  | tr 'A-Z' 'a-z' | sort -u > "$tmp/exist_tables.txt"

mysql -N -e "SELECT table_name FROM information_schema.views
             WHERE table_schema='${DB}';" \
  | tr 'A-Z' 'a-z' | sort -u > "$tmp/exist_views.txt"

# --- 5) Faltantes (ni tabla ni vista)
grep -Fvx -f "$tmp/exist_tables.txt" "$tmp/candidates_all.txt" \
  | grep -Fvx -f "$tmp/exist_views.txt" \
  > "$OUTDIR/missing_objects_all.txt" || true

# --- 6) Solo tablas (descartar nombres que parecen vistas)
sed -E '/(_v|_vw|_view|_vista)$/d' "$OUTDIR/missing_objects_all.txt" \
  > "$OUTDIR/missing_tables.txt" || true

echo "OK."
echo " - Objetos faltantes: $OUTDIR/missing_objects_all.txt"
echo " - Tablas (para artisan): $OUTDIR/missing_tables.txt"
echo
echo "Preview de tablas (primeras 60):"
nl -ba "$OUTDIR/missing_tables.txt" | sed -n '1,60p'
