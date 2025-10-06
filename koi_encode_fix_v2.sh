#!/usr/bin/env sh
set -eu

# =========================
# KOI1 → UTF-8 Normalizer (v2, POSIX)
# - Detecta encoding (uchardet)
# - Convierte CP1252/Latin1 → UTF-8 (iconv)
# - Quita BOM
# - Genera reportes
# Uso:
#   sh koi_encode_fix_v2.sh --dry-run    # solo detecta y reporta
#   sh koi_encode_fix_v2.sh              # detecta, convierte y limpia BOM
# =========================

DRY_RUN="${1:-}"

need() { command -v "$1" >/dev/null 2>&1 || { echo "ERROR: falta $1"; exit 1; }; }
need find
need sed
need xargs
need iconv

if ! command -v uchardet >/dev/null 2>&1; then
  echo "Instalando uchardet..."
  # Asumimos Ubuntu/Debian y usuario root (por tu prompt)
  apt-get update -y >/dev/null 2>&1 || true
  apt-get install -y uchardet >/dev/null 2>&1 || {
    echo "No se pudo instalar uchardet automáticamente. Instálalo y reintenta."
    exit 1
  }
fi

# Extensiones a procesar
EXTS="php phtml html htm js css tpl inc"

STAMP="$(date +%F_%H%M%S)"
REPORT="encoding_detect_${STAMP}.txt"
CWD="$(pwd)"
BACKUP="/tmp/backup_pre_utf8_${STAMP}.tar.gz" # fuera del árbol => sin warning de tar

# 1) Indexar archivos
echo "Indexando archivos..."
# Listado en un archivo temporal para recorrerlo reliably (soporta espacios)
FILES_LIST="/tmp/koi_files_${STAMP}.lst"
: > "$FILES_LIST"
for e in $EXTS; do
  find . -type f -iname "*.$e" -print >> "$FILES_LIST"
done

TOTAL="$(wc -l < "$FILES_LIST" | tr -d ' ')"
[ "$TOTAL" -eq 0 ] && { echo "No se encontraron archivos a procesar."; exit 0; }

# 2) Dry-run: solo reporte
if [ "$DRY_RUN" = "--dry-run" ]; then
  : > "$REPORT"
  while IFS= read -r f; do
    enc=$(uchardet "$f" 2>/dev/null | tr -d '\r')
    printf "%-12s %s\n" "$enc" "$f" >> "$REPORT"
  done < "$FILES_LIST"
  echo "Dry-run completado. Revisa $REPORT"
  exit 0
fi

# 3) Backup del árbol (sin incluir /tmp)
echo "Creando backup en $BACKUP"
# Excluir .git y vendor grandes si querés agilizar:
# --exclude=.git --exclude=vendor
tar -czf "$BACKUP" --exclude="$BACKUP" --exclude="/tmp/*" .

# 4) Reporte inicial
echo "Generando reporte de encodings → $REPORT"
: > "$REPORT"
while IFS= read -r f; do
  enc=$(uchardet "$f" 2>/dev/null | tr -d '\r')
  printf "%-12s %s\n" "$enc" "$f" >> "$REPORT"
done < "$FILES_LIST"

# 5) Conversión a UTF-8
echo "Convirtiendo a UTF-8…"
CONVERT_LOG="convert_${STAMP}.log"
: > "$CONVERT_LOG"

while IFS= read -r f; do
  enc=$(uchardet "$f" 2>/dev/null | tr -d '\r')

  # Normalizaciones de uchardet
  case "$enc" in
    "ASCII"|"US-ASCII"|"UTF-8")
      # Igual removemos BOM si tuviera
      ;;
    "ISO-8859-1")
      enc="CP1252" # Heurística: suele ser CP1252 en proyectos legacy
      ;;
    ""|"unknown")
      # intentar CP1252
      enc="CP1252"
      ;;
  esac

  # Convertir si no es UTF-8/ASCII
  if [ "$enc" != "UTF-8" ] && [ "$enc" != "ASCII" ] && [ "$enc" != "US-ASCII" ]; then
    tmp="${f}.tmp.__utf8"
    if iconv -f "$enc" -t UTF-8 "$f" -o "$tmp" 2>>"$CONVERT_LOG"; then
      mv "$tmp" "$f"
      echo "[OK] $f ($enc → UTF-8)"
    else
      # Reintento CP1252 por si la detección falló
      rm -f "$tmp"
      if iconv -f CP1252 -t UTF-8 "$f" -o "$tmp" 2>>"$CONVERT_LOG"; then
        mv "$tmp" "$f"
        echo "[OK] $f (CP1252 → UTF-8)"
      else
        echo "[ERR] $f no se pudo convertir (ver $CONVERT_LOG)"
        rm -f "$tmp" || true
      fi
    fi
  fi

  # 6) Quitar BOM (si existiera) — sed lo hace aunque no tenga BOM
  # Nota: sed GNU requerido (Ubuntu lo tiene).
  sed -i '1s/^\xEF\xBB\xBF//' "$f"

done < "$FILES_LIST"

echo "Listo."
echo "Revisa:"
echo " - $REPORT (detección original)"
echo " - $CONVERT_LOG (errores de iconv si los hubo)"
echo "Backup en: $BACKUP"
