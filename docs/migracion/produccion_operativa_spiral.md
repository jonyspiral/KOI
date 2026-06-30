# Produccion Operativa - Dependencias SQL (origen obligatorio: spiral)

Alcance analizado: `content/produccion/`, `clases/OrdenDeFabricacion.php`, `clases/TareaProduccion.php`, `clases/TareaProduccionItem.php`, `factory/Mapper.php` (metodos vinculados a Produccion), `factory/Datos.php`, `factory/Factory.php`.

Exclusiones aplicadas: `fonts/`, `* - copia.php`, `vendor`, `node_modules`, imagenes, `css`, `js`, logs, comprimidos y documentacion generica.

Registros relevados: **160**. Objetos unicos: **59**.

## Resumen por objeto

| objeto SQL | tipo | refs | operaciones | archivos | prioridad | estado | origen |
|---|---|---:|---|---:|---|---|---|
| ajustes_stock | tabla | 3 | insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| ajustes_stock_mp | tabla | 3 | insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| almacenes_por_seccion | tabla | 2 | borrado, insercion | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| almacenes_por_seccion_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| colores_por_articulo | tabla | 1 | actualizacion | 1 | SOPORTE | confirmado por codigo | spiral |
| confirmaciones_stock | tabla | 4 | actualizacion, insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| consumos_stock_mp | tabla | 3 | insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| explosion_lote_temp | tabla | 2 | actualizacion, lectura | 1 | EJECUCION | confirmado por codigo | spiral |
| ficha_tecnica_patrones_d | view | 1 | lectura | 1 | NUCLEO | confirmado por codigo | spiral |
| instrucciones_articulo | tabla | 4 | actualizacion, insercion, lectura | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| Materias_primas | tabla | 3 | actualizacion, insercion | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| materias_primas_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| modulos_basicos | tabla | 4 | actualizacion, insercion, lectura | 1 | SOPORTE | confirmado por codigo, inferido | spiral |
| movimientos_almacen | tabla | 3 | insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| movimientos_almacen_confirmacion | tabla | 5 | actualizacion, insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| movimientos_almacen_confirmacion_mp | tabla | 5 | actualizacion, insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| movimientos_almacen_mp | tabla | 3 | insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| movimientos_stock | tabla | 3 | insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| movimientos_stock_mp | tabla | 3 | insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| movimientos_stock_mp_v | view | 2 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| movimientos_stock_v | view | 2 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| Orden_fabricacion | tabla | 6 | actualizacion, insercion, lectura | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| ordenes_compra_cabecera | tabla | 4 | actualizacion, insercion, lectura | 1 | EJECUCION | confirmado por codigo, inferido | spiral |
| ordenes_compra_cabecera_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| ordenes_compra_detalle | tabla | 1 | actualizacion | 1 | EJECUCION | confirmado por codigo | spiral |
| Ordenes_compra_detalle | tabla | 4 | actualizacion, insercion, lectura | 1 | EJECUCION | confirmado por codigo, inferido | spiral |
| Ordenes_compra_detalle_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| Pasos_rutas_produccion | tabla | 4 | actualizacion, insercion, lectura | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| Patrones_mp_cabecera | tabla | 1 | lectura | 1 | NUCLEO | confirmado por codigo | spiral |
| patrones_mp_cabecera | tabla | 2 | actualizacion, insercion | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| Patrones_mp_detalle | tabla | 3 | borrado, lectura | 1 | NUCLEO | confirmado por codigo | spiral |
| patrones_mp_detalle | tabla | 1 | insercion | 1 | NUCLEO | inferido | spiral |
| Planes_produccion | tabla | 5 | actualizacion, insercion, lectura | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| presupuesto_c | tabla | 5 | actualizacion, insercion, lectura | 1 | EJECUCION | confirmado por codigo, inferido | spiral |
| presupuesto_d | tabla | 6 | actualizacion, borrado, insercion, lectura | 1 | EJECUCION | confirmado por codigo, inferido | spiral |
| presupuesto_orden_compra | tabla | 4 | borrado, insercion, lectura | 1 | EJECUCION | confirmado por codigo, inferido | spiral |
| programacion_empaque_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| Proveedores_materias_primas | tabla | 3 | actualizacion, insercion | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| Proveedores_materias_primas_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| remito_orden_de_compra | tabla | 5 | actualizacion, borrado, insercion, lectura | 1 | EJECUCION | confirmado por codigo, inferido | spiral |
| REVISAR_MANUALMENTE_SP_DINAMICO | procedure | 2 | llamada | 2 | SOPORTE | revisar manualmente | spiral |
| rutas_produccion | tabla | 4 | actualizacion, insercion, lectura | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| Rutas_produccion | tabla | 2 | actualizacion, lectura | 1 | NUCLEO | confirmado por codigo | spiral |
| secciones_produccion | tabla | 5 | actualizacion, insercion, lectura | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| sp_stock_a_fecha | procedure | 1 | llamada | 1 | STOCK_MATERIALES | confirmado por codigo | spiral |
| sp_stock_mp_a_fecha | procedure | 1 | llamada | 1 | STOCK_MATERIALES | confirmado por codigo | spiral |
| stock | tabla | 3 | actualizacion, insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| stock_mp_tabla | tabla | 3 | actualizacion, insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| stock_mp_vw | view | 2 | lectura | 2 | REPORTES | confirmado por codigo | spiral |
| stock_pt | view | 1 | lectura | 1 | STOCK_MATERIALES | confirmado por codigo | spiral |
| Tareas_cabecera | tabla | 4 | actualizacion, insercion, lectura | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| tareas_cabecera_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| Tareas_detalle | tabla | 2 | actualizacion, insercion | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| tareas_detalle_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |
| tipo_producto_stock | tabla | 4 | actualizacion, insercion, lectura | 1 | STOCK_MATERIALES | confirmado por codigo, inferido | spiral |
| Unidades_medida | tabla | 3 | actualizacion, insercion, lectura | 1 | SOPORTE | confirmado por codigo, inferido | spiral |
| usuarios_por_almacen | view | 2 | lectura | 2 | STOCK_MATERIALES | confirmado por codigo | spiral |
| usuarios_por_seccion | tabla | 2 | borrado, insercion | 1 | NUCLEO | confirmado por codigo, inferido | spiral |
| usuarios_por_seccion_v | view | 1 | lectura | 1 | REPORTES | confirmado por codigo | spiral |

