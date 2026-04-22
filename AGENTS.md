# KOI1 — AGENTS.md

## Proyecto
Sistema de gestión interno de **SPIRAL SHOES (Encinitas S.A.S.)**.  
Legacy PHP 5.6 corriendo en Docker en Ubuntu. Reemplaza al servidor Windows Server 2003 + SQL Server 2000 + PHP 5.2 que corre en `192.168.2.100`.

## Planes activos
Al iniciar cada sesión: leer todos los archivos en `plans/` con `status: active`
en su frontmatter para entender el estado actual antes de trabajar.

Usar `/update-plan` para actualizar el estado de los planes tras implementar.

## Repositorio
- **Git:** `git@github.com:jonyspiral/KOI.git`
- **Rama activa:** `main`
- **Ruta en servidor:** `/var/www/encinitas/`
- **Acceso Windows:** `Y:\var\www\encinitas\`

## Stack
- **PHP:** 5.6 (Apache 2.4, SAPI Apache 2.0 Handler)
- **DB:** MySQL 8 — host `192.168.2.210:3306`, BD `koi1_stage`, user `koiuser`
- **Cache:** Memcached en `localhost:11211`
- **Container:** Docker `koi1-php56` — puerto `8195:80` (Restaurado 21-Abr-2026, ver `koi_1_docker_php_5_v2.md`)
- **Proxy:** Nginx en host Ubuntu → `http://koi.spiralshoes.com/`

## Arquitectura de la app
```
.htaccess → master.php (router) → premaster.php → includes.php (autoload)
         → main.php (layout) → content/<pagename>.php (vista)
```

### Capa de datos
- `factory/Config.php` — constantes de configuración
- `factory/Factory.php` — singleton, instancia DbMysql
- `factory/Datos.php` — fachada estática (EjecutarSQL, EjecutarCommand, etc.)
- `factory/Mapper.php` — SQL por entidad + hidratación de objetos
- `factory/drivers/DbMysql.php` — driver único (mysqli), incluye shim T-SQL→MySQL
- `clases/` — 306 entidades de dominio (anémicas, lazy loads via Factory)

### Shim automático en DbMysql (conversiones T-SQL → MySQL)
El driver convierte automáticamente al ejecutar cualquier query:
- `ISNULL()` → `IFNULL()`
- `LEN()` → `CHAR_LENGTH()`
- `GETDATE()` → `NOW()`
- `[columna]` → `columna`
- `WITH (NOLOCK)` → (eliminado)
- `SELECT TOP n` → `SELECT ... LIMIT n`
- `@@IDENTITY` → `LAST_INSERT_ID()`

## Deploy
```bash
# En el servidor Ubuntu
cd /var/www/encinitas
git pull origin main
# No requiere restart — volumen montado, Apache sirve en caliente
```

## Testing
```bash
# Desde el host Ubuntu
curl -I http://127.0.0.1:8195/

# Desde dentro del container
docker exec -it koi1-php56 bash -lc "curl -i http://127.0.0.1/"

# Logs en vivo
docker exec -it koi1-php56 bash -lc "tail -n 100 /var/log/apache2/error.log"
docker exec -it koi1-php56 bash -lc "tail -n 100 /tmp/php_errors.log"
```

## Estado de módulos (actualizar al arreglar cada uno)
| Módulo | Estado | Notas |
|--------|--------|-------|
| Login | ✅ Funciona | |
| Catálogo / main | ✅ Funciona | |
| Favoritos (cliente) | ✅ Funciona | agregar/borrar/editarCurva/confirmarPedido |
| Clientes | ⚠️ Parcial | verificar listado y mayoristas |
| Stock producción | ⚠️ Parcial | vistas MySQL creadas, validar datos |
| Pedidos | ❓ Sin verificar | |
| Facturación | ❓ Sin verificar | |
| Cuenta corriente | ❓ Sin verificar | |
| Documentos | ❓ Sin verificar | |
| Cheques / Caja | ❓ Sin verificar | |

## Reglas de trabajo
- **No modificar** `factory/Config.php` sin avisar — tiene credenciales de BD
- **Un módulo a la vez** — arreglar, probar en browser, commitear, seguir
- **Commit por módulo arreglado** — mensajes descriptivos tipo `fix(favoritos): ...`
- **No commitear** archivos `.bak`, `test_*.php`, `*.tar.gz`, `tmp/`
- Los archivos en `tools/` son scripts de migración/diagnóstico, no tocar sin revisar

## .gitignore importante
Archivos que NO deben entrar al repo:
```
*.bak
*.tar.gz
tmp/
tools/vistas_out/*.bak
test_*.php
*_probe.php
```

## Contexto histórico
- La BD `koi1_stage` tiene **todas las tablas y vistas migradas** desde SQL Server 2000
- El código en `clases/` fue migrado masivamente de T-SQL a MySQL (ISNULL→IFNULL, etc.)
- El servidor original (`192.168.2.100`) sigue corriendo como backup — no apagarlo hasta validar todos los módulos
- `Transaction.php` tiene un bug: `driverName()` hace fallback a `'sqlsrv'` — pendiente corregir
