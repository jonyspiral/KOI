CREATE VIEW [dbo].[stock_registros_aux_vw] AS

CREATE VIEW [dbo].[stock_registros_aux_vw]
AS
	/*
		Esta consulta trae registros de cada lugar donde se debe sacar el stock y pone uno por línea
		Los asignados los trae como Almacen '01'. De donde se la llame se debe agrupar y hacer la suma.
		Se usa para la view 'stock_menos_asignado'
	 */
(
	SELECT Tareas_detalle.cod_almacen, Orden_fabricacion.cod_articulo, Orden_fabricacion.cod_color_articulo, Sum(Tareas_detalle.cant_1) c_1, Sum(Tareas_detalle.cant_2) c_2, Sum(Tareas_detalle.cant_3) c_3, Sum(Tareas_detalle.cant_4) c_4, Sum(Tareas_detalle.cant_5) c_5, Sum(Tareas_detalle.cant_6) c_6, Sum(Tareas_detalle.cant_7) c_7, Sum(Tareas_detalle.cant_8) c_8, Sum(Tareas_detalle.cant_9) c_9, Sum(Tareas_detalle.cant_10) c_10, Sum(Tareas_detalle.cant_11) c_11, Sum(Tareas_detalle.cant_12) c_12
	FROM (articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN (Orden_fabricacion INNER JOIN (Tareas_cabecera INNER JOIN Tareas_detalle ON (Tareas_cabecera.nro_tarea = Tareas_detalle.nro_tarea) AND (Tareas_cabecera.nro_orden_fabricacion = Tareas_detalle.nro_orden_fabricacion)) ON Orden_fabricacion.nro_orden_fabricacion = Tareas_cabecera.nro_orden_fabricacion) ON (colores_por_articulo.cod_articulo = Orden_fabricacion.cod_articulo) AND (colores_por_articulo.cod_color_articulo = Orden_fabricacion.cod_color_articulo)
	WHERE ((Tareas_detalle.cod_almacen <> '') AND ((Tareas_detalle.cod_seccion)=60) AND ((articulos.vigente)='s') AND ((colores_por_articulo.vigente)='s') AND ((Tareas_cabecera.anulado)<>'s') AND ((Tareas_detalle.cumplido_paso)='s') AND ((articulos.naturaleza)='PT'))
	GROUP BY Orden_fabricacion.cod_articulo, Orden_fabricacion.cod_color_articulo, Tareas_detalle.cod_almacen
) UNION (
	SELECT Despachos_detalle.cod_almacen, Despachos_detalle.cod_articulo cod_articulo, Despachos_detalle.cod_color cod_color_articulo, Sum(-[entr_1]) c_1, Sum(-[entr_2]) c_2, Sum(-[entr_3]) c_3, Sum(-[entr_4]) c_4, Sum(-[entr_5]) c_5, Sum(-[entr_6]) c_6, Sum(-[entr_7]) c_7, Sum(-[entr_8]) c_8, Sum(-[entr_9]) c_9, Sum(-[entr_10]) c_10, Sum(-[entr_11]) c_11, Sum(-[entr_12]) c_12
	FROM (((dbo.articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN Despachos_detalle ON (colores_por_articulo.cod_color_articulo = Despachos_detalle.cod_color) AND (colores_por_articulo.cod_articulo = Despachos_detalle.cod_articulo)) INNER JOIN Despachos_cabecera ON (Despachos_detalle.nro_despacho = Despachos_cabecera.nro_despacho) AND (Despachos_detalle.cod_sucursal_despacho = Despachos_cabecera.cod_sucursal_despacho) AND (Despachos_detalle.cod_empresa_despacho = Despachos_cabecera.cod_empresa_despacho)) INNER JOIN remitos_cabecera ON (Despachos_detalle.letra_remito = remitos_cabecera.letra_remito) AND (Despachos_detalle.nro_remito = remitos_cabecera.nro_remito) AND (Despachos_detalle.cod_sucursal_despacho = remitos_cabecera.cod_sucursal_despacho) AND (Despachos_detalle.cod_empresa_despacho = remitos_cabecera.cod_empresa_despacho)
	WHERE ((Despachos_detalle.cod_almacen <> '') AND (colores_por_articulo.vigente = 's') AND ((articulos.vigente)='s') AND ((articulos.naturaleza)='PT') AND ((Despachos_cabecera.anulado = 'n')))
	GROUP BY Despachos_detalle.cod_articulo, Despachos_detalle.cod_color, Despachos_detalle.cod_almacen
) UNION (
	--NEGATIVOS
	SELECT prod_terminados_movim_extraor.cod_almacen, prod_terminados_movim_extraor.cod_articulo cod_articulo, prod_terminados_movim_extraor.cod_color_articulo cod_color_articulo, Sum([cant_1]*-1) c_1, Sum([cant_2]*-1) c_2, Sum([cant_3]*-1) c_3, Sum([cant_4]*-1) c_4, Sum([cant_5]*-1) c_5, Sum([cant_6]*-1) c_6, Sum([cant_7]*-1) c_7, Sum([cant_8]*-1) c_8, Sum([cant_9]*-1) c_9, Sum([cant_10]*-1) c_10, Sum([cant_11]*-1) c_11, Sum([cant_12]*-1) c_12
	FROM (dbo.articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN dbo.prod_terminados_movim_extraor ON (colo
res_por_articulo.cod_articulo = prod_terminados_movim_extraor.cod_articulo) AND (colores_por_articulo.cod_color_articulo = prod_terminados_movim_extraor.cod_color_articulo)
	WHERE ((prod_terminados_movim_extraor.cod_almacen <> '') AND ((articulos.vigente)='s') AND ((colores_por_articulo.vigente)='s') AND ((articulos.naturaleza)='PT') AND (((prod_terminados_movim_extraor.tipo_movimiento)='aju_neg' Or (prod_terminados_movim_extraor.tipo_movimiento)='SINIES')))
	GROUP BY prod_terminados_movim_extraor.cod_almacen, prod_terminados_movim_extraor.cod_articulo, prod_terminados_movim_extraor.cod_color_articulo
) UNION (
	--POSITIVOS
	SELECT prod_terminados_movim_extraor.cod_almacen, prod_terminados_movim_extraor.cod_articulo cod_articulo, prod_terminados_movim_extraor.cod_color_articulo cod_color_articulo, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_1 ELSE cant_1 END) c_1, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_2 ELSE cant_2 END) c_2, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_3 ELSE cant_3 END) c_3, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_4 ELSE cant_4 END) c_4, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_5 ELSE cant_5 END) c_5, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_6 ELSE cant_6 END) c_6, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_7 ELSE cant_7 END) c_7, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_8 ELSE cant_8 END) c_8, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_9 ELSE cant_9 END) c_9, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_10 ELSE cant_10 END) c_10, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_11 ELSE cant_11 END) c_11, Sum(CASE tipo_movimiento WHEN 'aju_neg' THEN (-1) * cant_12 ELSE cant_12 END) c_12
	FROM (dbo.articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN dbo.prod_terminados_movim_extraor ON (colores_por_articulo.cod_articulo = prod_terminados_movim_extraor.cod_articulo) AND (colores_por_articulo.cod_color_articulo = prod_terminados_movim_extraor.cod_color_articulo)
	WHERE ((prod_terminados_movim_extraor.cod_almacen <> '') AND ((articulos.vigente)='s') AND ((colores_por_articulo.vigente)='s') AND ((articulos.naturaleza)='PT') AND (((prod_terminados_movim_extraor.tipo_movimiento)<>'aju_neg' AND (prod_terminados_movim_extraor.tipo_movimiento)<>'sinies')))
	GROUP BY prod_terminados_movim_extraor.cod_almacen, prod_terminados_movim_extraor.cod_articulo, prod_terminados_movim_extraor.cod_color_articulo
) UNION (
	SELECT docum_clientes_detalle.cod_almacen, docum_clientes_detalle.cod_articulo cod_articulo, docum_clientes_detalle.cod_color cod_color_articulo, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_1 ELSE -[cant_1] END) c_1, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_2 ELSE -[cant_2] END) c_2, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_3 ELSE -[cant_3] END) c_3, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_4 ELSE -[cant_4] END) c_4, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_5 ELSE -[cant_5] END) c_5, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_6 ELSE -[cant_6] END) c_6, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_7 ELSE -[cant_7] END) c_7, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_8 ELSE -[cant_8] END) c_8, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_9 ELSE -[cant_9] END) c_9, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_10 ELSE -[cant_10] END) c_10, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_11 ELSE -[cant_11] END) c_11, Sum(CASE tipo_doc WHEN 'ncr' THEN cant_12 ELSE -[cant_12] END) c_12
	FROM (articulos INNER JOIN dbo.colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) INNER JOIN dbo.docum_clientes_detalle ON (colores_por_articulo.cod_color_articulo = docum_clientes_detalle.cod_color) AND (colores_por_articulo.cod_articulo = docum_clientes_detalle.cod_articulo)
	WHERE ((docum_clientes_detalle.cod_almacen <> '') AND ((colores_por_articulo.vigente)='s') AND ((articulos.vigente)='s') AND ((articulos.natural
eza)='PT'))
	GROUP BY docum_clientes_detalle.cod_articulo, docum_clientes_detalle.cod_color, docum_clientes_detalle.cod_almacen
) UNION (
	SELECT '01' cod_almacen, cod_articulo, cod_color_articulo, (-1) * SUM(pend_1) c_1, (-1) * SUM(pend_2) c_2, (-1) * SUM(pend_3) c_3, (-1) * SUM(pend_4) c_4, (-1) * SUM(pend_5) c_5, (-1) * SUM(pend_6) c_6, (-1) * SUM(pend_7) c_7, (-1) * SUM(pend_8) c_8, (-1) * SUM(pend_9) c_9, (-1) * SUM(pend_10) c_10, (-1) * SUM(pend_11) c_11, (-1) * SUM(pend_12) c_12
	FROM pedidos_detalle de
	INNER JOIN pedidos_cabecera ca ON ca.nro_pedido = de.nro_pedido AND (ca.anulado = 'N' OR ca.anulado IS NULL)
	WHERE (
		(pend_1 IS NOT NULL OR pend_2 IS NOT NULL OR pend_3 IS NOT NULL OR pend_4 IS NOT NULL OR pend_5 IS NOT NULL OR pend_6 IS NOT NULL OR pend_7 IS NOT NULL OR pend_8 IS NOT NULL OR pend_9 IS NOT NULL OR pend_10 IS NOT NULL OR pend_11 IS NOT NULL OR pend_12 IS NOT NULL) AND
		(cantidad_pendiente > 0) AND
		(de.anulado = 'N' OR de.anulado IS NULL)
	)
	GROUP BY cod_articulo, cod_color_articulo
) 

GO
