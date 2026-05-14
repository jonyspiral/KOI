# Estado Lote Cliente (entorno dev encinitas5.6)

Fecha de corte: 2026-05-14

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

- `favoritos`: ajustado (json handlers, estructura de items y reporte).
- `pedidos`: compatibilidad de tag PHP en `index.php`.
- `cliente shell` (menu/index/usermenu/mobilemenu): normalizado para no depender de `short_open_tag`.
- `transaction`: desacoplada de sintaxis SQL Server y alineada al driver.

## Validaciones hechas

- Lint local en archivos modificados de cliente y endpoints principales.
- No quedaron `<? echo ... ?>` en archivos activos bajo `content/cliente` (solo en `.bak`).

## Riesgo conocido

- El host local valida con PHP 5.2.9; la validacion final debe hacerse en runtime PHP 5.6 real.
- `content/cliente/perfil/index.php` esta vacio (tambien en `encinitas_prod_truth`), fuera de este lote.

## Gate para merge a main

Seguir `docs/migracion/smoke-test-cliente-php56.md` y habilitar merge a `main` solo si:

1. Pasa smoke test funcional de catalogo/favoritos/pedidos.
2. No hay error 500 ni warning bloqueante en flujo cliente.
3. Favoritos y pedidos mantienen paridad funcional base.
