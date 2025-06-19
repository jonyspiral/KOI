#!/bin/bash

echo "🚀 Deploy total de código KOI2 y sincronización selectiva de BD"

cd /var/www/koi2 || exit 1

echo "📥 Actualizando código desde desarrollo (excepto .env)..."
rsync -av --exclude='.env' /var/www/koi2_v1/ /var/www/koi2/

echo "🔧 Instalando dependencias Laravel..."
composer install --no-dev --optimize-autoloader

echo "🧹 Limpiando y cacheando configuración..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache

echo "📄 Leyendo lista de tablas permitidas para sincronizar..."
TABLAS=$(cat /var/www/koi2/deploy/sync_allowed_tables.txt)

for tabla in $TABLAS; do
  echo "   ↪️ Clonando tabla: $tabla"
  mysqldump -u jony -pRoute667 koi2_v1 "$tabla" | mysql -u jony -pRoute667 koi2
done

echo "✅ Deploy completo. KOI2 actualizado y sincronizado selectivamente."