## Dependencias detalladas

Ver detalle completo en `docs/migracion/produccion_operativa_spiral.csv`.

| objeto SQL | tipo | operacion | archivo:linea | dependencia funcional previa | dependencia SQL declarada | prioridad | origen | estado |
|---|---|---|---|---|---|---|---|---|
| ficha_tecnica_patrones_d | view | lectura | content/produccion/producto/ficha_tecnica/buscar.php:97 | Consulta de UI/servicio en content/produccion/producto/ficha_tecnica/buscar.php | View invocada explicitamente por Factory::getArrayFromView | NUCLEO | spiral | confirmado por codigo |
| instrucciones_articulo | tabla | lectura | factory/Mapper.php:9587 | InstruccionArticulo via getListObject en content/produccion/producto/ficha_tecnica/buscar.php:62 | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| instrucciones_articulo | tabla | insercion | factory/Mapper.php:9592 | InstruccionArticulo via getListObject en content/produccion/producto/ficha_tecnica/buscar.php:62 | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| instrucciones_articulo | tabla | actualizacion | factory/Mapper.php:9608 | InstruccionArticulo via getListObject en content/produccion/producto/ficha_tecnica/buscar.php:62 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| instrucciones_articulo | tabla | actualizacion | factory/Mapper.php:9615 | InstruccionArticulo via getListObject en content/produccion/producto/ficha_tecnica/buscar.php:62 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Orden_fabricacion | tabla | lectura | factory/Mapper.php:10856 | Mapper::OrdenDeFabricacion (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Orden_fabricacion | tabla | insercion | factory/Mapper.php:10859 | Mapper::OrdenDeFabricacion (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| Orden_fabricacion | tabla | actualizacion | factory/Mapper.php:10903 | Mapper::OrdenDeFabricacion (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Orden_fabricacion | tabla | actualizacion | factory/Mapper.php:10914 | Mapper::OrdenDeFabricacion (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Orden_fabricacion | tabla | lectura | factory/Mapper.php:10918 | Mapper::OrdenDeFabricacion (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Orden_fabricacion | tabla | actualizacion | factory/Mapper.php:11689 | LoteDeProduccion via getListObject en content/produccion/gestion_produccion/lotes_produccion/buscar.php:21 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Pasos_rutas_produccion | tabla | lectura | factory/Mapper.php:13339 | Mapper::RutaProduccionPaso (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Pasos_rutas_produccion | tabla | insercion | factory/Mapper.php:13345 | Mapper::RutaProduccionPaso (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| Pasos_rutas_produccion | tabla | actualizacion | factory/Mapper.php:13373 | Mapper::RutaProduccionPaso (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Pasos_rutas_produccion | tabla | actualizacion | factory/Mapper.php:13385 | Mapper::RutaProduccionPaso (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Patrones_mp_cabecera | tabla | lectura | factory/Mapper.php:11120 | Patron via getListObject en content/produccion/producto/ficha_tecnica/buscar.php:266 | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| patrones_mp_cabecera | tabla | insercion | factory/Mapper.php:11125 | Patron via getListObject en content/produccion/producto/ficha_tecnica/buscar.php:266 | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| patrones_mp_cabecera | tabla | actualizacion | factory/Mapper.php:11156 | Patron via getListObject en content/produccion/producto/ficha_tecnica/buscar.php:266 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Patrones_mp_detalle | tabla | borrado | factory/Mapper.php:11151 | Patron via getListObject en content/produccion/producto/ficha_tecnica/buscar.php:266 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Patrones_mp_detalle | tabla | lectura | factory/Mapper.php:11182 | Mapper::PatronItem (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| patrones_mp_detalle | tabla | insercion | factory/Mapper.php:11188 | Mapper::PatronItem (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| Patrones_mp_detalle | tabla | lectura | factory/Mapper.php:11234 | Mapper::PatronItem (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Planes_produccion | tabla | lectura | factory/Mapper.php:11668 | LoteDeProduccion via getListObject en content/produccion/gestion_produccion/lotes_produccion/buscar.php:21 | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Planes_produccion | tabla | insercion | factory/Mapper.php:11671 | LoteDeProduccion via getListObject en content/produccion/gestion_produccion/lotes_produccion/buscar.php:21 | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| Planes_produccion | tabla | actualizacion | factory/Mapper.php:11685 | LoteDeProduccion via getListObject en content/produccion/gestion_produccion/lotes_produccion/buscar.php:21 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Planes_produccion | tabla | actualizacion | factory/Mapper.php:11693 | LoteDeProduccion via getListObject en content/produccion/gestion_produccion/lotes_produccion/buscar.php:21 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Planes_produccion | tabla | lectura | factory/Mapper.php:11698 | LoteDeProduccion via getListObject en content/produccion/gestion_produccion/lotes_produccion/buscar.php:21 | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| rutas_produccion | tabla | lectura | factory/Mapper.php:13299 | Mapper::RutaProduccion (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| rutas_produccion | tabla | insercion | factory/Mapper.php:13302 | Mapper::RutaProduccion (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| rutas_produccion | tabla | actualizacion | factory/Mapper.php:13312 | Mapper::RutaProduccion (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Rutas_produccion | tabla | actualizacion | factory/Mapper.php:13320 | Mapper::RutaProduccion (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| rutas_produccion | tabla | lectura | factory/Mapper.php:13325 | Mapper::RutaProduccion (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Rutas_produccion | tabla | lectura | factory/Mapper.php:13344 | Mapper::RutaProduccionPaso (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| secciones_produccion | tabla | lectura | factory/Mapper.php:13403 | Mapper::SeccionProduccion (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| secciones_produccion | tabla | insercion | factory/Mapper.php:13406 | Mapper::SeccionProduccion (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| secciones_produccion | tabla | actualizacion | factory/Mapper.php:13436 | Mapper::SeccionProduccion (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| secciones_produccion | tabla | actualizacion | factory/Mapper.php:13450 | Mapper::SeccionProduccion (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| secciones_produccion | tabla | lectura | factory/Mapper.php:13455 | Mapper::SeccionProduccion (uso indirecto en Produccion) | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Tareas_cabecera | tabla | insercion | factory/Mapper.php:13903 | TareaProduccion via getListObject en clases/OrdenDeFabricacion.php:163 | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| Tareas_cabecera | tabla | actualizacion | factory/Mapper.php:13951 | TareaProduccion via getListObject en clases/OrdenDeFabricacion.php:163 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| Tareas_cabecera | tabla | lectura | factory/Mapper.php:13960 | TareaProduccion via getListObject en clases/OrdenDeFabricacion.php:163 | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Tareas_cabecera | tabla | lectura | factory/Mapper.php:14046 | TareaProduccionItem via getListObject en content/produccion/gestion_produccion/confirmacion/buscar.php:63 | Lectura directa desde mapper | NUCLEO | spiral | confirmado por codigo |
| Tareas_detalle | tabla | insercion | factory/Mapper.php:13979 | TareaProduccionItem via getListObject en content/produccion/gestion_produccion/confirmacion/buscar.php:63 | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| Tareas_detalle | tabla | actualizacion | factory/Mapper.php:14037 | TareaProduccionItem via getListObject en content/produccion/gestion_produccion/confirmacion/buscar.php:63 | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| usuarios_por_seccion | tabla | insercion | factory/Mapper.php:14794 | Mapper::UsuarioPorSeccionProduccion (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | NUCLEO | spiral | inferido |
| usuarios_por_seccion | tabla | borrado | factory/Mapper.php:14802 | Mapper::UsuarioPorSeccionProduccion (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | NUCLEO | spiral | confirmado por codigo |
| explosion_lote_temp | tabla | lectura | factory/Mapper.php:8189 | ExplosionLoteTemp via getListObject en content/produccion/compras/presupuesto/explosion/buscar.php:21 | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| explosion_lote_temp | tabla | actualizacion | factory/Mapper.php:8193 | ExplosionLoteTemp via getListObject en content/produccion/compras/presupuesto/explosion/buscar.php:21 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| ordenes_compra_cabecera | tabla | insercion | factory/Mapper.php:10706 | OrdenDeCompra via getListObject en content/produccion/compras/ordenes_compra/pendiente/buscar.php:63 | Insercion directa; dependencias por claves cod_* | EJECUCION | spiral | inferido |
| ordenes_compra_cabecera | tabla | actualizacion | factory/Mapper.php:10736 | OrdenDeCompra via getListObject en content/produccion/compras/ordenes_compra/pendiente/buscar.php:63 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| ordenes_compra_cabecera | tabla | actualizacion | factory/Mapper.php:10748 | OrdenDeCompra via getListObject en content/produccion/compras/ordenes_compra/pendiente/buscar.php:63 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| ordenes_compra_cabecera | tabla | lectura | factory/Mapper.php:10754 | OrdenDeCompra via getListObject en content/produccion/compras/ordenes_compra/pendiente/buscar.php:63 | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| ordenes_compra_detalle | tabla | actualizacion | factory/Mapper.php:10743 | OrdenDeCompra via getListObject en content/produccion/compras/ordenes_compra/pendiente/buscar.php:63 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| Ordenes_compra_detalle | tabla | insercion | factory/Mapper.php:10772 | OrdenDeCompraItem via getListObject en content/produccion/compras/ordenes_compra/descontar_pendiente/buscar.php:58 | Insercion directa; dependencias por claves cod_* | EJECUCION | spiral | inferido |
| Ordenes_compra_detalle | tabla | actualizacion | factory/Mapper.php:10822 | OrdenDeCompraItem via getListObject en content/produccion/compras/ordenes_compra/descontar_pendiente/buscar.php:58 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| Ordenes_compra_detalle | tabla | actualizacion | factory/Mapper.php:10834 | OrdenDeCompraItem via getListObject en content/produccion/compras/ordenes_compra/descontar_pendiente/buscar.php:58 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| Ordenes_compra_detalle | tabla | lectura | factory/Mapper.php:10841 | OrdenDeCompraItem via getListObject en content/produccion/compras/ordenes_compra/descontar_pendiente/buscar.php:58 | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_c | tabla | lectura | factory/Mapper.php:11837 | Presupuesto via getListObject en content/produccion/compras/ordenes_compra/generacion/buscar.php:64 | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_c | tabla | insercion | factory/Mapper.php:11840 | Presupuesto via getListObject en content/produccion/compras/ordenes_compra/generacion/buscar.php:64 | Insercion directa; dependencias por claves cod_* | EJECUCION | spiral | inferido |
| presupuesto_c | tabla | actualizacion | factory/Mapper.php:11864 | Presupuesto via getListObject en content/produccion/compras/ordenes_compra/generacion/buscar.php:64 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_c | tabla | actualizacion | factory/Mapper.php:11872 | Presupuesto via getListObject en content/produccion/compras/ordenes_compra/generacion/buscar.php:64 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_c | tabla | lectura | factory/Mapper.php:11878 | Presupuesto via getListObject en content/produccion/compras/ordenes_compra/generacion/buscar.php:64 | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_d | tabla | borrado | factory/Mapper.php:11862 | Presupuesto via getListObject en content/produccion/compras/ordenes_compra/generacion/buscar.php:64 | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_d | tabla | lectura | factory/Mapper.php:11892 | Mapper::PresupuestoItem (uso indirecto en Produccion) | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_d | tabla | insercion | factory/Mapper.php:11896 | Mapper::PresupuestoItem (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | EJECUCION | spiral | inferido |
| presupuesto_d | tabla | actualizacion | factory/Mapper.php:11930 | Mapper::PresupuestoItem (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_d | tabla | actualizacion | factory/Mapper.php:11945 | Mapper::PresupuestoItem (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_d | tabla | lectura | factory/Mapper.php:11952 | Mapper::PresupuestoItem (uso indirecto en Produccion) | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_orden_compra | tabla | lectura | factory/Mapper.php:11967 | Mapper::PresupuestoOrdenCompra (uso indirecto en Produccion) | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_orden_compra | tabla | insercion | factory/Mapper.php:11970 | Mapper::PresupuestoOrdenCompra (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | EJECUCION | spiral | inferido |
| presupuesto_orden_compra | tabla | borrado | factory/Mapper.php:11985 | Mapper::PresupuestoOrdenCompra (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| presupuesto_orden_compra | tabla | lectura | factory/Mapper.php:11988 | Mapper::PresupuestoOrdenCompra (uso indirecto en Produccion) | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| remito_orden_de_compra | tabla | lectura | factory/Mapper.php:12614 | Mapper::RemitoPorOrdenDeCompra (uso indirecto en Produccion) | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| remito_orden_de_compra | tabla | insercion | factory/Mapper.php:12617 | Mapper::RemitoPorOrdenDeCompra (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | EJECUCION | spiral | inferido |
| remito_orden_de_compra | tabla | actualizacion | factory/Mapper.php:12647 | Mapper::RemitoPorOrdenDeCompra (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| remito_orden_de_compra | tabla | borrado | factory/Mapper.php:12653 | Mapper::RemitoPorOrdenDeCompra (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | EJECUCION | spiral | confirmado por codigo |
| remito_orden_de_compra | tabla | lectura | factory/Mapper.php:12656 | Mapper::RemitoPorOrdenDeCompra (uso indirecto en Produccion) | Lectura directa desde mapper | EJECUCION | spiral | confirmado por codigo |
| ajustes_stock | tabla | lectura | factory/Mapper.php:4123 | Mapper::AjusteStock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| ajustes_stock | tabla | insercion | factory/Mapper.php:4126 | Mapper::AjusteStock (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| ajustes_stock | tabla | lectura | factory/Mapper.php:4154 | Mapper::AjusteStock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| ajustes_stock_mp | tabla | lectura | factory/Mapper.php:4168 | Mapper::AjusteStockMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| ajustes_stock_mp | tabla | insercion | factory/Mapper.php:4171 | Mapper::AjusteStockMP (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| ajustes_stock_mp | tabla | lectura | factory/Mapper.php:4199 | Mapper::AjusteStockMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| almacenes_por_seccion | tabla | insercion | factory/Mapper.php:4256 | Mapper::AlmacenPorSeccion (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| almacenes_por_seccion | tabla | borrado | factory/Mapper.php:4264 | Mapper::AlmacenPorSeccion (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| confirmaciones_stock | tabla | lectura | factory/Mapper.php:5893 | Mapper::ConfirmacionStock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| confirmaciones_stock | tabla | insercion | factory/Mapper.php:5896 | Mapper::ConfirmacionStock (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| confirmaciones_stock | tabla | actualizacion | factory/Mapper.php:5921 | Mapper::ConfirmacionStock (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| confirmaciones_stock | tabla | lectura | factory/Mapper.php:5927 | Mapper::ConfirmacionStock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| consumos_stock_mp | tabla | lectura | factory/Mapper.php:6070 | Mapper::ConsumoStockMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| consumos_stock_mp | tabla | insercion | factory/Mapper.php:6073 | Mapper::ConsumoStockMP (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| consumos_stock_mp | tabla | lectura | factory/Mapper.php:6097 | Mapper::ConsumoStockMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| Materias_primas | tabla | insercion | factory/Mapper.php:5658 | ColorMateriaPrima via getListObject en content/produccion/compras/ordenes_compra/generacion/agregar.php:129 | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| Materias_primas | tabla | actualizacion | factory/Mapper.php:5675 | ColorMateriaPrima via getListObject en content/produccion/compras/ordenes_compra/generacion/agregar.php:129 | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| Materias_primas | tabla | actualizacion | factory/Mapper.php:5683 | ColorMateriaPrima via getListObject en content/produccion/compras/ordenes_compra/generacion/agregar.php:129 | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen | tabla | lectura | factory/Mapper.php:9883 | Mapper::MovimientoAlmacen (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen | tabla | insercion | factory/Mapper.php:9886 | Mapper::MovimientoAlmacen (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| movimientos_almacen | tabla | lectura | factory/Mapper.php:9916 | Mapper::MovimientoAlmacen (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_confirmacion | tabla | lectura | factory/Mapper.php:9930 | MovimientoAlmacenConfirmacion via getListObject en content/produccion/stock/confirmacion_movimiento_almacen/buscar.php:78 | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_confirmacion | tabla | insercion | factory/Mapper.php:9933 | MovimientoAlmacenConfirmacion via getListObject en content/produccion/stock/confirmacion_movimiento_almacen/buscar.php:78 | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| movimientos_almacen_confirmacion | tabla | actualizacion | factory/Mapper.php:9963 | MovimientoAlmacenConfirmacion via getListObject en content/produccion/stock/confirmacion_movimiento_almacen/buscar.php:78 | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_confirmacion | tabla | actualizacion | factory/Mapper.php:9969 | MovimientoAlmacenConfirmacion via getListObject en content/produccion/stock/confirmacion_movimiento_almacen/buscar.php:78 | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_confirmacion | tabla | lectura | factory/Mapper.php:9975 | MovimientoAlmacenConfirmacion via getListObject en content/produccion/stock/confirmacion_movimiento_almacen/buscar.php:78 | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_confirmacion_mp | tabla | lectura | factory/Mapper.php:10036 | MovimientoAlmacenConfirmacionMP via getListObject en content/produccion/stock_mp/confirmacion_movimiento_almacen/buscar.php:79 | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_confirmacion_mp | tabla | insercion | factory/Mapper.php:10039 | MovimientoAlmacenConfirmacionMP via getListObject en content/produccion/stock_mp/confirmacion_movimiento_almacen/buscar.php:79 | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| movimientos_almacen_confirmacion_mp | tabla | actualizacion | factory/Mapper.php:10069 | MovimientoAlmacenConfirmacionMP via getListObject en content/produccion/stock_mp/confirmacion_movimiento_almacen/buscar.php:79 | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_confirmacion_mp | tabla | actualizacion | factory/Mapper.php:10075 | MovimientoAlmacenConfirmacionMP via getListObject en content/produccion/stock_mp/confirmacion_movimiento_almacen/buscar.php:79 | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_confirmacion_mp | tabla | lectura | factory/Mapper.php:10081 | MovimientoAlmacenConfirmacionMP via getListObject en content/produccion/stock_mp/confirmacion_movimiento_almacen/buscar.php:79 | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_mp | tabla | lectura | factory/Mapper.php:9989 | Mapper::MovimientoAlmacenMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_almacen_mp | tabla | insercion | factory/Mapper.php:9992 | Mapper::MovimientoAlmacenMP (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| movimientos_almacen_mp | tabla | lectura | factory/Mapper.php:10022 | Mapper::MovimientoAlmacenMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_stock | tabla | lectura | factory/Mapper.php:10095 | Mapper::MovimientoStock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_stock | tabla | insercion | factory/Mapper.php:10098 | Mapper::MovimientoStock (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| movimientos_stock | tabla | lectura | factory/Mapper.php:10130 | Mapper::MovimientoStock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_stock_mp | tabla | lectura | factory/Mapper.php:10144 | Mapper::MovimientoStockMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| movimientos_stock_mp | tabla | insercion | factory/Mapper.php:10147 | Mapper::MovimientoStockMP (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| movimientos_stock_mp | tabla | lectura | factory/Mapper.php:10179 | Mapper::MovimientoStockMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| Proveedores_materias_primas | tabla | insercion | factory/Mapper.php:12169 | ProveedorMateriaPrima via getListObject en content/produccion/compras/ordenes_compra/generacion/agregar.php:113 | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| Proveedores_materias_primas | tabla | actualizacion | factory/Mapper.php:12191 | ProveedorMateriaPrima via getListObject en content/produccion/compras/ordenes_compra/generacion/agregar.php:113 | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| Proveedores_materias_primas | tabla | actualizacion | factory/Mapper.php:12198 | ProveedorMateriaPrima via getListObject en content/produccion/compras/ordenes_compra/generacion/agregar.php:113 | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| sp_stock_a_fecha | procedure | llamada | content/produccion/stock/stock_a_fecha/buscar.php:54 | Calculo operativo en content/produccion/stock/stock_a_fecha/buscar.php | Stored procedure invocado por Factory::getArrayFromStoredProcedure | STOCK_MATERIALES | spiral | confirmado por codigo |
| sp_stock_mp_a_fecha | procedure | llamada | content/produccion/stock_mp/stock_a_fecha/buscar.php:53 | Calculo operativo en content/produccion/stock_mp/stock_a_fecha/buscar.php | Stored procedure invocado por Factory::getArrayFromStoredProcedure | STOCK_MATERIALES | spiral | confirmado por codigo |
| stock | tabla | lectura | factory/Mapper.php:13619 | Mapper::Stock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| stock | tabla | insercion | factory/Mapper.php:13624 | Mapper::Stock (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| stock | tabla | actualizacion | factory/Mapper.php:13640 | Mapper::Stock (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| stock_mp_tabla | tabla | lectura | factory/Mapper.php:13668 | Mapper::StockMP (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| stock_mp_tabla | tabla | insercion | factory/Mapper.php:13673 | Mapper::StockMP (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| stock_mp_tabla | tabla | actualizacion | factory/Mapper.php:13689 | Mapper::StockMP (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| stock_pt | view | lectura | content/produccion/stock/movimiento_almacen/buscar.php:70 | Consulta de UI/servicio en content/produccion/stock/movimiento_almacen/buscar.php | View invocada explicitamente por Factory::getArrayFromView | STOCK_MATERIALES | spiral | confirmado por codigo |
| tipo_producto_stock | tabla | lectura | factory/Mapper.php:14246 | Mapper::TipoProductoStock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| tipo_producto_stock | tabla | insercion | factory/Mapper.php:14249 | Mapper::TipoProductoStock (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | STOCK_MATERIALES | spiral | inferido |
| tipo_producto_stock | tabla | actualizacion | factory/Mapper.php:14263 | Mapper::TipoProductoStock (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| tipo_producto_stock | tabla | lectura | factory/Mapper.php:14270 | Mapper::TipoProductoStock (uso indirecto en Produccion) | Lectura directa desde mapper | STOCK_MATERIALES | spiral | confirmado por codigo |
| usuarios_por_almacen | view | lectura | content/produccion/stock/confirmacion_movimiento_almacen/buscar.php:39 | Consulta de UI/servicio en content/produccion/stock/confirmacion_movimiento_almacen/buscar.php | View invocada explicitamente por Factory::getArrayFromView | STOCK_MATERIALES | spiral | confirmado por codigo |
| usuarios_por_almacen | view | lectura | content/produccion/stock_mp/confirmacion_movimiento_almacen/buscar.php:42 | Consulta de UI/servicio en content/produccion/stock_mp/confirmacion_movimiento_almacen/buscar.php | View invocada explicitamente por Factory::getArrayFromView | STOCK_MATERIALES | spiral | confirmado por codigo |
| almacenes_por_seccion_v | view | lectura | factory/Mapper.php:4252 | Mapper::AlmacenPorSeccion (uso indirecto en Produccion) | Lectura directa desde mapper | REPORTES | spiral | confirmado por codigo |
| materias_primas_v | view | lectura | factory/Mapper.php:5654 | ColorMateriaPrima via getListObject en content/produccion/compras/ordenes_compra/generacion/agregar.php:129 | Lectura directa desde mapper | REPORTES | spiral | confirmado por codigo |
| movimientos_stock_mp_v | view | lectura | content/produccion/stock_mp/movimientos/buscar.php:45 | Consulta de UI/servicio en content/produccion/stock_mp/movimientos/buscar.php | View invocada explicitamente por Factory::getArrayFromView | REPORTES | spiral | confirmado por codigo |
| movimientos_stock_mp_v | view | lectura | content/produccion/stock_mp/movimientos/buscar.php:57 | Consulta de UI/servicio en content/produccion/stock_mp/movimientos/buscar.php | View invocada explicitamente por Factory::getArrayFromView | REPORTES | spiral | confirmado por codigo |
| movimientos_stock_v | view | lectura | content/produccion/stock/movimientos/buscar.php:46 | Consulta de UI/servicio en content/produccion/stock/movimientos/buscar.php | View invocada explicitamente por Factory::getArrayFromView | REPORTES | spiral | confirmado por codigo |
| movimientos_stock_v | view | lectura | content/produccion/stock/movimientos/buscar.php:58 | Consulta de UI/servicio en content/produccion/stock/movimientos/buscar.php | View invocada explicitamente por Factory::getArrayFromView | REPORTES | spiral | confirmado por codigo |
| ordenes_compra_cabecera_v | view | lectura | factory/Mapper.php:10703 | OrdenDeCompra via getListObject en content/produccion/compras/ordenes_compra/pendiente/buscar.php:63 | Lectura directa desde mapper | REPORTES | spiral | confirmado por codigo |
| Ordenes_compra_detalle_v | view | lectura | factory/Mapper.php:10768 | OrdenDeCompraItem via getListObject en content/produccion/compras/ordenes_compra/descontar_pendiente/buscar.php:58 | Lectura directa desde mapper | REPORTES | spiral | confirmado por codigo |
| programacion_empaque_v | view | lectura | content/produccion/reportes/programacion_empaque/buscar.php:41 | Consulta de UI/servicio en content/produccion/reportes/programacion_empaque/buscar.php | View invocada explicitamente por Factory::getArrayFromView | REPORTES | spiral | confirmado por codigo |
| Proveedores_materias_primas_v | view | lectura | factory/Mapper.php:12164 | ProveedorMateriaPrima via getListObject en content/produccion/compras/ordenes_compra/generacion/agregar.php:113 | Lectura directa desde mapper | REPORTES | spiral | confirmado por codigo |
| stock_mp_vw | view | lectura | content/produccion/stock_mp/consumos/buscar.php:73 | Consulta de UI/servicio en content/produccion/stock_mp/consumos/buscar.php | View invocada explicitamente por Factory::getArrayFromView | REPORTES | spiral | confirmado por codigo |
| stock_mp_vw | view | lectura | content/produccion/stock_mp/movimiento_almacen/buscar.php:73 | Consulta de UI/servicio en content/produccion/stock_mp/movimiento_almacen/buscar.php | View invocada explicitamente por Factory::getArrayFromView | REPORTES | spiral | confirmado por codigo |
| tareas_cabecera_v | view | lectura | factory/Mapper.php:13899 | TareaProduccion via getListObject en clases/OrdenDeFabricacion.php:163 | Lectura directa desde mapper | REPORTES | spiral | confirmado por codigo |
| tareas_detalle_v | view | lectura | factory/Mapper.php:13974 | TareaProduccionItem via getListObject en content/produccion/gestion_produccion/confirmacion/buscar.php:63 | Lectura directa desde mapper | REPORTES | spiral | confirmado por codigo |
| usuarios_por_seccion_v | view | lectura | factory/Mapper.php:14790 | Mapper::UsuarioPorSeccionProduccion (uso indirecto en Produccion) | Lectura directa desde mapper | REPORTES | spiral | confirmado por codigo |
| colores_por_articulo | tabla | actualizacion | factory/Mapper.php:13648 | Mapper::Stock (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | SOPORTE | spiral | confirmado por codigo |
| modulos_basicos | tabla | lectura | factory/Mapper.php:6315 | Mapper::CurvaProduccionPorArticulo (uso indirecto en Produccion) | Lectura directa desde mapper | SOPORTE | spiral | confirmado por codigo |
| modulos_basicos | tabla | insercion | factory/Mapper.php:6318 | Mapper::CurvaProduccionPorArticulo (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | SOPORTE | spiral | inferido |
| modulos_basicos | tabla | actualizacion | factory/Mapper.php:6338 | Mapper::CurvaProduccionPorArticulo (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | SOPORTE | spiral | confirmado por codigo |
| modulos_basicos | tabla | actualizacion | factory/Mapper.php:6345 | Mapper::CurvaProduccionPorArticulo (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | SOPORTE | spiral | confirmado por codigo |
| REVISAR_MANUALMENTE_SP_DINAMICO | procedure | llamada | factory/Datos.php:124 | Abstraccion de SP usada por Factory y modulo Produccion | CALL $name(...) dinamico | SOPORTE | spiral | revisar manualmente |
| REVISAR_MANUALMENTE_SP_DINAMICO | procedure | llamada | factory/Factory.php:123 | Factory::getListFromStoredProcedure para Produccion | Datos::EjecutarStoredProcedure($storedProcedureName, ...) | SOPORTE | spiral | revisar manualmente |
| Unidades_medida | tabla | lectura | factory/Mapper.php:14574 | Mapper::UnidadDeMedida (uso indirecto en Produccion) | Lectura directa desde mapper | SOPORTE | spiral | confirmado por codigo |
| Unidades_medida | tabla | insercion | factory/Mapper.php:14577 | Mapper::UnidadDeMedida (uso indirecto en Produccion) | Insercion directa; dependencias por claves cod_* | SOPORTE | spiral | inferido |
| Unidades_medida | tabla | actualizacion | factory/Mapper.php:14585 | Mapper::UnidadDeMedida (uso indirecto en Produccion) | Filtro WHERE declarado en mapper | SOPORTE | spiral | confirmado por codigo |

## Etapas propuestas de migracion (sin ejecucion)

1. **Etapa 1 - NUCLEO**: Migrar maestros y transaccionales base de produccion: ordenes de fabricacion, tareas, rutas, secciones, patrones, lotes, instrucciones.
2. **Etapa 2 - EJECUCION**: Migrar ejecucion operativa de compras/planificacion: ordenes de compra, presupuestos, explosion de lote, remito por orden de compra y confirmaciones.
3. **Etapa 3 - STOCK_MATERIALES**: Migrar stock PT/MP, movimientos, confirmaciones de almacen y consumos de MP; validar saldos y trazabilidad por almacen/color/talle.
4. **Etapa 4 - REPORTES**: Migrar views y procedimientos de consulta: programacion, costos, movimientos y stock a fecha.
5. **Etapa 5 - SOPORTE**: Migrar soporte operativo (usuarios por almacen/seccion, unidades, tipos) y cerrar items de revision manual por SP dinamicos.
