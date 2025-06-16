#!/bin/bash

echo "🔄 Sincronizando cambios desde koi2_v1 a koi2..."

# Rutas
ORIGEN="/var/www/koi2_v1"
DESTINO="/var/www/koi2"

# Sincronización incremental excluyendo carpetas sensibles
rsync -av --delete \
  --exclude 'vendor' \
  --exclude 'storage/logs' \
  --exclude '.env' \
  "$ORIGEN/" "$DESTINO/"

# Permisos
chown -R www-data:www-data "$DESTINO"
chmod -R 775 "$DESTINO/storage"
chmod -R 775 "$DESTINO/bootstrap/cache"

echo "✅ Sincronización incremental completa."
