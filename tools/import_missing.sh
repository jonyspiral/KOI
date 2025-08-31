#!/usr/bin/env bash
set -euo pipefail

ARTISAN="/var/www/koi/artisan"   # <-- CAMBIA esto si tu proyecto Laravel está en otra carpeta
if [ ! -f "$ARTISAN" ]; then
  echo "No encuentro $ARTISAN. Ajustá la ruta y reintentá." >&2
  exit 1
fi

while IFS= read -r t; do
  [ -z "$t" ] && continue
  echo "=== Importando $t ==="
  php "$ARTISAN" importar:tabla "$t" \
    --connection=sqlsrv_koi \
    --to=mysql_k1 \
    --schema=koi1_stage \
    --mirror \
    --stage-only \
    --money-precision=19,4 \
  || { echo "ERROR en $t"; exit 1; }
done < /var/www/encinitas/tmp/missing_tables.txt

echo "Listo."
