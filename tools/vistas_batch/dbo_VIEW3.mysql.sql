CREATE VIEW `VIEW3` AS
CREATE VIEW VIEW3
AS
SELECT PERCENT dbo.colores_por_articulo.catalogo_orden_pagina AS `Position`, 'Spiral' AS Manufacturer, '' AS Supplier, 
                      dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_d.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo
                       + '_e.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_i.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo
                       + '_t.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_a.jpg' AS `Product Images`, 
                      dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' AS `Product Images Caption`, 
                      dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.articulos.denom_articulo + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' AS `Product Images Caption AG`, 
                      'AR Standard rate (21%)' AS Tax, '' AS `Product Carrier`, '' AS `Product Accessories`, '' AS `Attribute Group Tamaño`, 
                      dbo.colores_por_articulo.denom_color AS `Attribute Group Color`, '' AS `Attribute Group Dimension`, 
                      dbo.stock_01_14_20_por_talle_v.Talle AS `Attribute Group Talle`, 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle AS `Combination Reference`, 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo AS `Combination Supplier Reference`, 
                      0 AS `Combination Supplier Unit Price Tax Excl`, '' AS `Combination Location`, '' AS `Combination EAN13`, '' AS `Combination UPC`, 
                      dbo.colores_por_articulo.precio_mayorista_usd / 1.21 AS `Combination Wholesale Price`, 
                      dbo.colores_por_articulo.ecommerce_price1 / 1.21 AS `Combination Price`, 0 AS `Combination EcoTax`, 
                      dbo.stock_01_14_20_por_talle_v.cant_1 AS `Combination Quantity`, 0 AS `Combination Weight`, 0 AS `Combination Unit Price Impact`, 
                      '' AS `Combination Is Default`, 1 AS `Combination Minimal Quantity`, 0 AS attr_low_stock_threshold, 0 AS attr_low_stock_alert, NOW() 
                      AS `Combination Attribute Available Date`, 
                      dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_d.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo
                       + '_e.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_i.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo
                       + '_t.jpg;' + dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + '_a.jpg' AS `Combination Images`, 
                      dbo.colores_por_articulo.ecommerce_price3 AS `Attribute Group Composition`, dbo.articulos.naturaleza, dbo.stock_01_14_20_por_talle_v.Tall
e, 
                      dbo.familias_producto.nombre AS familia, familias_producto_2.nombre AS familia_ecommere, dbo.colores_por_articulo.ecommerce_existe, 
                      dbo.colores_por_articulo.catalogo, dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.cod_color_articulo AS cod_color, 
                      dbo.colores_por_articulo.ecommerce_price3 AS Composition, 
                      'ZAPATILLAS SPIRAL' + dbo.articulos.denom_articulo + '. Tienda Oficial' AS Description_ML
FROM         dbo.familias_producto familias_producto_2 RIGHT OUTER JOIN
                      dbo.familias_producto INNER JOIN
                      dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo ON 
                      dbo.familias_producto.id = dbo.articulos.cod_familia_producto INNER JOIN
                      dbo.Marcas ON dbo.articulos.cod_marca = dbo.Marcas.cod_marca INNER JOIN
                      dbo.lineas_productos ON dbo.articulos.cod_linea = dbo.lineas_productos.cod_linea INNER JOIN
                      dbo.stock_01_14_20_por_talle_v ON dbo.colores_por_articulo.cod_articulo = dbo.stock_01_14_20_por_talle_v.cod_articulo AND 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.stock_01_14_20_por_talle_v.cod_color_articulo ON 
                      familias_producto_2.id = dbo.colores_por_articulo.ecommerce_cod_category
WHERE     (dbo.stock_01_14_20_por_talle_v.Talle <> 'X') AND (dbo.stock_01_14_20_por_talle_v.Talle IS NOT NULL) AND 
                      (dbo.stock_01_14_20_por_talle_v.Talle <> '') AND (dbo.articulos.naturaleza = 'PT') AND (dbo.colores_por_articulo.ecommerce_existe = 'S') AND 
                      (dbo.colores_por_articulo.id_tipo_producto_stock <> '06')
ORDER BY dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle, 
                      dbo.colores_por_articulo.catalogo_orden_pagina


LIMIT 100;;
