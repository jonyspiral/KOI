# Metodo de recuperacion de contexto - KOI1 Encinitas

Fecha de corte: 2026-05-15

## Problema

Durante la depuracion de un error 500 y luego una pantalla blanca, el trabajo se desvio de la linea original de migracion. Se mezclaron fuentes distintas:

- produccion legacy
- backup funcional PHP 5.6/MySQL
- repo Docker actual
- backups intermedios
- `fonts/evy`

Eso genero perdida de contexto y cambios reactivos sobre archivos de flujo (`login`, `master`, `premaster`, `main`, menu cliente, CSS) sin mantener una linea clara de recuperacion.

## Fuentes y roles

- `C:\dev\encinitas_prod_real_20260515`: fuente semantica de verdad. Sirve para entender flujo KOI1 real, pantallas, includes, permisos y comportamiento legacy.
- `C:\dev\encinitas_backup_20260514-142411`: baseline tecnico funcional. En este estado login, modo cliente y pedidos funcionaban en PHP 5.6/MySQL.
- `Y:\var\www\encinitas`: repo activo de trabajo. Se usa, pero debe ser llevado de vuelta a una linea controlada antes de seguir.
- `C:\dev\encinitas_prod_truth`: fuera del circuito operativo por contaminacion de contexto.

## Commits de control

La linea controlada queda anclada en:

- `d13993a koi1: alinea motor mysql (config, datos y transacciones)`
- `6fb1e73 koi1 cliente: hardening favoritos/pedidos y compat php tags`

Hasta esos commits el trabajo tenia separacion razonable entre motor y lote cliente.

Archivos del commit de motor:

- `factory/Config.php`
- `factory/Datos.php`
- `factory/Factory.php`
- `factory/Transaction.php`

Archivos del commit cliente:

- `content/cliente/favoritos/agregarVarios.php`
- `content/cliente/favoritos/borrarTodos.php`
- `content/cliente/favoritos/borrarVarios.php`
- `content/cliente/favoritos/index.php`
- `content/cliente/favoritos/reporte/index.php`
- `content/cliente/favoritos/reporte/reporte.php`
- `content/cliente/index.php`
- `content/cliente/menu.php`
- `content/cliente/mobilemenu.php`
- `content/cliente/pedidos/index.php`
- `content/cliente/usermenu.php`
- `docs/migracion/comandos-commit-lote-cliente.md`
- `docs/migracion/estado-lote-cliente-dev.md`
- `docs/migracion/smoke-test-cliente-php56.md`

## Decision de estrategia

Como lo migrado real es menor al total de KOI1, no conviene consolidar parches locales como patron definitivo. La estrategia vuelve a ser motor adaptado:

- mantener la app funcionalmente parecida a KOI1 legacy;
- adaptar infraestructura comun para MySQL/PHP 5.6;
- refactorizar modulos migrados para alinearlos al motor;
- evitar soluciones sueltas por modulo salvo excepciones justificadas.

## Motor minimo esperado

Capas prioritarias:

1. `factory/Config.php`: configuracion por entorno, driver, host, base, charset y timezone.
2. `factory/Datos.php`: punto unico de ejecucion, escape, fetch, stored procedures y compatibilidad MySQL.
3. `factory/SqlCompat.php`: capa pragmatica para transformar SQL legacy frecuente.
4. `clases/Base.php`: generacion de CRUD compatible con MySQL.
5. `factory/Mapper.php`: ajustes dirigidos solo cuando fallos reales lo exijan.

Compatibilidad SQL inicial:

- `SELECT TOP n` a `LIMIT n`
- `ISNULL(x,y)` a `IFNULL(x,y)`
- `GETDATE()` a `NOW()`
- `@@IDENTITY` a `LAST_INSERT_ID()`
- `WITH (NOLOCK)` neutralizado
- `LEN()` a `CHAR_LENGTH()`
- manejo explicito de ids manuales y `AUTO_INCREMENT`

## Metodo de trabajo

1. No hacer rollbacks globales ni copias masivas.
2. Usar `Y:\var\www\encinitas` como repo activo.
3. Preservar siempre `docs/` y archivos `*.md`.
4. Restaurar o portar por lista corta de archivos.
5. Separar commits por responsabilidad:
   - motor
   - cliente
   - stored procedures
   - ABM puntual
6. Validar cada avance con smoke test minimo.
7. Documentar causa raiz, fix, validacion y riesgos.

## Clasificacion de modulos

Cada modulo debe quedar en uno de estos estados:

- `MIGRADO`: funciona en PHP 5.6/MySQL y esta alineado al motor.
- `HIBRIDO`: funciona parcialmente, pero conserva SQL Server, helpers legacy o parches locales.
- `LEGACY`: todavia depende del stack original.
- `DESCONOCIDO`: no fue relevado.

## ABMs

Antes de migrar un ABM, relevar:

- entrypoint real;
- archivos `index`, `buscar`, `editar`, `guardar`, `borrar`;
- clase principal;
- si usa `Factory`, `Mapper`, `Base`, query directa o stored procedure;
- tablas, vistas y stored procedures;
- dependencias de permisos;
- diferencias SQL Server/MySQL;
- prueba funcional minima.

Regla de decision:

- Si una mejora central desbloquea varios modulos, tocar motor.
- Si solo desbloquea un modulo, hacer port dirigido y documentarlo.

## Estado de recuperacion aplicado

Se ejecuto restore selectivo hacia `6fb1e73` sobre archivos versionados de aplicacion, excluyendo `docs/` y `*.md`.

Quedaron particularidades del entorno:

- `clases/Html.php` y `clases/HTML.php` colisionan en el mount RaiDrive/SFTP.
- Se agrego fallback explicito en `includes.php` para cargar `clases/Html.php.bak` y evitar fallo de autoload en PHP 5.6/Linux.
- Los `Thumbs.db` pueden aparecer con diferencias de case (`Thumbs.db` vs `thumbs.db`) y no deben guiar decisiones de migracion.

## Siguiente paso

Ejecutar `docs/migracion/smoke-test-cliente-php56.md` sobre Docker real.

Si pasa:

- congelar este punto como nuevo baseline operativo;
- continuar con inventario ABM.

Si falla:

- diagnosticar desde log/error exacto;
- no restaurar carpetas completas;
- corregir solo el archivo responsable.
