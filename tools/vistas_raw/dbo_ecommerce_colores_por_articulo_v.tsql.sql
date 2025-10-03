CREATE VIEW [dbo].[ecommerce_colores_por_articulo_v] AS

CREATE VIEW ecommerce_colores_por_articulo_v AS

SELECT
	c.cod_articulo cod_articulo,
	c.cod_color_articulo cod_color_articulo,
	c.fechaUltimaMod fecha_ultima_mod,
	c.categoria_usuario categoria_usuario,

	c.ecommerce_existe ecommerce_existe,
	c.ecommerce_fecha_ultima_sinc ecommerce_fecha_ultima_sinc,
	c.ecommerce_nombre nombre,
	c.ecommerce_info info,
	c.ecommerce_forsale forsale,
	c.ecommerce_condition condition,
	c.ecommerce_cod_category cod_category,
	c.ecommerce_exclusive exclusive,
	c.ecommerce_featured featured,
	c.ecommerce_price1 price1,
	c.ecommerce_price2 price2,
	c.ecommerce_price3 price3,
	c.ecommerce_image1 image1,

	r.posic_1 size_id_1, 0 min_stock_1, 0 replacement_stock_1, 0 max_stock_1, s.cant_1 current_stock_1,
	r.posic_2 size_id_2, 0 min_stock_2, 0 replacement_stock_2, 0 max_stock_2, s.cant_2 current_stock_2,
	r.posic_3 size_id_3, 0 min_stock_3, 0 replacement_stock_3, 0 max_stock_3, s.cant_3 current_stock_3,
	r.posic_4 size_id_4, 0 min_stock_4, 0 replacement_stock_4, 0 max_stock_4, s.cant_4 current_stock_4,
	r.posic_5 size_id_5, 0 min_stock_5, 0 replacement_stock_5, 0 max_stock_5, s.cant_5 current_stock_5,
	r.posic_6 size_id_6, 0 min_stock_6, 0 replacement_stock_6, 0 max_stock_6, s.cant_6 current_stock_6,
	r.posic_7 size_id_7, 0 min_stock_7, 0 replacement_stock_7, 0 max_stock_7, s.cant_7 current_stock_7,
	r.posic_8 size_id_8, 0 min_stock_8, 0 replacement_stock_8, 0 max_stock_8, s.cant_8 current_stock_8,
	r.posic_9 size_id_9, 0 min_stock_9, 0 replacement_stock_9, 0 max_stock_9, s.cant_9 current_stock_9,
	r.posic_10 size_id_10, 0 min_stock_10, 0 replacement_stock_10, 0 max_stock_10, s.cant_10 current_stock_10
	
FROM colores_por_articulo c
INNER JOIN articulos a ON a.cod_articulo = c.cod_articulo
INNER JOIN rango_talles r ON a.cod_rango = r.cod_rango
LEFT JOIN stock s ON s.cod_almacen = 14 AND c.cod_articulo = s.cod_articulo AND c.cod_color_articulo = s.cod_color_articulo
WHERE c.vigente = 'S'
GO
