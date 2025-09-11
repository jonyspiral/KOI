# KOI1 – Docker PHP 5.6 (documentación de instalación y contenido)

> **Estado: CERRADO — 2025-09-11** · Documento congelado. Para editar, crear una nueva versión (v2) y referenciar este como base.

> **Objetivo**: documentar de forma reproducible **cómo está instalado** el contenedor de PHP 5.6 y **qué contiene** por dentro. Todo lo que figura como *verificado* proviene de comandos ejecutados durante esta sesión; el resto incluye comandos exactos para comprobar/extraer la información en tu entorno.

---

## 1) Resumen (estado actual verificado)

- **Host**: Ubuntu con **nginx 1.18** al frente (reverse proxy).
- **Contenedor**: `koi1-php56` (imagen `koi1-php56:local`).
- **Puertos**: host **8195** → contenedor **80/tcp**.
- **Servidor web** (dentro del contenedor): **Apache/2.4.25 (Debian)**, **PHP 5.6** (SAPI **Apache 2.0 Handler**).
- **VHost activo**: `/etc/apache2/sites-enabled/encinitas.conf` con `DocumentRoot /var/www/encinitas`.
- **DocRoot por defecto**: `/var/www/html` (no es el que sirve el vhost). Se creó symlink: `/var/www/html/content → /var/www/encinitas/content` (solo para pruebas).
- **PHP ini principal**: *no hay archivo único*, se usan *drop-ins* en `/usr/local/etc/php/conf.d/`.
- **Override propio**: `/usr/local/etc/php/conf.d/zzz-koi-override.ini` (cargado) con `always_populate_raw_post_data=-1`, `display_errors=Off`, `log_errors=On`, `error_log=/tmp/php_errors.log`, etc.
- **Extensiones PHP** vistas: `mysqli`, `pdo_mysql`, `gd`, `zip` (entre otras).
- **Logs** usuales: Apache en `/var/log/apache2/error.log`; PHP (override) en `/tmp/php_errors.log`; endpoints: `/tmp/php_favoritos.log`, `/tmp/error_favoritos_shutdown.log`.

---

## 2) Cómo se instaló (origen de la imagen / despliegue)

> Si no recordás el procedimiento exacto, estos comandos **extraen** la información desde Docker.

```bash
# En el host
# Imagen, fecha de creación y tamaño
docker images koi1-php56:local

# Historia de la imagen (capas → pista del Dockerfile base)
docker history koi1-php56:local

# Contenedores en ejecución (confirma el mapeo de puertos)
docker ps --format 'table {{.Names}}\t{{.Image}}\t{{.Ports}}\t{{.Status}}'

# Inspección completa (montajes, redes, env, cmd)
docker inspect koi1-php56 > /tmp/koi1-php56.inspect.json

# Montajes (resumen legible)
docker inspect -f '{{json .Mounts}}' koi1-php56 | jq .

# Variables de entorno definidas en el contenedor
docker inspect -f '{{json .Config.Env}}' koi1-php56 | jq .

# Si se usa Compose, ver proyectos
docker compose ls 2>/dev/null || docker-compose ls 2>/dev/null || true
```

> **Tip**: si existe un `docker-compose.yml` o `Dockerfile` en tu repo, archívalos junto a esta guía.

---

## 3) Red y puertos

```bash
# En el host: confirmar el puerto publicado
docker port koi1-php56

# Prueba desde el host a través de nginx (reverse):
# ¡Ojo! http://127.0.0.1 (sin puerto) pega al nginx del host y puede redirigir a HTTPS.
# Usar el puerto publicado para llegar al contenedor:
curl -i http://127.0.0.1:8195/

# Dentro del contenedor: prueba directa al Apache interno
docker exec -it koi1-php56 bash -lc "curl -i http://127.0.0.1/"
```

**Gotcha**: Si ves `301 Moved Permanently` desde el host, probablemente responda nginx del host. Para hablar con el contenedor, usa `:8195` o ejecuta `curl` **dentro** del contenedor.

---

## 4) Apache dentro del contenedor

```bash
# VirtualHosts y DocumentRoot activo
docker exec -it koi1-php56 bash -lc 'apache2ctl -S; grep -n "DocumentRoot" /etc/apache2/sites-enabled/*'

# Ver contenido del vhost
docker exec -it koi1-php56 bash -lc 'sed -n "1,200p" /etc/apache2/sites-enabled/encinitas.conf'

# Módulos habilitados
docker exec -it koi1-php56 bash -lc 'apache2ctl -M | sort'

# Versión y paths relevantes
docker exec -it koi1-php56 bash -lc 'apache2ctl -V'
```

**Rutas proyecto**:

- `/var/www/encinitas` (raíz del sitio servido por el vhost).
- `/var/www/html` (DocRoot por defecto de Apache; no es el vhost principal).

**Reinicio / reload**:

```bash
docker exec -it koi1-php56 bash -lc 'apache2ctl -k graceful || apache2ctl -k restart'
```

