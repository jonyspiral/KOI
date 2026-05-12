SELECT     TOP 100 PERCENT (CASE ecommerce_forsale WHEN 'S' THEN (1) ELSE 0 END) AS [On Sale], 0 AS [Online Only], '' AS EAN13, '' AS UPC, 0 AS [Eco-Tax],
                       (CASE [stock_01_14_20_por_talle_v].[cantidad] WHEN NULL THEN 0 ELSE [stock_01_14_20_por_talle_v].[cantidad] END) AS Quantity, 
                      1 AS [Minimal Quantity], 0 AS Price, colores_por_articulo.precio_mayorista_usd AS [Wholesale Price], '' AS Unity, 0 AS [Unit Price Ratio], 
                      0 AS [Additional Shipping Cost], articulos.cod_articulo AS Reference, articulos.cod_articulo AS [Supplier Reference], 0 AS [Supplier Unit Price (Tax Excl)],
                       '' AS Location, 0 AS Width, 0 AS Height, 0 AS Depth, 0 AS Weight, 2 AS [Out of Stock], 0 AS [Delivery time], 0 AS [Quantity Discount], 0 AS Customizable, 
                      1 AS Status, '301-[category]' AS [Redirect Type], 0 AS id_type_redirected, 1 AS [Available for Order], '' AS [Date available], 0 AS show_condition, 
                      'new' AS Condition, 1 AS [Show Price], 1 AS Indexed, 'both' AS Visibility, 0 AS [Is Pack Product], 0 AS [Has attachments], 0 AS [Is Virtual Product], 
                      '' AS [Cache default Attribute], getdate() AS [Date Add], getdate() AS [Date Update], 0 AS [Advanced Stock Management], 3 AS pack_stock_type, 
                      1 AS State, articulos.denom_articulo_largo AS Description, '' AS [Short Description], '' AS [Friendly URL], articulos.denom_articulo AS [Product Name], 
                      '' AS [Available Now], '' AS [Available Later], '' AS [Delivery time of instock products], '' AS [Delivery time of out-of-stock products with allowed orders], 
                      colores_por_articulo.ecommerce_info AS [Description AG], '' AS [Short Description AG], '' AS Tags, '' AS [Friendly URL AG], '' AS [Meta Description AG], 
                      '' AS [Meta Keywords AG], '' AS [Meta Title AG], articulos.denom_articulo AS [Product Name AG], '' AS [Available Now AG], '' AS [Available Later AG], 
                      '' AS [Delivery time of in-stock products AG], '' AS [Delivery time of out-of-stock products with allowed orders AG], '' AS [Tags AG], '' AS [Product URL], 
                      CASE [familias_producto_2].[nombre] WHEN 'SALE' THEN ('Raíz|Inicio|SALE') 
                      ELSE ('Raíz|Inicio|' + (CASE [titulo_catalogo] WHEN 'SMALL' THEN 'MEN' ELSE [titulo_catalogo] END)) 
                      + '|' + [familias_producto_2].[nombre] END AS Category, colores_por_articulo.catalogo_orden_pagina AS [Position], 'Spiral' AS Manufacturer, 
                      '' AS Supplier, 
                      [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo] + '_e.jpg;' + [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo]
                       + '_d.jpg;' + [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo] + '_i.jpg;' + [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo]
                       + '_t.jpg;' + [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo] + '_a.jpg' AS [Product Images], 
                      [denom_articulo] + ' ' + [colores_por_articulo].[cod_color_articulo] + ';' + [denom_articulo] + ' ' + [colores_por_articulo].[cod_color_articulo] + ';' + [denom_articulo]
                       + ' ' + [colores_por_articulo].[cod_color_articulo] + ';' + [denom_articulo] + ' ' + [colores_por_articulo].[cod_color_articulo] + ';' + [denom_articulo] + ' ' + [colores_por_articulo].[cod_color_articulo]
                       + ';' AS [Product Images Caption], 
                      [denom_articulo] + ' ' + [colores_por_articulo].[cod_color_articulo] + ';' + [denom_articulo] + ' ' + [colores_por_articulo].[cod_color_articulo] + ';' + [denom_articulo]
                       + ' ' + [colores_por_articulo].[cod_color_articulo] + ';' + [denom_articulo] + ' ' + [colores_por_articulo].[cod_color_articulo] + ';' + [denom_articulo] + ' ' + [colores_por_articulo].[cod_color_articulo]
                       + ';' AS [Product Images Caption AG], 'AR Standard rate (21%)' AS Tax, '' AS [Product Carrier], '' AS [Product Accessories], '' AS [Attribute Group Tamaño],
                       colores_por_articulo.denom_color AS [Attribute Group Color], '' AS [Attribute Group Dimension], 
                      stock_01_14_20_por_talle_v.Talle AS [Attribute Group Talle], 
                      [articulos].[cod_articulo] + [colores_por_articulo].[cod_color_articulo] + [talle] AS [Combination Reference], 
                      [articulos].[cod_articulo] + [colores_por_articulo].[cod_color_articulo] AS [Combination Supplier Reference], 
                      0 AS [Combination Supplier Unit Price Tax Excl], '' AS [Combination Location], '' AS [Combination EAN13], '' AS [Combination UPC], 
                      [precio_mayorista_usd] / 1.21 AS [Combination Wholesale Price], [ecommerce_price1] / 1.21 AS [Combination Price], 0 AS [Combination EcoTax], 
                      stock_01_14_20_por_talle_v.cant_1 AS [Combination Quantity], 0 AS [Combination Weight], 0 AS [Combination Unit Price Impact], 
                      '' AS [Combination Is Default], 1 AS [Combination Minimal Quantity], 0 AS attr_low_stock_threshold, 0 AS attr_low_stock_alert, getdate() 
                      AS [Combination Attribute Available Date], 
                      [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo] + '_e.jpg;' + [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo]
                       + '_d.jpg;' + [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo] + '_i.jpg;' + [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo]
                       + '_t.jpg;' + [colores_por_articulo].[cod_articulo] + [colores_por_articulo].[cod_color_articulo] + '_a.jpg' AS [Combination Images], 
                      colores_por_articulo.ecommerce_price3 AS [Attribute Group Composition], articulos.naturaleza, stock_01_14_20_por_talle_v.talle, 
                      familias_producto.nombre AS familia, familias_producto_2.nombre AS familia_ecommere, colores_por_articulo.ecommerce_existe, 
                      colores_por_articulo.catalogo, colores_por_articulo.id_tipo_producto_stock, colores_por_articulo.cod_color_articulo AS cod_color, 
                      colores_por_articulo.ecommerce_price3 AS Composition, 'ZAPATILLAS SPIRAL' + articulos.denom_articulo + '. Tienda Oficial' AS Description_ML
