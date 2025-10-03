CREATE VIEW [dbo].[prestashop_articulos_update_v] AS
CREATE VIEW dbo.prestashop_articulos_update_v
AS
SELECT     TOP 100 PERCENT (CASE ecommerce_forsale WHEN 'S' THEN (1) ELSE 0 END) AS [On Sale], 0 AS [Online Only], '' AS EAN13, '' AS UPC, 0 AS [Eco-Tax],
                       (CASE [stock_01_14_20_por_talle_v].[cantidad] WHEN NULL THEN 0 ELSE [stock_01_14_20_por_talle_v].[cantidad] END) AS Quantity, 
                      1 AS [Minimal Quantity], 0 AS Price, dbo.colores_por_articulo.precio_mayorista_usd AS [Wholesale Price], '' AS Unity, 0 AS [Unit Price Ratio], 
                      0 AS [Additional Shipping Cost], dbo.colores_por_articulo.ecommerce_reference AS Reference, dbo.articulos.cod_articulo AS [Supplier Reference], 
                      0 AS [Supplier Unit Price (Tax Excl)], '' AS Location, 0 AS Width, 0 AS Height, 0 AS Depth, 0 AS Weight, 2 AS [Out of Stock], 0 AS [Delivery time], 
                      0 AS [Quantity Discount], 0 AS Customizable, 1 AS Status, '301-[category]' AS [Redirect Type], 0 AS id_type_redirected, 1 AS [Available for Order], 
                      '' AS [Date available], 0 AS show_condition, 'new' AS Condition, 1 AS [Show Price], 1 AS Indexed, 'both' AS Visibility, 0 AS [Is Pack Product], 
                      0 AS [Has attachments], 0 AS [Is Virtual Product], '' AS [Cache default Attribute], GETDATE() AS [Date Add], GETDATE() AS [Date Update], 
                      0 AS [Advanced Stock Management], 3 AS pack_stock_type, 1 AS State, dbo.colores_por_articulo.ecommerce_description AS Description, 
                      '' AS [Short Description], '' AS [Friendly URL], dbo.colores_por_articulo.ecommerce_name AS [Product Name], '' AS [Available Now], 
                      '' AS [Available Later], '' AS [Delivery time of instock products], '' AS [Delivery time of out-of-stock products with allowed orders], '' AS [Description AG], 
                      '' AS [Short Description AG], '' AS Tags, '' AS [Friendly URL AG], '' AS [Meta Description AG], '' AS [Meta Keywords AG], '' AS [Meta Title AG], 
                      dbo.colores_por_articulo.ecommerce_name AS [Product Name AG], '' AS [Available Now AG], '' AS [Available Later AG], 
                      '' AS [Delivery time of in-stock products AG], '' AS [Delivery time of out-of-stock products with allowed orders AG], '' AS [Tags AG], '' AS [Product URL], 
                      'Raíz|Inicio|' + dbo.colores_por_articulo.ecommerce_cod_category AS Category, dbo.colores_por_articulo.catalogo_orden_pagina AS [Position], 
                      'SPIRAL SHOES' AS Manufacturer, 'SPIRAL SHOES' AS Supplier, ISNULL(piu.e, '') + ';' + ISNULL(piu. d, '') + ';' + ISNULL(piu.a, '') + ';' + ISNULL(piu. t, '') 
                      + ';' + ISNULL(piu.b, '') + ';' + ISNULL(piu.u, '') + ';' + ISNULL(piu.f, '') + ';' + ISNULL(piu.i1, '') AS [Product Images], 
                      dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name
                       + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' AS [Product Images Caption], 
                      dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo
                       + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name
                       + ' ' + dbo.colores_por_articulo.cod_color_articulo + ';' + dbo.colores_por_articulo.ecommerce_name + ' ' + dbo.colores_po
