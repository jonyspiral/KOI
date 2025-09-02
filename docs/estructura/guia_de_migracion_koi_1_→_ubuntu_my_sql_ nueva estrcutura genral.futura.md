# 🧭 Guía de migración KOI1 → Ubuntu + MySQL (por islas)

> Objetivo: migrar gradualmente KOI1 desde Windows/SQL Server a **Ubuntu + MySQL**, conviviendo temporalmente con KOI2 (Laravel/PHP 8.2), hasta apagar completamente Windows.

---

## 1) Alcance y principios

- **Migración por islas** (módulos relativamente independientes: tablas, vistas, SP y archivos PHP específicos).
- **Cambios mínimos** al framework custom de KOI1.
- **Convivencia**: KOI1 (PHP 5.2.9) y KOI2 (PHP 8.2) operan **la misma BD MySQL** de forma controlada.
- **Rollback simple**: un toggle de `driver` en `config.php` para volver a SQL Server cuando sea necesario.

---

## 2) Infra base (referencial)

- **Ubuntu** (192.168.2.210) con:
  - KOI2 (Laravel + PHP 8.2) en prod (NGINX→Apache:8081)
  - **Nuevo vhost** para KOI1 (“encinitas/”) con **PHP 5.2.9 aislado** (CGI/FastCGI o contenedor)
- **MySQL** como fuente de verdad (bd `koi`/`koi2`).
- **Usuarios** de BD separados: `koi1_legacy_ro` / `koi1_legacy_rw` y `koi2_app`.

> Carpeta sugerida:

```
/var/www/
├── koi2/          # Laravel prod
├── koi2_v1/       # Laravel dev
└── encinitas/     # KOI1 (PHP 5.2.9)
```

---

## 3) Fases del proyecto

### Fase 0 — Base técnica (una sola vez)

1. Crear `encinitas/` en Ubuntu con el esqueleto KOI1.
2. Levantar vhost Apache (p.ej. puerto 8195) apuntando a PHP 5.2.9 aislado.
3. Agregar **driver MySQL** a la Factory de KOI1 (`DbMysql`) y mantener `DbSqlsrv` para rollback.
4. Definir conexión MySQL en `config.php` + `utf8mb4` + `time_zone` `-03:00`.
5. **Shim SQL** activado por sesión para traducir T‑SQL→MySQL (ver §6.2).

### Fase 1 — Piloto en solo lectura (RO)

1. Seleccionar **isla A** (bajo acoplamiento, preferir listados/reportes).
2. Inventario de **tablas/vistas/SP** usados por la isla.
3. Conectar a MySQL con usuario **RO** y validar 3 pantallas (listado, detalle, reporte).
4. Comparar resultados y tiempos vs. KOI1 original.

### Fase 2 — Escritura controlada (RW)

1. Habilitar usuario **RW** para la isla A.
2. Verificar transacciones/locks y crear **índices equivalentes** (EXPLAIN/Slow Log).
3. Alinear orden de actualización con KOI2 para reducir deadlocks.

### Fase 3 — Rampa iterativa

- Repetir ciclo **(Descubrir → Portar SQL → Probar RO → Habilitar RW)** por dominios:
  1. Maestros (Clientes, Artículos, Vendedores)
  2. Operaciones livianas (Pedidos, Remitos no contables)
  3. Procesos intensivos (Stock, Caja/Contabilidad, Integraciones)

### Fase 4 — Decom Windows

- Congelar escrituras en Windows → apuntar todo KOI1 a Ubuntu → apagar servicios en SRV‑NEW.

---

## 4) Estructura mínima en `encinitas/`

- `premaster.php`, `master.php` (bootstrap/plantilla)
- `factory.php` (elige driver por `config.php`)
- `datos.php` / `Db*.php` (capa de datos)
- `config.php` (parámetros por vhost/entorno)
- `/includes`, `/lib`, `/templates` necesarios

### 4.1 `config.php` (ejemplo)

