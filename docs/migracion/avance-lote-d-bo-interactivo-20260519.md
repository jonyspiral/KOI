# Avance Lote D - BO interactivo - 2026-05-19

## Objetivo del lote

Abrir el siguiente frente despues de `Lote C`, enfocando pantallas BO interactivas en lugar de generacion PDF.

Criterio de entrada:

- `Lote C` quedo saneado a nivel infraestructura PDF
- ya existe una sesion valida reproducible por `curl` contra `/`
- conviene validar ahora render, navegacion y operaciones CRUD simples sobre pantallas BO reales

## Candidatos relevados

Se relevaron modulos BO con estructura clasica `index.php` + `buscar.php` y, en el caso del ABM, tambien `agregar.php` / `editar.php` / `borrar.php`.

### ABM simple elegido

- `content/abm/bancos`

Motivo:

- modulo corto y acotado
- estructura CRUD completa
- formulario simple (`nombre`, `codigoBanco`)
- sirve para validar alta, edicion, busqueda y borrado sin demasiadas dependencias laterales

### Pantalla BO sensible a datos elegida

- `content/administracion/proveedores/gestion_proveedores`

Motivo:

- usa filtros, carga de resultados, acciones contextuales y navegacion secundaria
- depende de datos reales y de interaccion BO tipica
- es suficientemente representativa sin saltar a una pantalla extrema como `nota_de_pedido`

## Estado de Lote D

Subestado actual:

- relevamiento inicial: `COMPLETADO`
- candidatos elegidos: `COMPLETADO`
- smoke test interactivo: `PARCIALMENTE CERRADO`

## Evidencia validada

### content/abm/bancos

Validado en runtime real con sesion `curl` contra `/`:

- carga de `/abm/bancos/`
- `buscar.php` via `GET` con `id=1`
- `agregar.php` via `POST`
- `editar.php` via `POST`
- `borrar.php` via `POST`

Hallazgos:

- `buscar.php` usa `Funciones::get('id')`; por eso las pruebas iniciales con `-d 'id=...'` devolvian objeto vacio
- `borrar.php` realiza baja logica, no borrado fisico; el banco queda con `fechaBaja` y `fechaUltimaMod`
- el CRUD base del ABM quedo validado de punta a punta

### content/administracion/proveedores/gestion_proveedores

Validado en runtime real:

- carga de `/administracion/proveedores/gestion_proveedores/`
- `buscar.php` responde HTML de tabla con datos reales
- `editar.php` persiste `observacionesGestion` sobre proveedor real (`idProveedor=810`)
- reconsulta por `idProveedor` devuelve la fila del proveedor y confirma consistencia funcional del flujo

Contrato confirmado por codigo:

- `buscar.php` usa `GET`
- `editar.php` usa `POST` con `idProveedor` y `observaciones`
- click en nombre abre `cuenta_corriente_proveedor`
- click en saldo abre `aplicacion`

Fixes aplicados:

- `content/administracion/proveedores/gestion_proveedores/editar.php`
- mensaje corregido de `orrectamente` a `correctamente`
- carga de proveedor ajustada de `getProveedor($idProveedor)` a `getProveedorTodos($idProveedor)` para alinear el id usado por la grilla (`cod_prov`) con el objeto editable

## Dependencias previas

Antes de declarar completo `Lote D`, sigue recomendado cerrar de forma minima estos checks pendientes de `Lote C`:

1. `content/sistema/auditoria/calificacion_clientes/getPdf.php`
2. un flujo real basado en `Formulario.php`
3. smoke test puntual de `FormularioGuiaDePorte.php`

## Siguiente paso recomendado

1. consolidar commit/push de `Lote C` + cierre parcial de `Lote D`
2. continuar con otra pantalla BO interactiva (`administracion/cobranzas/gestion_cobranza` o `comercial/pedidos/nota_de_pedido`)