FROM         familias_producto AS familias_producto_2 RIGHT JOIN
                      ((((familias_producto INNER JOIN
                      (articulos INNER JOIN
                      colores_por_articulo ON articulos.cod_articulo = colores_por_articulo.cod_articulo) ON familias_producto.id = articulos.cod_familia_producto) 
                      INNER JOIN
                      Marcas ON articulos.cod_marca = Marcas.cod_marca) INNER JOIN
                      lineas_productos ON articulos.cod_linea = lineas_productos.cod_linea) INNER JOIN
                      stock_01_14_20_por_talle_v ON (colores_por_articulo.cod_articulo = stock_01_14_20_por_talle_v.cod_articulo) AND 
                      (colores_por_articulo.cod_color_articulo = stock_01_14_20_por_talle_v.cod_color_articulo)) ON 
                      familias_producto_2.id = colores_por_articulo.ecommerce_cod_category
WHERE     (((stock_01_14_20_por_talle_v.Talle) <> ('X') AND (stock_01_14_20_por_talle_v.Talle) IS NOT NULL AND (stock_01_14_20_por_talle_v.Talle) <> '') AND 
                      ((articulos.naturaleza) = 'PT') AND ((colores_por_articulo.ecommerce_existe) = 'S') AND ((colores_por_articulo.id_tipo_producto_stock) <> '06'))
ORDER BY dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle, 
                      dbo.colores_por_articulo.catalogo_orden_pagina