```php
return [
  'db' => [
    'driver'  => 'mysql',
    'host'    => '192.168.2.210',
    'port'    => 3306,
    'name'    => 'koi',
    'user'    => 'koi1_legacy_rw',
    'pass'    => '********',
    'charset' => 'utf8mb4',
    'timeout' => 5,
  ],
];
```

### 4.2 `factory.php` (switch de drivers)

```php
switch ($cfg['db']['driver']) {
  case 'mysql':  return new DbMysql($cfg['db']);
  case 'sqlsrv': return new DbSqlsrv($cfg['db']); // rollback
  case 'odbc':   return new DbOdbc($cfg['db']);
  default: throw new InvalidArgumentException('Driver desconocido');
}
```

---

## 5) Convivencia KOI1 (PHP 5.2.9) ↔ KOI2 (PHP 8.2)

- **BD compartida** con usuarios distintos y privilegios mínimos.
- **Transacciones**: usar begin/commit/rollback en ambas apps con el mismo orden de actualización por módulo.
- **sql\_mode por sesión** en KOI1 para evitar choques con código legacy (KOI2 puede seguir en `STRICT`).
- **Zona horaria** `-03:00` en ambas conexiones.
- **Auditoría**: tabla/trigger de cambios relevantes durante la convivencia.

---

## 6) Cambios mínimos al framework KOI1

### 6.1 `DbMysql` (resumen funcional)

- Conexión via `mysqli_init()` + `real_connect()`
- `set_charset('utf8mb4')`
- `SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'`
- `SET time_zone='-03:00'`
- Métodos: `begin()`, `commit()`, `rollback()`, `query($sql,$p)`, `exec($sql,$p)`, `call($sp,$p)` (manejar múltiples resultsets)

### 6.2 Shim T‑SQL → MySQL (reglas)

- `SELECT TOP n ... ORDER BY ...` → `SELECT ... ORDER BY ... LIMIT n`
- `ISNULL(x,y)` → `IFNULL(x,y)`
- `LEN(x)` → `CHAR_LENGTH(x)`
- `GETDATE()` → `NOW()`
- Remover `WITH (NOLOCK)` (o ajustar aislamiento por sesión si es imprescindible)

### 6.3 Diferencias típicas

- `@@IDENTITY` → `LAST_INSERT_ID()`
- `NVARCHAR`→`VARCHAR` / `BIT`→`TINYINT(1)`
- Fechas `0000-00-00` (cuidar si `STRICT` en KOI2)

---

## 7) Vhost Apache (ejemplo KOI1 en 8195)

```apache
<VirtualHost *:8195>
  ServerName encinitas.local
  DocumentRoot /var/www/encinitas
  <Directory /var/www/encinitas>
    AllowOverride All
    Require all granted
  </Directory>

  # PHP 5.2.9 aislado (CGI/FastCGI o contenedor)
  ScriptAlias /php52/ "/opt/php52/"
  Action php52-script "/php52/php-cgi"
  AddHandler php52-script .php

  ErrorLog  /var/log/apache2/encinitas_error.log
  CustomLog /var/log/apache2/encinitas_access.log combined
</VirtualHost>
```

---

## 8) Checklist 48h por isla

**Día 1**

-

**Día 2**

-

---

## 9) Criterios de “estabilidad OK”

- 72 h sin errores en logs de KOI1 y sin deadlocks en MySQL
- p95 de respuesta ±10% vs. KOI1 original (o mejor)
- Consistencia de datos validada (consultas cruzadas KOI1/KOI2)

---

## 10) Gobernanza de datos y DDL

- **Owner DDL**: KOI2 (Laravel) coordina cambios de esquema.
- KOI1 solicita columnas/índices vía tickets; se aplican migrations/SQL centralizado.
- **Backups**: snapshot diarios + binlog (PITR); prueba de restore mensual.

---

## 11) Plan de rollback

- Cambiar `driver` a `sqlsrv` en `config.php` (o variable de entorno del vhost) y reiniciar servicio web.
- Mantener `DbSqlsrv` y `DbOdbc` intactos.

---

## 12) Roadmap sugerido

