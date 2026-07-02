# Estado Lote Cliente (entorno dev encinitas5.6)

Fecha de corte: 2026-05-19

## Alcance del lote

- Base de motor:
  - `factory/Config.php`
  - `factory/Datos.php`
  - `factory/Factory.php`
  - `factory/Transaction.php`
- Flujo cliente:
  - `content/cliente/favoritos/*`
  - `content/cliente/pedidos/index.php`
  - `content/cliente/index.php`
  - `content/cliente/menu.php`
  - `content/cliente/mobilemenu.php`
  - `content/cliente/usermenu.php`
- Documentacion:
  - `docs/migracion/smoke-test-cliente-php56.md`

## Estado tecnico

- `favoritos`: batch y endpoints single/edit validados en runtime Ubuntu; locking Linux corregido en `factory/Mutex.php`.
- `pedidos`: `index.php` compatible, endpoints `agregar/borrar/getPdf` endurecidos y smoke test funcional completo validado en Ubuntu.
- `cliente shell` (menu/index/usermenu/mobilemenu): normalizado para no depender de `short_open_tag`.
- `transaction`: desacoplada de sintaxis SQL Server y alineada al driver.

## Validaciones hechas

- Lint local en archivos modificados de cliente y endpoints principales.
- No quedaron `<? echo ... ?>` en archivos activos bajo `content/cliente` (solo en `.bak`).
- Validacion manual positiva de favoritos batch con payload real (3202 / RSN) en runtime Ubuntu.
- Validacion manual positiva de favoritos single (agregar.php, editarLibre.php) y generacion de pedido cliente.
- Pedido cliente `5392` generado correctamente y PDF accesible en runtime Ubuntu/contenedor.

## Riesgo conocido

- El host local valida con PHP 5.2.9; la validacion final debe hacerse en runtime PHP 5.6 real.
- `content/cliente/perfil/index.php` esta vacio (tambien en `encinitas_prod_truth`), fuera de este lote.
- `content/cliente/pedidos/*` ya quedo normalizado; el riesgo residual se desplaza a otros modulos legacy que consumen `Html2Pdf`.

## Gate para merge a main

Seguir `docs/migracion/smoke-test-cliente-php56.md` y habilitar merge a `main` solo si:

1. Pasa smoke test funcional de catalogo/favoritos/pedidos/PDF.
2. No hay error 500 ni warning bloqueante en flujo cliente.
3. Favoritos y pedidos mantienen paridad funcional base.