---

## 5) PHP dentro del contenedor

```bash
# Versión y SAPI
docker exec -it koi1-php56 bash -lc 'php -v'

# Dónde busca .ini y cuáles carga
docker exec -it koi1-php56 bash -lc 'php -i | grep -E "Scan this dir|Additional .ini files parsed|Loaded Configuration File"'

# Listado de drop-ins .ini
docker exec -it koi1-php56 bash -lc 'ls -1 /usr/local/etc/php/conf.d'

# Mostrar nuestro override (confirmar valores efectivos)
docker exec -it koi1-php56 bash -lc 'sed -n "1,200p" /usr/local/etc/php/conf.d/zzz-koi-override.ini'

# Extensiones cargadas
docker exec -it koi1-php56 bash -lc 'php -m | sort'

# Parámetros sensibles a nuestro caso
docker exec -it koi1-php56 bash -lc 'php -i | grep -E "always_populate_raw_post_data|display_errors|error_log|output_buffering|short_open_tag|session.save_path"'
```

**Nota**: En esta imagen, `Loaded Configuration File` aparece como **(none)**; es normal. Toda la configuración se aplica vía archivos en `conf.d/`.

---

## 6) Logs

```bash
# Apache (error y access)
docker exec -it koi1-php56 bash -lc 'tail -n 200 /var/log/apache2/error.log'
docker exec -it koi1-php56 bash -lc 'tail -n 200 /var/log/apache2/access.log'

# PHP (según override)
docker exec -it koi1-php56 bash -lc 'tail -n 200 /tmp/php_errors.log 2>/dev/null || echo "(sin php_errors.log)"'

# Logs específicos usados por endpoints
# Favoritos (fatal en shutdown y app log)
docker exec -it koi1-php56 bash -lc 'tail -n 200 /tmp/error_favoritos_shutdown.log 2>/dev/null || echo "(sin shutdown.log)"'
docker exec -it koi1-php56 bash -lc 'tail -n 200 /tmp/php_favoritos.log 2>/dev/null || echo "(sin php_favoritos.log)"'
```

**Tip**: si `tail` parece “colgar”, es habitual con archivos FIFO o I/O lenta; agregá `-n +1` o `head` para validar que el descriptor exista.

---

## 7) Estructura del proyecto y endpoints clave

**Árbol mínimo**:

```
/var/www/encinitas/
  ├─ content/
  │   ├─ cliente/favoritos/agregarVarios.php
  │   └─ api/
  │       ├─ funciones.php
  │       └─ stock_produccion.php (llama a funciones.php)
  ├─ factory/
  │   ├─ drivers/DbMysql.php
  │   └─ ... (Datos.php, Factory.php, etc.)
  └─ premaster.php
```

**Pruebas rápidas**:

```bash
# Dentro del contenedor (sin sesión de usuario: debería devolver 403, NO 500)
curl -i -X POST -H 'Content-Type: application/json' \
  --data '{"favorites":[{"idArticulo":"3179","idColorPorArticulo":"AZM"}]}' \
  http://127.0.0.1/content/cliente/favoritos/agregarVarios.php

# Stock en producción (requiere vistas en MySQL)
curl -i 'http://127.0.0.1/content/api/stock_produccion.php?articulo=3179&color=AZM'
```

> **Sesiones**: si el endpoint exige `Usuario::logueado()`, para probar con `curl` capturá cookies tras un login previo: `curl -c cookies.txt -b cookies.txt ...`.

---

## 8) MySQL (vistas necesarias para KOI)

```sql
-- En koi1_stage (collation recomendado: utf8mb4_0900_as_ci)
CREATE OR REPLACE VIEW `koi1_stage`.`stock_produccion_incumplida_v` AS
SELECT
  CONVERT(pe.cod_articulo USING utf8mb4) COLLATE utf8mb4_0900_as_ci        AS cod_articulo,
  CONVERT(pe.denom_articulo USING utf8mb4) COLLATE utf8mb4_0900_as_ci      AS denom_articulo,
  CONVERT(pe.cod_color_articulo USING utf8mb4) COLLATE utf8mb4_0900_as_ci  AS cod_color_articulo,
  SUM(pe.cantidad)                                  AS cantidad,
  SUM(IFNULL(pe.pos_1_cant, 0))                     AS cant_1,
  SUM(IFNULL(pe.pos_2_cant, 0))                     AS cant_2,
  SUM(IFNULL(pe.pos_3_cant, 0))                     AS cant_3,
  SUM(IFNULL(pe.pos_4_cant, 0))                     AS cant_4,
  SUM(IFNULL(pe.pos_5_cant, 0))                     AS cant_5,
  SUM(IFNULL(pe.pos_6_cant, 0))                     AS cant_6,
  SUM(IFNULL(pe.pos_7_cant, 0))                     AS cant_7,
  SUM(IFNULL(pe.pos_8_cant, 0))                     AS cant_8,
  CONVERT(pe.posic_1 USING utf8mb4) COLLATE utf8mb4_0900_as_ci             AS posic_1
FROM `koi1_stage`.`programacion_empaque_v` pe
WHERE pe.situacion IN ('p','i')
  AND pe.anulado = 'n'
  AND pe.Confirmada = 's'
  AND pe.cumplido_paso = 'n'
GROUP BY pe.cod_articulo, pe.denom_articulo, pe.cod_color_articulo, pe.posic_1;

CREATE OR REPLACE VIEW `koi1_stage`.`stock_produccion_incumplida_40_v` AS
SELECT * FROM `koi1_stage`.`stock_produccion_incumplida_v`;
```

