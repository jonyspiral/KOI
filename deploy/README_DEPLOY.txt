# KOI2 - Deploy a Producción

Este script realiza:
- Copia total del código desde `koi2_v1` a `koi2`, sin sobrescribir `.env`.
- Instalación de dependencias Laravel.
- Cacheo de configuración.
- Clonado selectivo de tablas desde la base `koi2_v1` hacia `koi2`, basándose en el archivo `sync_allowed_tables.txt`.

## Cómo usar

1. Copiar el contenido a `/var/www/koi2/deploy/`
2. Dar permisos de ejecución:
   chmod +x deploy_koi2.sh
3. Ejecutar el script:
   ./deploy_koi2.sh

## Personalización

Agregar más nombres de tabla a `sync_allowed_tables.txt` para incluirlas en la sincronización.
