
CREATE VIEW [dbo].[stock_registros_aux_vw]
AS
	/*
		Esta consulta trae registros de cada lugar donde se debe sacar el stock y pone uno por línea
		Los asignados los trae como Almacen '01'. De donde se la llame se debe agrupar y hacer la suma.
		Se res_por_articulo.cod_articulo = prod_terminados_movim_extraor.cod_articulo) AND (colores_por_articulo.cod_color_articulo = prod_terminados_movim_extraor.cod_color_articulo)
	WHERE ((prod_terminados_movim_extraor.cod_almacen <> '') AND ((articulos.vigenteeza)='PT'))
	GROUP BY docum_clientes_detalle.cod_articulo, docum_clientes_detalle.cod_color, docum_clientes_detalle.cod_almacen
) UNION (
	SELECT '01' cod_almacen, cod_articulo, cod_color_articulo, (-1) * SUM(pend_1) c_1, (-1) * SUM(pend_2) c_2, (-1) *