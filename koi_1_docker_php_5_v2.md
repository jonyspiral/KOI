# KOI1 – Docker PHP 5.6 (Documentación v2 — Restauración)

> **Estado: ACTIVO — 2026-04-21**
> **Referencia base**: [[koi_1_docker_php_5.md]](file:///c:/dev/encinitas/koi_1_docker_php_5.md)

## 1) Resumen de la Sesión
En esta sesión se restauró el entorno de prueba de KOI1 que se encontraba fuera de servicio debido a la falta del contenedor principal en el servidor Ubuntu (`192.168.2.210`).

### Detalles del Entorno Restaurado
- **Contenedor**: `koi1-php56`
- **Imagen utilizada**: `koi1-php56:local` (ID: `7050c64196b4`)
- **Puerto Host**: `8195`
- **Puerto Contenedor**: `80`
- **Volumen**: `/var/www/encinitas` -> `/var/www/encinitas`

## 2) Comandos de Recuperación
Para futuras referencias, si el contenedor desaparece, el comando exacto de restauración es:

```bash
docker run -d \
  --name koi1-php56 \
  -p 8195:80 \
  -v /var/www/encinitas:/var/www/encinitas \
  --restart always \
  koi1-php56:local
```

## 3) Verificación de Salud
- **Acceso Web**: [http://192.168.2.210:8195/](http://192.168.2.210:8195/) — **OK**
- **Estado Docker**: `Up` (verificado con `docker ps`)
- **Logs**: No se observan errores críticos en la salida inicial de Apache.

## 4) Próximos Pasos
- Continuar con la validación de los módulos pendientes (Clientes, Stock producción, Pedidos) según el [[migracion-docker-mysql.md]](file:///c:/dev/encinitas/plans/migracion-docker-mysql.md).
