#!/usr/bin/env bash
set -euo pipefail

# ===========================
# KOI — Mass Fix (PHP 5.6 + MySQL 8)
# Enfocado en: factory, content, clases, js y archivos raíz
# Hace:
#   - Filtra SOLO archivos con T-SQL en carpetas foco
#   - Aplica reemplazos SEGUROS (NOLOCK, GETDATE, ISNULL, LEN, [], DATEDIFF)
#   - NO toca TOP→LIMIT (solo reporta)
#   - Reportes y backups .bak
#   - Commits pequeños y reversibles
# ===========================

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

# --- prereqs
if ! command -v rg >/dev/null 2>&1; then
  echo "Instalando ripgrep (rg)…"
  sudo apt-get update -y && sudo apt-get install -y ripgrep
fi

mkdir -p tools/reports

echo "===> 0) Crear rama de trabajo"
git rev-parse --is-inside-work-tree >/dev/null 2>&1 || { echo "No es un repo git"; exit 1; }
git checkout -b migracion/php56-mysql8-active-$(date +%Y%m%d-%H%M%S)

echo "===> 1) Definir carpetas/archivos foco"
cat > tools/active_dirs.txt <<'EOF'
factory
content
clases
js
.htaccess
master.php
premaster.php
main.php
login.php
EOF

echo "===> 2) Detectar T-SQL en foco (lista de archivos)"
rg -n --hidden --glob '!vendor' \
   -e 'WITH\s*\(\s*NOLOCK' \
   -e '\bGETDATE\s*\(' \
   -e '\bISNULL\s*\(' \
   -e '\bLEN\s*\(' \
   -e 'SELECT\s+TOP\s+\d+' \
   -e 'DATEDIFF\s*\(\s*day' \
   -e '\[[^\]]+\]' \
   | awk -F: '{print $1}' \
   | sort -u \
   | grep -E -f <(sed 's#^#^#; s#$#(/|$)#' tools/active_dirs.txt) \
   > tools/reports/tsql_hits_active_files.txt || true

COUNT=$(wc -l < tools/reports/tsql_hits_active_files.txt || echo 0)
echo "    Archivos con T-SQL en foco: $COUNT"
if [ "$COUNT" -eq 0 ]; then
  echo "    No hay archivos a tocar. Saliendo."
  exit 0
fi

echo "===> 3) Resumen por subcarpeta (para priorizar)"
awk -F/ '{print $1"/"$2"/"$3}' tools/reports/tsql_hits_active_files.txt \
 | sed 's#/$##' | sort | uniq -c | sort -nr \
 > tools/reports/tsql_hits_active_summary.txt
echo "    → tools/reports/tsql_hits_active_summary.txt"

echo "===> 4) (Opcional) Reducir a escrituras (INSERT/UPDATE) — reporte"
rg -n --hidden --glob '!vendor' -e 'INSERT\s+INTO\s+\w+' -e 'UPDATE\s+\w+' \
  -f tools/reports/tsql_hits_active_files.txt \
  > tools/reports/writes_active.txt || true
echo "    → tools/reports/writes_active.txt (para revisión manual)"

echo "===> 5) Aplicar REEMPLAZOS SEGUROS (con backup .bak)"
# 5.1 WITH (NOLOCK) → eliminar
xargs -a tools/reports/tsql_hits_active_files.txt -I{} \
  sed -i.bak -E 's/\s+WITH\s*\(\s*NOLOCK\s*\)//g' "{}"

# 5.2 GETDATE() → NOW()
xargs -a tools/reports/tsql_hits_active_files.txt -I{} \
  sed -i.bak -E 's/\bGETDATE\s*\(\s*\)/NOW()/g' "{}"

# 5.3 ISNULL( → IFNULL(
xargs -a tools/reports/tsql_hits_active_files.txt -I{} \
  sed -i.bak -E 's/\bISNULL\s*\(/IFNULL(/g' "{}"

# 5.4 LEN( → CHAR_LENGTH(
xargs -a tools/reports/tsql_hits_active_files.txt -I{} \
  sed -i.bak -E 's/\bLEN\s*\(/CHAR_LENGTH(/g' "{}"

# 5.5 [col] → col
xargs -a tools/reports/tsql_hits_active_files.txt -I{} \
  sed -i.bak -E 's/\[([^\]]+)\]/\1/g' "{}"

# 5.6 DATEDIFF(day, a, b) → DATEDIFF(b, a)
xargs -a tools/reports/tsql_hits_active_files.txt -I{} \
  sed -i.bak -E 's/\bDATEDIFF\s*\(\s*day\s*,\s*([^,]+)\s*,\s*([^)]+)\)/DATEDIFF(\2, \1)/gi' "{}"

echo "===> 6) Verificación post-fix (pendientes)"
rg -n --hidden --glob '!vendor' \
   -e 'WITH\s*\(\s*NOLOCK' \
   -e '\bGETDATE\s*\(' \
   -e '\bISNULL\s*\(' \
   -e '\bLEN\s*\(' \
   -e 'DATEDIFF\s*\(\s*day' \
   -e '\[[^\]]+\]' \
   -f tools/reports/tsql_hits_active_files.txt \
   > tools/reports/tsql_hits_remaining.txt || true
REMAIN=$(wc -l < tools/reports/tsql_hits_remaining.txt || echo 0)
echo "    Restantes: $REMAIN"
echo "    → tools/reports/tsql_hits_remaining.txt"

echo "===> 7) TOP n (SOLO reporte; conversión manual si no pasan por el driver)"
rg -n --hidden --glob '!vendor' 'SELECT\s+TOP\s+\d+' \
  -f tools/reports/tsql_hits_active_files.txt \
  > tools/reports/top_hits_active.txt || true
echo "    → tools/reports/top_hits_active.txt"

echo "===> 8) PHP 5.6 — null coalesce (??) SOLO en foco"
rg -n --hidden --glob '!vendor' '\?\?' \
   -f tools/reports/tsql_hits_active_files.txt \
   > tools/reports/php56_null_coalesce_hits.txt || true
echo "    → tools/reports/php56_null_coalesce_hits.txt (hacer manual/semimanual)"

echo "===> 9) Commit checkpoint"
git add -A
git commit -m "mass(active): T-SQL→MySQL (NOLOCK/GETDATE/ISNULL/LEN/[]/DATEDIFF) en factory/content/clases/js/raíz"

echo "✅ Listo.
- Revisá: tools/reports/* (resúmenes, pendientes, TOP y ??)
- Probá flows críticos (cobranza, seguimiento, login).
- Si algo no te gusta:  git reset --hard HEAD~1
"