**Chequeo**:

```sql
SHOW FULL TABLES IN koi1_stage LIKE 'stock_produccion_incumplida%';
```

---

## 9) Salud y diagnóstico (one-liners)

```bash
# Snapshot general del contenedor y guardarlo en el host
mkdir -p ~/koi1-dump && \
  docker inspect koi1-php56 > ~/koi1-dump/inspect.json && \
  docker logs --tail 500 koi1-php56 > ~/koi1-dump/docker-logs.txt 2>&1 && \
  docker exec koi1-php56 bash -lc 'apache2ctl -S; apache2ctl -M; php -v; php -m' > ~/koi1-dump/stack.txt 2>&1 && \
  echo "Hecho: ~/koi1-dump/"

# Endpoints mínimos
curl -I http://127.0.0.1:8195/
docker exec -it koi1-php56 bash -lc "curl -i http://127.0.0.1/"

# Logs vivos
docker exec -it koi1-php56 bash -lc 'tail -f /var/log/apache2/error.log'
```

---

## 10) Re-inicio / despliegue

```bash
# Reiniciar solo el contenedor (host)
docker restart koi1-php56

# Dentro del contenedor: recargar Apache
apache2ctl -k graceful || apache2ctl -k restart
```

---

## 11) FAQ / gotchas

- **301 al probar desde el host**: usá `:8195` o ejecutá `curl` dentro del contenedor; `http://127.0.0.1` a secas golpea nginx del host.
- **php.ini “(none)”**: normal; la imagen aplica `.ini` via `conf.d/` (ver sección PHP).
- **Warnings mezclados con JSON**: desactivar `display_errors`, activar `log_errors` y establecer `error_log` (hecho en `zzz-koi-override.ini`).
- **Sesiones**: endpoints que llaman `Usuario::logueado()` devuelven **403** si no hay cookie de sesión; evita 500 envolviendo la llamada y/o iniciando `session_start()` tras `premaster.php`.
- **Collation**: mantener `utf8mb4_0900_as_ci` para columnas comparadas con `=` y alinear vistas/tablas afectados.

---

## 12) Ficha técnica (valores observados)

- **Contenedor**: `koi1-php56`
- **Imagen**: `koi1-php56:local`
- **Puertos**: `0.0.0.0:8195->80/tcp`
- **Apache**: 2.4.25 (Debian)
- **PHP**: 5.6, SAPI Apache 2.0 Handler
- **VHost**: `/etc/apache2/sites-enabled/encinitas.conf` → `DocumentRoot /var/www/encinitas`
- **Overrides PHP**: `/usr/local/etc/php/conf.d/zzz-koi-override.ini`

> **Acción sugerida**: ejecutar los comandos de las secciones 2–6 y pegar el `inspect.json`, `stack.txt` y `docker-logs.txt` en el repo de documentación para congelar el estado del entorno.



---

## 12b) Snapshot verificado (11‑Sep‑2025)

**Resultados reales en tu host**

- Dump generado: `~/koi1-dump/`
  - `inspect.json` (Docker inspect del contenedor)
  - `docker-logs.txt` (últimas 500 líneas de logs del contenedor)
  - `stack.txt` (Apache vhosts/módulos y PHP v/m)
- Respuesta desde **host → contenedor**: `curl -I http://127.0.0.1:8195/` → **200 OK**, `Server: Apache/2.4.25 (Debian)` y cookie `PHPSESSID=...`
- Respuesta **dentro del contenedor**: `curl -i http://127.0.0.1/` → **200 OK** (HTML del login KOI)
- **VHost activo**: `*:80 localhost` (desde `apache2ctl -S`), con `DocumentRoot /var/www/encinitas` (`/etc/apache2/sites-enabled/encinitas.conf`).

**Notas de operación**

- Si `tail -f /var/log/apache2/error.log` “cuelga”, usar **no bloqueante**:\
  `docker exec -it koi1-php56 bash -lc 'tail -n 200 /var/log/apache2/error.log'`\
  o con timeout:\
  `docker exec -it koi1-php56 bash -lc 'timeout 3 tail -f /var/log/apache2/error.log || true'`.
- Para evitar tocar nginx del host (que devuelve `301` a HTTPS), probar siempre vía **8195** o desde dentro del contenedor.

