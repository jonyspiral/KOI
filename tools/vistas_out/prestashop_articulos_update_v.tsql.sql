CREATE VIEW dbo.prestashop_articulos_update_v
AS
SELECT     TOP 100 PERCENT (CASE ecommerce_forsale WHEN 'S' THEN (1) ELSE 0 END) AS [On Sale], 0 AS [Online Only], '' AS EAN13, '' AS UPC, 0 AS [Eco-Tax],
                       (CASE [stock_01_14_20_porr_articulo.cod_color_articulo
                       + ';' AS [Product Images Caption AG], 'AR Standard rate (21%)' AS Tax, '' AS [Product Carrier], '' AS [Product Accessories], '' AS [Attribute Group Tamaño],
                       dbo.colores_por_artiraleza = 'PT') AND (dbo.colores_por_articulo.ecommerce_existe = 'S') AND 
                      (dbo.colores_por_articulo.id_tipo_producto_stock <> '06') AND (dbo.stock_01_14_20_por_talle_v.cant_1 > 0)
ORDER BY dbo.articulos.cod_articulo + dbo.colores_p