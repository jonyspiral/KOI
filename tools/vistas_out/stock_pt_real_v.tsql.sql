CREATE VIEW dbo.stock_pt_real_v AS select	alm.cod_almacen + ' - ' + alm.denom_almacen as "Almacen",
		art.cod_articulo + ' - ' + art.denom_articulo as "Articulo",
		cxa.cod_color_articulo + ' - ' + cxa.denom_color as "Color",
		ccu.denom_categoria as "