1. **Base técnica** (encinitas + vhost + driver + shim)
2. **Isla 1 (RO→RW)**: Reportes/Maestros simples
3. **Isla 2 (RO→RW)**: Operaciones livianas
4. **Isla 3 (RO→RW)**: Procesos intensivos
5. **Full switch** de KOI1 a Ubuntu
6. **Decom Windows** y limpieza final

---

## 13) Riesgos y mitigaciones

- **Diferencias SQL** → shim + `sql_mode` por sesión
- **Locking/Transacciones** → orden consistente + pruebas en RW
- **Índices faltantes** → EXPLAIN + réplicas de IX de SQL Server
- **Fechas/charset** → `utf8mb4` + TZ `-03:00` + validaciones de fecha en app

---

## 14) Artefactos a preparar

- `DbMysql.php` (clase)
- `factory.php` (switch con `case 'mysql'`)
- `config.php` (target MySQL por entorno)
- **Shim** de traducción T‑SQL→MySQL (50 líneas)
- Script de diagnóstico de conexión (ping BD, sql\_mode, tz)

---

### ✅ Resultado esperado

- KOI1 corre en Ubuntu (carpeta `encinitas/`) con PHP 5.2.9, operando la **misma BD MySQL** que KOI2.
- Migración por islas sin interrupciones, hasta **apagar Windows** con seguridad.



---

## 🟩 Hito alcanzado — 22/08/2025

**Objetivo:** dejar KOI1 corriendo en PHP 5.6 contra MySQL 8 (handshake y auth estables) sobre UBUNTUSERVER.

**Logrado:**

- Contenedor **PHP 5.6 + Apache** sirviendo `encinitas/` en **:8195**.
- MySQL 8 ajustado para compatibilidad de cliente legado:
  - `default_authentication_plugin = mysql_native_password` (archivo `zzz-auth.cnf`).
  - Handshake en **UTF-8 (utf8mb3 / utf8mb3\_general\_ci)** para clientes antiguos.
- Usuario dedicado para Docker (seguridad + compat): `koi1_php56@172.17.%` con `mysql_native_password` y **GRANT** en `koi2` (prod) y `koi2_v1` (dev).
- **Conexión verificada** desde `test_db.php` a **koi2** con `ok => 1` (fecha del servidor y variables de charset/collation correctas).

> Referencias de entorno (según docs adjuntas): UBUNTUSERVER `192.168.2.210`; bases: `koi2` (producción), `koi2_v1` (desarrollo).

## ▶️ Siguientes pasos (Fase 1 — Piloto SOLO LECTURA)

1. **Estructura base KOI1 en **``**:** (listo).
2. **Driver MySQL operativo vía Factory:** (listo) `Factory::getInstance()->db()` devuelve conexión a `koi2`.
3. **Herramientas de test RO** (crear ahora en `tools/`):
   - `tools/proto_listado.php` para listar cualquier tabla con `LIMIT 10` de forma segura.
   - (Opcional) `tools/proto_query.php` solo en dev, para probar consultas con el shim.
4. **Elegir “Isla 1” (RO):** un módulo de listados/reportes (3–6 tablas, bajo acoplamiento). Correr equivalentes a: listado → detalle → reporte.
5. **Observabilidad:** revisar `/var/log/php_errors.log` del contenedor y habilitar slow log en MySQL para rutas del módulo.
6. **Gobernanza DDL:** en KOI2/migrations, nueva norma: `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` siempre explícitos.
7. **Backup previo** a RW.

## ✅ Criterios para cerrar Fase 1 (RO)

- 0 errores en logs por 48–72 h del módulo elegido.
- Totales/consultas coinciden con KOI1 Windows.
- Sin slow queries (>2s) en el recorrido principal del módulo.



---

## 🟩 Hito alcanzado — 22/08/2025 (PHP 5.6 listo con MySQL)

**Logrado:**

- Contenedor `koi1-php56` (PHP 5.6 + Apache) con **mysqli/pdo\_mysql** instalados y activos.
- Apache sirviendo **/var/www/encinitas** como `DocumentRoot` (vhost `encinitas`).
- `Factory::getInstance()->db()` operando contra **koi2\_v1** (DEV).
- Endpoints de verificación:
  - `tools/whereami.php` → muestra DB actual (`koi2_v1`).
  - `tools/proto_listado.php?tabla=...` → lectura segura con `LIMIT 10`.

