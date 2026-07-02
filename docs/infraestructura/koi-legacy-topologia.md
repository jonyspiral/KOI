# KOI legacy - topologia operativa y harness

Fecha de actualizacion: 2026-07-02

Este documento es la fuente de verdad unica para identificar:
- produccion KOI legacy
- rollback KOI legacy (baseline)
- limites operativos del harness

Fuente estructurada principal del harness local:
- `.harness/project.yml`
- Este documento es narrativa operativa de soporte y debe mantenerse alineado con `.harness/project.yml`.

## Identidad del sistema
- Sistema: KOI legacy
- Repo local Windows: `C:\dev\koi`
- Repo servidor: `/var/www/koi`
- Host: `192.168.2.210`
- Sistema operativo host: Ubuntu Server 20.04
- GitHub: `https://github.com/jonyspiral/KOI.git`
- Rama: `main`
- Commit de referencia confirmado: `2add1fd0843673ebf69f944dbaecb4d03797154f`

## Topologia compacta
| Item | KOI Produccion | KOI Rollback (alias permitido: baseline) |
|---|---|---|
| Contenedor Docker | `koi1-php56` | `koi1-php56_test` |
| Imagen | `koi1-php56:local` | `koi1-php56:local` |
| Puerto host -> contenedor | `127.0.0.1:8196 -> 80` | `0.0.0.0:8195 -> 80` |
| Mount host -> contenedor | `/var/www/koi -> /var/www/encinitas` | `/var/www/koi_rollback_20260702_094857 -> /var/www/encinitas` |
| Base de datos | `koi1_prod` | `koi1_stage` |
| Config runtime | `/var/www/koi/factory/Config.php` | no tocar para este documento |
| URL publica | `https://koi.spiralshoes.com/` | sin exposicion publica intencional |
| URL LAN | `http://192.168.2.210:8196/` | `http://192.168.2.210:8195/` |
| Enrutamiento publico | Nginx publico -> `http://127.0.0.1:8196` | no aplica |

## Reglas operativas obligatorias
1. No ejecutar `git pull`, `merge`, `reset` ni `checkout` en `/var/www/koi` sin autorizacion explicita.
2. No modificar Nginx publico, certificados, DNS o MikroTik sin autorizacion explicita.
3. No tocar `/var/www/koi_rollback_20260702_094857`.
4. No usar `koi1_stage` como fuente para decisiones o restauraciones de produccion.
5. No versionar `factory/Config.php` ni archivos `*.bak` de produccion.
6. No iniciar contenedores historicos detenidos con mounts antiguos.
7. Produccion publica entra por Nginx y llega a `127.0.0.1:8196`.
8. El contenedor de rollback debe quedar disponible solo como referencia LAN en `8195`.

## Nombres obsoletos / compatibilidad
- `/var/www/encinitas` en host: obsoleto como ruta operativa del host.
- `/var/www/encinitas_test_runtime` en host: obsoleto como ruta operativa del host.
- `koi1-php56-test`: obsoleto como nombre vigente de contenedor.
- `test` como nombre principal del entorno rollback: obsoleto; usar `KOI Rollback` o `baseline`.
- Compatibilidad interna permitida: dentro de contenedores legacy se mantiene `/var/www/encinitas` porque el codigo lo espera.

## Bloque de variables para harness (sin secretos)
Este bloque no agrega credenciales; define identificacion y limites operativos.
Los datos estructurados y versionables del harness viven en `.harness/project.yml`.

```env
KOI_LEGACY_HOST=192.168.2.210
KOI_LEGACY_REPO_LOCAL=C:\dev\koi
KOI_LEGACY_REPO_SERVER=/var/www/koi
KOI_LEGACY_REF_COMMIT=2add1fd0843673ebf69f944dbaecb4d03797154f

KOI_LEGACY_PROD_CONTAINER=koi1-php56
KOI_LEGACY_PROD_IMAGE=koi1-php56:local
KOI_LEGACY_PROD_HOST_BIND=127.0.0.1:8196
KOI_LEGACY_PROD_CONTAINER_PORT=80
KOI_LEGACY_PROD_MOUNT_HOST=/var/www/koi
KOI_LEGACY_PROD_MOUNT_CONTAINER=/var/www/encinitas
KOI_LEGACY_PROD_DB=koi1_prod
KOI_LEGACY_PROD_URL_PUBLIC=https://koi.spiralshoes.com/
KOI_LEGACY_PROD_URL_LAN=http://192.168.2.210:8196/

KOI_LEGACY_ROLLBACK_CONTAINER=koi1-php56_test
KOI_LEGACY_ROLLBACK_IMAGE=koi1-php56:local
KOI_LEGACY_ROLLBACK_HOST_BIND=0.0.0.0:8195
KOI_LEGACY_ROLLBACK_CONTAINER_PORT=80
KOI_LEGACY_ROLLBACK_MOUNT_HOST=/var/www/koi_rollback_20260702_094857
KOI_LEGACY_ROLLBACK_MOUNT_CONTAINER=/var/www/encinitas
KOI_LEGACY_ROLLBACK_DB=koi1_stage
KOI_LEGACY_ROLLBACK_URL_LAN=http://192.168.2.210:8195/
```

## Comandos seguros de verificacion
Comandos de solo lectura para validar topologia y estado del harness.

```bash
# 1) confirmar contenedores, puertos e imagen
sudo docker ps --format 'table {{.Names}}\t{{.Image}}\t{{.Ports}}'

# 2) inspeccionar mounts de produccion y rollback
sudo docker inspect koi1-php56 --format '{{json .Mounts}}'
sudo docker inspect koi1-php56_test --format '{{json .Mounts}}'

# 3) validar que Nginx apunte a 127.0.0.1:8196 (lectura)
sudo nginx -T | grep -n '127.0.0.1:8196'

# 4) validar respuesta HTTP local de ambos endpoints
curl -I http://127.0.0.1:8196/
curl -I http://192.168.2.210:8195/

# 5) validar referencia de commit en el repo local del servidor
cd /var/www/koi && git rev-parse HEAD
```

## ODBC y Access (resumen narrativo)
Los perfiles ODBC cliente documentados, sin password en repo, estan definidos en `.harness/project.yml`:
- `KOI1_MYSQL_PROD_RW` (uso operativo contra `koi1_prod`)
- `KOI1_MYSQL_STAGE_RW` (uso baseline/rollback contra `koi1_stage`)

Check local de preflight (solo lectura, sin crear DSN):
- `.harness/checks/verify-odbc-preflight.ps1`

## Alcance excluido
- KOI2 Laravel no forma parte de este harness.
- Este documento no autoriza cambios de infraestructura ni de produccion.
- Este documento no debe incluir credenciales ni secretos.
