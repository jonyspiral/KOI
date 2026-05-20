# Avance Lote C - PDF sistema y formularios - 2026-05-19

## Alcance

Cerrar el remanente tecnico del frente PDF despues de validar comercial, produccion y administracion.

## Trabajo aplicado

### Sistema

Se normalizo:

- `content/sistema/auditoria/calificacion_clientes/getPdf.php`

Quedo con el mismo patron defensivo del resto de endpoints PDF:

- `ob_start()`
- `shutdown_function` para fatales
- `ob_clean()` antes de emitir salida
- chequeo explicito de sesion y permiso
- `exit` inmediato si responde JSON de error

### Formularios

Se relevaron los flujos reales que usan `Formulario.php` y sus subclases.

Hallazgos:

- el motor comun ya quedo saneado por los fixes sobre `Html2Pdf`, `KoiServices`, rutas Linux y `Html.php.bak`
- `FormularioGuiaDePorte.php` tenia un bug puntual de ruta de modelo que ya fue corregido
- `content/produccion/guia_de_porte/getPdf.php` ahora tambien quedo normalizado con el mismo hardening defensivo de los demas endpoints PDF

## Estado actual

- `content/sistema/auditoria/calificacion_clientes/getPdf.php`: `PENDIENTE DE SMOKE TEST`
- `content/produccion/guia_de_porte/getPdf.php`: `PENDIENTE DE SMOKE TEST`
- `Formulario*.php` en general: `MOTOR SANEADO`, falta evidencia funcional puntual

## Smoke test recomendado

### 1. Sistema / auditoria de calificacion de clientes

Usar una sesion valida contra `/` y probar:

```bash
curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie \
  -o /tmp/calificacion_clientes.pdf \
  "http://127.0.0.1:8195/content/sistema/auditoria/calificacion_clientes/getPdf.php?fechaDesde=2026-05-01&fechaHasta=2026-05-20"

file /tmp/calificacion_clientes.pdf
head -c 120 /tmp/calificacion_clientes.pdf | xxd
```

### 2. Produccion / guia de porte

Flujo directo del formulario real:

```bash
curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie \
  -o /tmp/guia_de_porte.pdf \
  "http://127.0.0.1:8195/content/produccion/guia_de_porte/getPdf.php?numeroGuia=1&fecha=20/05/2026&senores=Cliente%20Prueba&clienteNro=1&direccionCalle=Siempreviva&direccionNumero=742&direccionPiso=&direccionDpto=&direccionLocalidad=Springfield&direccionCP=1000&cuit=20123456789&condicionIva=RI&transportistaSenor=Transportista%20Prueba&transportistaDomicilio=Ruta%201&transportistaCuit=20123456789&transportistaDni=12345678&detalle[0][cantidad]=1&detalle[0][descripcion]=Bulto%20de%20prueba"

file /tmp/guia_de_porte.pdf
head -c 120 /tmp/guia_de_porte.pdf | xxd
```

## Criterio de cierre del remanente de Lote C

Se considera suficiente para cerrar este remanente si:

- `calificacion_clientes/getPdf.php` devuelve PDF valido o error de negocio controlado
- `guia_de_porte/getPdf.php` devuelve PDF valido o error de negocio controlado
- no aparecen fatales, salida contaminada ni problemas de runtime Linux