**Notas técnicas:**

- Imagen base: `php:5.6-apache` (Debian Stretch EOL). Repos apuntados a **snapshot 20210814** para evitar “hash sum mismatch”.
- Extensiones compiladas con **mysqlnd** (`docker-php-ext-install mysqli pdo_mysql`).
- Compatibilidad de cliente: handshake `utf8` (utf8mb3) + `sql_mode` y `time_zone` por sesión.

## ▶️ Qué sigue (Fase 1 — Solo Lectura)

1. **Congelar** el entorno en un Dockerfile reproducible (ver abajo) y/o `docker-compose.yml` con `restart: always` y `healthcheck`.
2. **Elegir Isla 1 (RO):** módulo de listados/detalles con 3–6 tablas de bajo acoplamiento.
3. **Pruebas funcionales:** comparar totales/tiempos contra KOI1 Windows; ajustar consultas con el shim (TOP→LIMIT, ISNULL→IFNULL, etc.).
4. **Observabilidad:** habilitar `error_log` en PHP y slow log en MySQL para rutas del módulo; registrar p95.
5. **Cierre Fase 1:** 48–72 h sin errores, totales OK, sin slow>2s.

## 📦 Dockerfile (reproducible)

```Dockerfile
FROM php:5.6-apache

# DocumentRoot
RUN a2enmod rewrite && \
    sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/encinitas#' /etc/apache2/sites-available/000-default.conf && \
    mkdir -p /var/www/encinitas && ln -sfn /var/www/encinitas /var/www/html

# Repos EOL: snapshot fijo + build deps + mysqli/pdo_mysql (mysqlnd)
RUN set -e; \
  echo 'deb [trusted=yes] http://snapshot.debian.org/archive/debian/20210814T000000Z stretch main' > /etc/apt/sources.list && \
  echo 'deb [trusted=yes] http://snapshot.debian.org/archive/debian-security/20210814T000000Z stretch/updates main' >> /etc/apt/sources.list && \
  printf "Acquire::Check-Valid-Until \"false\";
Acquire::AllowInsecureRepositories \"true\";
" > /etc/apt/apt.conf.d/99legacy && \
  rm -rf /var/lib/apt/lists/* && apt-get update && \
  apt-get install -y --no-install-recommends autoconf pkg-config build-essential && \
  docker-php-ext-configure mysqli --with-mysqli=mysqlnd && \
  docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd && \
  docker-php-ext-install -j"$(nproc)" mysqli pdo_mysql && \
  apt-get purge -y autoconf pkg-config build-essential && apt-get autoremove -y && \
  rm -rf /var/lib/apt/lists/*

# PHP error log básico
RUN echo 'log_errors=On' > /usr/local/etc/php/conf.d/log.ini \
 && echo 'error_log=/var/log/php_errors.log' >> /usr/local/etc/php/conf.d/log.ini

WORKDIR /var/www/encinitas
```

## 🧩 docker-compose.yml (opcional)

```yaml
services:
  koi1-php56:
    build: .
    image: koi1-php56:local
    container_name: koi1-php56
    ports: ["8195:80"]
    volumes:
      - ./:/var/www/encinitas
    restart: always
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/tools/whereami.php"]
      interval: 30s
      timeout: 5s
      retries: 3
```

---

# Isla 1 — Predespachos (content/comercial/reportes/predespachos)

## Objetivo

Levantar el módulo Predespachos en solo lectura (RO) sobre `koi2_v1`, usando la estructura original de Encinitas y el driver MySQL con shim.

## Ubicación

- Origen (Windows): Y:/var/www/encinitas/content/comercial/reportes/predespachos
- Destino (Ubuntu): /var/www/encinitas/content/comercial/reportes/predespachos

## Pasos

