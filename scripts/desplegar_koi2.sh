#!/bin/bash

echo "🚀 Copiando KOI2 desde koi2_v1 a koi2 (producción)..."

# Rutas
ORIGEN="/var/www/koi2_v1"
DESTINO="/var/www/koi2"

# Crear destino si no existe
mkdir -p "$DESTINO"

# Copiar archivos (excepto carpetas sensibles)
rsync -av --exclude 'vendor' --exclude 'storage/logs' --exclude '.env' "$ORIGEN/" "$DESTINO/"

# Dar permisos
chown -R www-data:www-data "$DESTINO"
chmod -R 775 "$DESTINO/storage"
chmod -R 775 "$DESTINO/bootstrap/cache"

echo "✅ Copia completada."

# Recordatorio
echo "👉 Recordá configurar el archivo .env en $DESTINO"
