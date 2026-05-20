# Matriz de certificacion para pase productivo a Ubuntu - 2026-05-20

## Objetivo

Definir un criterio operativo de `go/no-go` para migrar KOI1 Encinitas a Ubuntu de forma definitiva.

Esta matriz no mide cantidad de pantallas revisadas. Mide riesgo de negocio cubierto con evidencia.

## Regla principal

No se considera suficiente "tener chequeado el 80 por ciento del sistema" si el 20 por ciento pendiente contiene flujos criticos de:

- ventas y pedidos
- stock y produccion
- cobranzas y proveedores
- tesoreria y contabilidad
- documentos PDF o formularios con valor legal u operativo

La unidad correcta de avance es:

1. cobertura de riesgo
2. cobertura por patron tecnico
3. evidencia reproducible

## Criterio de aprobacion por dominio

Cada dominio debe quedar clasificado en uno de estos estados:

- `CERTIFICADO`: evidencia suficiente para produccion en Ubuntu
- `PARCIAL`: evidencia positiva, pero faltan casos criticos o muestras adicionales
- `BLOQUEADO`: existe un bug o riesgo abierto que impide el pase
- `NO RELEVADO`: no existe evidencia util todavia

## Criterio de aprobacion por modulo o flujo

Un flujo no se considera validado porque "abre". Debe cumplir, segun aplique:

- login y sesion correctos
- render sin fatales ni salida contaminada
- busqueda o carga de datos
- mutacion real si corresponde
- persistencia verificable mediante recarga posterior
- export o PDF si corresponde
- permisos coherentes
- comportamiento reproducible por navegador o `curl`

## Cobertura minima para pase productivo

### 1. Flujos criticos

Deben quedar `CERTIFICADOS` al 100 por ciento:

- login, sesion y logout
- catalogo cliente
- favoritos
- generacion de pedidos cliente
- listado y PDF de pedidos cliente
- BO de cobranzas/proveedores con mutaciones reales
- stock y movimientos criticos
- reportes y PDF operativos usados en el dia a dia
- formularios de negocio obligatorios o legales

### 2. Flujos frecuentes no criticos

Deben quedar al menos `PARCIALES` avanzados o `CERTIFICADOS` en un 80 a 90 por ciento por muestreo inteligente:

- ABMs simples
- consultas BO de uso recurrente
- reportes secundarios
- listados y exportaciones auxiliares

### 3. Infraestructura comun

Debe quedar `CERTIFICADA`:

- rutas Linux / `pathBase`
- case sensitivity
- compatibilidad MySQL (`ISNULL`, views, SPs relevantes)
- mutex Linux
- runtime PDF (`wkhtmltopdf`, `xvfb`, rutas temporales, contenedor)
- sesiones y permisos en runtime real
- salida limpia sin BOM ni prefijos debug

## Estado actual resumido

| Dominio | Estado | Evidencia actual | Riesgo restante |
| --- | --- | --- | --- |
| Cliente | `CERTIFICADO PARCIAL` | favoritos, pedidos, PDF cliente validados en runtime real | falta criterio final de corte sobre otras rutas cliente no cubiertas |
| PDF BO/comercial | `PARCIAL AVANZADO` | comercial, produccion y administracion validados; sistema/formularios pendientes | falta cierre puntual de sistema y formularios |
| BO interactivo | `PARCIAL AVANZADO` | `abm/bancos` CRUD validado; `gestion_proveedores` y `gestion_cobranza` con mutacion real validada | falta ampliar muestra a mas pantallas BO de distinta familia |
| Infraestructura comun | `PARCIAL AVANZADO` | mutex Linux, `DbMysql`, `Html2Pdf`, `KoiServices`, sesiones por `/`, limpieza de `Html.php.bak` | falta consolidar remanentes y muestreo final de formularios/sistema |
| Batch / reportes complejos / formularios | `PARCIAL` | avance puntual y motor saneado | falta evidencia funcional de varios flujos |

## Estrategia recomendada para no eternizar la migracion

### A. Validar por familia, no por archivo suelto

Familias sugeridas:

- `index + buscar + editar + borrar`
- `getPdf.php`
- `Formulario*.php`
- pantallas BO con popup + AJAX
- pantallas con stored procedure o view compleja

Si una familia queda estable, se reduce mucho el universo a muestrear.

### B. Muestreo por criticidad

- `Alta`: prueba completa de punta a punta
- `Media`: muestreo funcional representativo
- `Baja`: validacion superficial, salvo hallazgo

### C. Criterio de cierre por lote

Cada lote debe dejar:

- evidencia concreta
- riesgos abiertos
- dominios cubiertos
- dominios todavia no aptos
- decision explicita: `apto`, `apto con riesgo`, `no apto`

## Gate de produccion Ubuntu

No pasar a Ubuntu definitivo hasta cumplir todos estos puntos:

1. `Cliente` critico certificado
2. `PDF/reportes` criticos certificados
3. al menos 3 o 4 pantallas BO representativas con mutacion real certificadas
4. formularios obligatorios o legales probados en runtime real
5. sin errores estructurales abiertos de compatibilidad Linux/MySQL
6. plan de rollback listo

## Definicion practica de go/no-go

### GO

Solo si:

- los dominios de riesgo alto estan `CERTIFICADOS`
- los dominios medios estan al menos `PARCIALES` con muestreo amplio
- no quedan bugs estructurales abiertos
- existe rollback claro

### NO-GO

Si ocurre cualquiera de estos:

- formularios o PDF legales sin evidencia
- stock, cobranzas, proveedores o contabilidad sin mutacion real validada
- errores de sesion, permisos o runtime todavia intermitentes
- modulos criticos con evidencia aislada pero no reproducible

## Siguiente uso recomendado

Usar esta matriz como tablero de decision para los proximos lotes:

1. cerrar remanente de `Lote C` (`sistema` + formularios)
2. ampliar `Lote D` con 2 o 3 pantallas BO mas de distinta familia
3. marcar cada dominio como `CERTIFICADO`, `PARCIAL`, `BLOQUEADO` o `NO RELEVADO`
4. recien despues discutir fecha de corte a Ubuntu productivo