1. Copiar la carpeta del módulo al destino respetando mayúsculas y minúsculas.
2. Asegurar permisos de salida si el módulo exporta archivos: crear y dar permisos a `tmp`, `logs`, `html2pdf`, `array2csv`.
3. Revisar includes del módulo y ajustar a `require_once __DIR__.'...';` cuando aplique.
4. Descubrir tablas y vistas relacionadas con pred(e)spachos en `koi2_v1` con una consulta a `information_schema.tables` filtrando por nombre.
5. Probar lecturas con `tools/proto_listado.php` (tablas) y con `tools/proto_query.php` (vistas o consultas del módulo).

## Shim T‑SQL → MySQL a confirmar para este módulo

- TOP n → LIMIT n
- ISNULL(a,b) → IFNULL(a,b)
- LEN(x) → CHAR\_LENGTH(x)
- GETDATE() → NOW()
- WITH (NOLOCK) → eliminar
- CONVERT(VARCHAR(10), f, 120) → DATE\_FORMAT(f, '%Y-%m-%d')
- CONVERT(VARCHAR(19), f, 120) → DATE\_FORMAT(f, '%Y-%m-%d %H:%i:%s')
- DATEADD(DAY, n, f) → DATE\_ADD(f, INTERVAL n DAY)
- DATEDIFF(DAY, a, b) → DATEDIFF(b, a)
- SCOPE\_IDENTITY()/@@IDENTITY/IDENT\_CURRENT+IDENT\_INCR → LAST\_INSERT\_ID()

## Pruebas dirigidas (RO)

- Listar tablas/vistas que contengan la palabra despach para identificar fuentes.
- Ejecutar un SELECT limitado sobre cada fuente detectada (LIMIT 10) y validar tiempos y resultados.

## Criterios de aceptación (RO)

- Totales y filas coinciden con KOI1 Windows para filtros equivalentes.
- Sin warnings/errores en logs durante 48–72 horas de uso del módulo.
- Respuestas menores a 2 segundos; si no, revisar índices y consulta.



# Módulo: Reportes → Predespachos (encinitas)

**Ubicación**

- `content/comercial/reportes/predespachos/` con `index.php`, `buscar.php`, `getPdf.php`, `getXls.php`. (Ver estructura).

**Arquitectura y flujo**

1. `master.php` resuelve `pagename` y carga `content/<pagename>.php` → para este módulo: `pagename=comercial/reportes/predespachos` → entra a `index.php`.
2. `index.php` arma el formulario/filtros y dispara la búsqueda.
3. `buscar.php` genera el HTML del listado.
4. Exportadores:
   - `getPdf.php` crea `Html2Pdf`, toma el HTML de `buscar.php` y abre el PDF.
   - `getXls.php` crea `Html2Xls`, toma el HTML de `buscar.php` y descarga XLS.
5. Ambos exportadores chequean permiso `comercial/reportes/predespachos/buscar/`.

**Parámetros (GET)**

- `tipo`, `idCliente`, `idPedido`, `desde`, `hasta`, `almacen`, `idArticulo`, `idColor`, y usa `empresa` desde sesión.

**Dependencias**

- `premaster.php` (manejo de sesión y charset).
- Clases utilitarias: `Html2Pdf`, `Html2Xls`, `Usuario`, `Funciones`.
- Mapper usa vista/tabla: `predespachos_v`, `predespachos`.

**Pruebas rápidas (DEV)**

- Navegador (usuario logueado con permiso):
  - UI: `http://<host>:8195/master.php?pagename=comercial/reportes/predespachos`
  - PDF: `http://<host>:8195/content/comercial/reportes/predespachos/getPdf.php?tipo=P&idPedido=<N>`
  - XLS: `http://<host>:8195/content/comercial/reportes/predespachos/getXls.php?tipo=P&idPedido=<N>`
- Ver DB activa: `tools/whereami.php` → muestra `koi2_v1`.

**Checklist de integración**

-

**Notas**

- Este módulo reutiliza el HTML de `buscar.php` para ambas exportaciones, por lo que cualquier cambio visual impacta en PDF/XLS.
- Los SQL de Predespachos en `Mapper` referencian `predespachos_v` (select) y `predespachos` (insert/update/delete).

