CREATE VIEW [dbo].[stock_pt_real_v] AS
CREATE VIEW dbo.stock_pt_real_v AS select	alm.cod_almacen + ' - ' + alm.denom_almacen as "Almacen",
		art.cod_articulo + ' - ' + art.denom_articulo as "Articulo",
		cxa.cod_color_articulo + ' - ' + cxa.denom_color as "Color",
		ccu.denom_categoria as "Categoria",
		tps.denom_tipo_producto as "Tipo",
		stk.cantidad as "Total",
		stk.cant_1 as "Cantidad 1",
		stk.cant_2 as "Cantidad 2",
		stk.cant_3 as "Cantidad 3",
		stk.cant_4 as "Cantidad 4",
		stk.cant_5 as "Cantidad 5",
		stk.cant_6 as "Cantidad 6",
		stk.cant_7 as "Cantidad 7",
		stk.cant_8 as "Cantidad 8",
		stk.cant_9 as "Cantidad 9",
		stk.cant_10 as "Cantidad 10"
from	almacenes alm,
		articulos art,
		colores_por_articulo cxa,
		categorias_calzado_usuarios ccu,
		stock stk,
		tipo_producto_stock tps
where	art.cod_articulo = cxa.cod_articulo
and		cxa.categoria_usuario = ccu.cod_categoria
and		alm.cod_almacen = stk.cod_almacen
and		art.cod_articulo = stk.cod_articulo
and		cxa.cod_color_articulo = stk.cod_color_articulo
and		cxa.id_tipo_producto_stock = tps.id_tipo_producto_stock
GO
