#!/usr/bin/env bash
set -euo pipefail

# ------------------------------------------------------------
# make_arca_csr.sh
# Genera KEY + CSR para ARCA (ex-AFIP) usando OpenSSL.
# Uso:
#   ./make_arca_csr.sh homo 30716182815 "ENCINITAS SAS"
#   ./make_arca_csr.sh prod 30716182815 "ENCINITAS SAS"
#
# Parámetros:
#   1) ENV  : homo | prod   (default: homo)
#   2) CUIT : 11 dígitos, sin guiones (obligatorio)
#   3) ORG  : Razón social (default: ENCINITAS SAS)
# ------------------------------------------------------------

ENV="${1:-homo}"
CUIT="${2:?Pasá el CUIT sin guiones, ej 30716182815}"
ORG="${3:-ENCINITAS SAS}"

if [[ "$ENV" != "homo" && "$ENV" != "prod" ]]; then
  echo "ERROR: ENV debe ser 'homo' o 'prod'." >&2
  exit 1
fi

if ! [[ "$CUIT" =~ ^[0-9]{11}$ ]]; then
  echo "ERROR: CUIT inválido. Debe tener exactamente 11 dígitos." >&2
  exit 1
fi

# Directorio base = carpeta donde está este script
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BASE="${ROOT}/${ENV}"
CONF="${ROOT}/openssl_${ENV}.cnf"

mkdir -p "$BASE"
chmod 700 "$BASE"

# Si no existe el .cnf, generar uno mínimo
if [[ ! -f "$CONF" ]]; then
  CN="KOI2 ARCA $( [[ "$ENV" == "homo" ]] && echo "HOMO" || echo "PROD" )"
  cat > "$CONF" <<EOF
[ req ]
default_bits       = 2048
default_md         = sha256
prompt             = no
encrypt_key        = no
distinguished_name = dn
req_extensions     = req_ext

[ dn ]
C=AR
O=${ORG}
CN=${CN}
serialNumber=CUIT ${CUIT}

[ req_ext ]
basicConstraints = CA:FALSE
keyUsage         = digitalSignature, keyEncipherment
extendedKeyUsage = clientAuth
EOF
fi

# Asegurar que en el .cnf queden ORG/CN/CUIT correctos (sin tocar el original permanentemente)
CN="KOI2 ARCA $( [[ "$ENV" == "homo" ]] && echo "HOMO" || echo "PROD" )"
TMP_CONF="$(mktemp)"
# Notas: reemplaza líneas O=, CN= y serialNumber= (si existen) y preserva el resto del archivo
sed -E \
  -e "s/^O=.*/O=${ORG}/" \
  -e "s/^CN=.*/CN=${CN}/" \
  -e "s/^serialNumber=.*/serialNumber=CUIT ${CUIT}/" \
  "$CONF" > "$TMP_CONF"

KEY_PATH="${BASE}/priv.key"
CSR_PATH="${BASE}/solicitud.csr"

# No sobrescribimos la clave si ya existe (por seguridad)
if [[ -f "$KEY_PATH" ]]; then
  echo "ERROR: Ya existe ${KEY_PATH}. Borralo o movelo si querés regenerar." >&2
  rm -f "$TMP_CONF"
  exit 1
fi

# 1) Generar clave privada
openssl genpkey -algorithm RSA -pkeyopt rsa_keygen_bits:2048 -out "$KEY_PATH"
chmod 600 "$KEY_PATH"

# 2) Generar CSR con el TMP_CONF
openssl req -new -key "$KEY_PATH" -out "$CSR_PATH" -config "$TMP_CONF"
chmod 644 "$CSR_PATH"
rm -f "$TMP_CONF"

echo "✔ KEY: $KEY_PATH"
echo "✔ CSR: $CSR_PATH"
echo
echo "➡ Subí el CSR al portal de ARCA del CUIT ${CUIT} para obtener el certificado (.crt/.pem)."
echo "   Luego guardá el cert como:"
echo "   - ${ROOT}/homo/cert.crt   (homologación)"
echo "   - ${ROOT}/prod/cert.crt   (producción)"
echo
echo "Verificación rápida del CSR:"
echo "  openssl req -in \"$CSR_PATH\" -noout -text | grep -E \"Subject:|serialNumber\""
