# Estado de recuperacion de render cliente y BO - 2026-05-15

## Estado actual
- La raiz `/` vuelve a responder y el login cliente renderiza.
- El catalogo cliente vuelve a renderizar con `CatalogoCtrl`, `#catalogo` e items visibles.
- El menu lateral vuelve a filtrar por familias y por tipos de producto stock.
- El BO tambien vuelve a renderizar.

## Ajustes aplicados
- `factory/Config.php`: credenciales MySQL y `pathBase` alineados al runtime Linux Docker de KOI1 stage.
- `includes.php`: fallback de carga para `Html.php` por la colision de nombres en el mount RaiDrive/SFTP.
- `master.php`: `findRealPath()` corregido para resolver rutas relativas reales antes de caer a `include_path`.
- `factory/Mapper.php`: tabla `tipo_producto_stock` en minusculas para MySQL Linux.
- `factory/Mapper.php`: fallback defensivo para `zoom_cana` / `texto_cana` cuando no llegan los aliases con `ñ`.
- `content/cliente/menu.php`: filtros default normalizados a `01/02/03/04`.
- `clases/Cliente.php` y `clases/ColorPorArticulo.php`: firmas `getIdNombre()` alineadas con `Base` para evitar `Strict Standards`.

## Evidencia de recuperacion
- `curl` sobre `/catalogo/?c=1&f=all` ya devuelve `CatalogoCtrl`, `id="catalogo"` e `item-inner`.
- Ya no aparecen `Strict Standards` ni `Undefined index` en la respuesta HTML del catalogo.
- La query de `tipo_producto_stock` responde correctamente en `koi1_stage`.

## Pendiente para la siguiente sesion
- smoke test funcional completo en navegador: favoritos, pedidos y flujo detallado de modelo.
- limpieza de archivos backup, `Thumbs.db` y artefactos locales antes de un commit de normalizacion.
- revisar si quedan aliases heredados con `ñ` en otras vistas o mappers.

## Excepcion operativa conocida
- El mount RaiDrive/SFTP no materializa de forma confiable clases/Html.php y clases/HTML.php al mismo tiempo.
- En el indice Git existen ambas rutas, pero en el filesystem expuesto pueden aparecer como ausentes y aun asi bloquear la recreacion de Html.php.
- Por eso el baseline funcional actual mantiene la carga explicita de clases/Html.php.bak desde includes.php.
- Esta excepcion debe resolverse fuera del mount problematico o en una copia local/WSL donde el filesystem respete case-sensitive de forma consistente.
