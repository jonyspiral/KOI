#!/usr/bin/env bash
set -euo pipefail

# =========================
# KOI1 → UTF-8 Normalizer
# - Detecta encoding (uchardet)
# - Convierte CP1252/Latin1 → UTF-8 (iconv)
# - Quita BOM
# - Genera reportes
# Uso:
#   bash koi_encode_fix.sh --dry-run    # solo detecta y reporta
#   bash koi_encode_fix.sh              # detecta, convierte y limpia BOM
# =========================

DRY_RUN="${1:-}"

# 0) Requisitos
need() { command -v "$1" >/dev/null 2>&1 || { echo "ERROR: falta $1"; exit 1; }; }
need find
need xargs
need sed

if ! command -v uchardet >/dev/null 2>&1; then
  echo "Instalando uchardet..."
  sudo apt-get update -y && sudo apt-get install -y uchardet
fi
if ! command -v iconv >/dev/null 2>&1; then
  echo "Instalando iconv (gconv)..." && exit 1
fi

# 1) Extensiones a procesar (ajustar si hace falta)
EXTS=("php" "phtml" "html" "htm" "js" "css" "tpl" "inc")

# 2) Backup del árbol (una sola vez)
STAMP="$(date +%F_%H%M%S)"
BACKUP="backup_pre_utf8_${STAMP}.tar.gz"
if [ "$DRY_RUN" != "--dry-run" ]; then
  echo "Creando backup: $BACKUP"
  tar -czf "$BACKUP" .
fi

# 3) Listado de archivos
echo "Indexando archivos..."
MAPFILE -t FILES < <(find . -type f \( $(printf -- '-iname "*.%s" -o ' "${EXTS[@]}") -false \) -print0 | xargs -0 -I{} echo {})

TOTAL="${#FILES[@]}"
[ "$TOTAL" -eq 0 ] && { echo "No se encontraron archivos con las extensiones definidas."; exit 0; }

# 4) Reporte inicial de encodings
REPORT="encoding_detect_${STAMP}.txt"
echo "Generando reporte de encodings → $REPORT"
: > "$REPORT"
for f in "${FILES[@]}"; do
  enc=$(uchardet "$f" 2>/dev/null | tr -d '\r')
  printf "%-8s  %s\n" "$enc" "$f" >> "$REPORT"
done

# 5) Conversión (solo si no es dry-run)
convert_file() {
  local f="$1"
  local enc
  enc=$(uchardet "$f" 2>/dev/null | tr -d '\r')

  # Normalizaciones de uchardet
  case "$enc" in
    ASCII|US-ASCII) return 0 ;;           # ya compatible
    UTF-8)         return 0 ;;
    ISO-8859-1)    enc="CP1252" ;;        # heurística: proyectos .ar suelen ser CP1252
  esac

  # Conversión segura → UTF-8
  tmp="${f}.tmp.__utf8"
  if iconv -f "$enc" -t UTF-8 "$f" -o "$tmp" 2>>convert_errors.log; then
    mv "$tmp" "$f"
    echo "[OK] $f ($enc → UTF-8)"
  else
    echo "[WARN] Falló iconv con $enc en $f. Reintentando CP1252..."
    rm -f "$tmp"
    if iconv -f CP1252 -t UTF-8 "$f" -o "$tmp" 2>>convert_errors.log; then
      mv "$tmp" "$f"
      echo "[OK] $f (CP1252 → UTF-8)"
    else
      echo "[ERR] No se pudo convertir $f (ver convert_errors.log)"
      rm -f "$tmp"
    fi
  fi
}

# 6) Quitar BOM si existiera
remove_bom() {
  local f="$1"
  # Si comienza con EF BB BF, quitar
  if [ "$(xxd -p -l 3 "$f" 2>/dev/null)" = "efbbbf" ]; then
    # sed GNU: eliminar BOM en primera línea
    sed -i '1s/^\xEF\xBB\xBF//' "$f"
    echo "[BOM-] $f"
  fi
}

# 7) Ejecutar
if [ "$DRY_RUN" = "--dry-run" ]; then
  echo "Dry-run completado. Revisa $REPORT"
  exit 0
fi

echo "Convirtiendo a UTF-8…"
for f in "${FILES[@]}"; do
  convert_file "$f"
done

echo "Eliminando BOM (si hubiera)…"
for f in "${FILES[@]}"; do
  remove_bom "$f"
done

echo "Listo. Revisa:"
echo " - $REPORT (detección original)"
echo " - convert_errors.log (si hubo errores)"
echo " Sugerido: commit en git con mensaje 'chore: normaliza encoding a UTF-8'"
