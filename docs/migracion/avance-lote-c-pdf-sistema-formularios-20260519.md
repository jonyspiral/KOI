# Avance Lote C - PDF sistema y formularios - 2026-05-19

## Alcance de este sublote

Se relevo y cerro el ultimo tramo tecnico pendiente de `Lote C` despues de validar comercial, produccion y administracion.

Incluye:

- el unico endpoint `content/sistema/*/getPdf.php`
- la base comun de formularios que consumen `Html2Pdf`
- revision puntual de `Formulario*.php` en busca de errores mecanicos de ruta/modelo

## Sistema

Se normalizo:

- `content/sistema/auditoria/calificacion_clientes/getPdf.php`

Cambio aplicado:

- `ob_start()` al inicio
- `ob_clean()` luego del bootstrap
- `shutdown_function` para fatales reales en JSON
- chequeo explicito de permiso y usuario logueado
- `exit` inmediato si falla el permiso
- escritura UTF-8 sin BOM

## Formularios

Consumers relevados de `Html2Pdf` en `clases/Formulario*.php`: 21 clases.

Hallazgo puntual corregido:

- `clases/FormularioGuiaDePorte.php` tenia mal concatenada la ruta del modelo y terminaba en `... . 'php'` en vez de `... . '.php'`

No se reescribio el resto de `Formulario*.php` porque el motor comun ya quedo saneado en:

- `clases/Html2Pdf.php`
- `clases/KoiServices.php`
- `clases/Html.php.bak`

## Estado de Lote C

Subestado actual:

- PDF comercial: `VALIDADO`
- PDF produccion: `VALIDADO`
- PDF administracion: `VALIDADO`
- PDF sistema: `NORMALIZADO, SMOKE TEST PENDIENTE`
- Formularios de negocio: `BASE TECNICA SANEADA, SMOKE TEST PENDIENTE`

## Siguiente paso recomendado

Validar con la misma sesion autenticada:

1. `content/sistema/auditoria/calificacion_clientes/getPdf.php`
2. al menos un formulario que use `Formulario.php`
3. `FormularioGuiaDePorte.php` en un flujo real que invoque su modelo
