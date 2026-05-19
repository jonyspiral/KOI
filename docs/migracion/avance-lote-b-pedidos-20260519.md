# Avance Lote B - Pedidos cliente - 2026-05-19

## Resumen

Se cerro la recuperacion funcional del flujo de pedidos cliente sobre KOI1 PHP 5.6 + MySQL ejecutando en contenedor Docker `koi1-php56` expuesto por `:8195`.

La validacion final en Ubuntu confirmo:

- alta de pedido desde favoritos reales
- persistencia correcta del pedido generado
- visualizacion del PDF del pedido

Caso validado en runtime:

- pedido cliente generado: `5392`
- PDF accesible en `/content/cliente/pedidos/getPdf.php?id=5392`

## Cambios tecnicos aplicados

### Endpoints endurecidos

Se normalizaron y endurecieron:

- `content/cliente/pedidos/agregar.php`
- `content/cliente/pedidos/borrar.php`
- `content/cliente/pedidos/getPdf.php`

Con estos cambios los endpoints quedaron con:

- output buffering defensivo
- limpieza de salida espuria antes de emitir JSON o PDF
- `shutdown_function` para exponer fatales reales
- chequeo explicito de permisos y usuario logueado

### Compatibilidad MySQL

El bloqueo funcional inicial de alta de pedido en MySQL fue:

- `Incorrect parameter count in the call to native function 'ISNULL'`

La causa fue un shim roto en `factory/drivers/DbMysql.php`: el comentario indicaba traduccion `ISNULL -> IFNULL`, pero la linea real no hacia ningun reemplazo.

Se corrigio el driver para traducir `ISNULL(` a `IFNULL(` antes de ejecutar SQL en MySQL.

### Locking Linux

El flujo seguia dependiendo de la correccion previa de `factory/Mutex.php`, reemplazado por una implementacion basada en archivo + `flock()` compatible con Linux.

### PDF en contenedor

El flujo PDF de pedidos no fallaba por logica de negocio sino por runtime del contenedor `koi1-php56`.

Problemas cerrados en cadena:

1. `KoiServices` dependia de `socket_*` y fallaba si `ext-sockets` no estaba cargada.
2. El daemon `KoiServices` no estaba disponible en el runtime objetivo.
3. `wkhtmltopdf` no estaba instalado dentro del contenedor, aunque si existia en el host.
4. Qt necesitaba entorno headless y requirio `xvfb`.
5. `Html2Pdf.php` armaba rutas temporales con un path del host que no existia dentro del contenedor.

Se resolvio con esta estrategia:

- `clases/KoiServices.php` ahora soporta fallback por `stream_socket_client`
- `clases/Html2Pdf.php` usa ejecucion local en Linux con `/usr/bin/wkhtmltopdf`
- si existe `xvfb-run`, lo usa automaticamente
- las rutas de `tmp/html2pdf` e `includes/html2pdf` se resuelven contra el runtime real del contenedor
- se asegura la existencia de `tmp/html2pdf/`

Requisito operativo confirmado en el contenedor `koi1-php56`:

- `wkhtmltopdf`
- `xvfb`

## Subestado del lote

Subestado actualizado dentro de `Lote B - Cliente`:

- favoritos batch: `MIGRADO`
- favoritos single/edit: `MIGRADO`
- pedidos: `MIGRADO`
- PDF de pedidos: `MIGRADO`

## Riesgos residuales

- Queda ruido historico en otros modulos que aun usan `Html2Pdf` legacy; aunque la clase ya soporta Linux, conviene smoke testear reportes BO de mayor uso.
- El runtime final de cliente sigue dependiendo del contenedor `koi1-php56`, no del host Ubuntu.
- Persisten archivos legacy fuera del lote con mezcla de tags PHP y mensajes no normalizados.

## Resultado

`Lote B - Cliente` queda funcionalmente cerrado para:

- catalogo
- favoritos
- pedidos
- PDF de pedidos