r_articulo.cod_color_articulo
                       + ';' AS [Product Images Caption AG], 'AR Standard rate (21%)' AS Tax, '' AS [Product Carrier], '' AS [Product Accessories], '' AS [Attribute Group Tamaño],
                       dbo.colores_por_articulo.denom_color AS [Attribute Group Color], '' AS [Attribute Group Dimension], 
                      dbo.stock_01_14_20_por_talle_v.Talle AS [Attribute Group Talle], 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle AS [Combination Reference], 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo AS [Combination Supplier Reference], 
                      0 AS [Combination Supplier Unit Price Tax Excl], '' AS [Combination Location], '' AS [Combination EAN13], '' AS [Combination UPC], 
                      dbo.colores_por_articulo.precio_mayorista_usd / 1.21 AS [Combination Wholesale Price], 
                      dbo.colores_por_articulo.ecommerce_price1 / 1.21 AS [Combination Price], 0 AS [Combination EcoTax], 
                      dbo.stock_01_14_20_por_talle_v.cant_1 AS [Combination Quantity], 0 AS [Combination Weight], 0 AS [Combination Unit Price Impact], 
                      '' AS [Combination Is Default], 1 AS [Combination Minimal Quantity], 0 AS attr_low_stock_threshold, 0 AS attr_low_stock_alert, GETDATE() 
                      AS [Combination Attribute Available Date], ISNULL(piu.e, '') + ';' + ISNULL(piu. d, '') + ';' + ISNULL(piu.a, '') + ';' + ISNULL(piu. t, '') + ';' + ISNULL(piu.b, '') 
                      + ';' + ISNULL(piu.u, '') + ';' + ISNULL(piu.f, '') + ';' + ISNULL(piu.i1, '') AS [Combination Images], 
                      dbo.colores_por_articulo.composition AS [Attribute Group Composition], dbo.articulos.naturaleza, dbo.stock_01_14_20_por_talle_v.Talle, 
                      dbo.colores_por_articulo.ecommerce_cod_category AS familia_ecommerce, dbo.colores_por_articulo.ecommerce_existe, 
                      dbo.colores_por_articulo.ecomm_especific_price_identifier AS reduction_identifier, 
                      dbo.colores_por_articulo.ecomm_especific_price_reduction AS reduction, dbo.colores_por_articulo.ecomm_especific_price_from AS reduction_from, 
                      dbo.colores_por_articulo.ecomm_especific_price_to AS reduction_to, dbo.colores_por_articulo.catalogo, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.cod_color_articulo AS cod_color, 
                      dbo.colores_por_articulo.ecommerce_price3 AS Composition, 'ZAPATILLAS SPIRAL' + dbo.articulos.denom_articulo AS Description_ML, 
                      dbo.articulos.ml_denominacion
FROM         dbo.prestashop_imagenes_update piu INNER JOIN
                      dbo.familias_producto INNER JOIN
                      dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo ON 
                      dbo.familias_producto.id = dbo.articulos.cod_familia_producto INNER JOIN
                      dbo.Marcas ON dbo.articulos.cod_marca = dbo.Marcas.cod_marca INNER JOIN
                      dbo.lineas_productos ON dbo.articulos.cod_linea = dbo.lineas_productos.cod_linea INNER JOIN
                      dbo.stock_01_14_20_por_talle_v ON dbo.colores_por_articulo.cod_articulo = dbo.stock_01_14_20_por_talle_v.cod_articulo AND 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.stock_01_14_20_por_talle_v.cod_color_articulo ON 
                      piu.cod_articulo = dbo.colores_por_articulo.cod_articulo AND piu.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo
WHERE     (dbo.stock_01_14_20_por_talle_v.Talle <> 'X') AND (dbo.stock_01_14_20_por_talle_v.Talle IS NOT NULL) AND 
                      (dbo.stock_01_14_20_por_talle_v.Talle <> '') AND (dbo.articulos.natu
raleza = 'PT') AND (dbo.colores_por_articulo.ecommerce_existe = 'S') AND 
                      (dbo.colores_por_articulo.id_tipo_producto_stock <> '06') AND (dbo.stock_01_14_20_por_talle_v.cant_1 > 0)
ORDER BY dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle, 
                      dbo.colores_por_articulo.catalogo_orden_pagina

GO
(716 rows affected)
1> 
