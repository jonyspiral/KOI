CREATE VIEW dbo.ecomexperts_articulos_update_v
AS
SELECT     TOP 100 PERCENT dbo.colores_por_articulo.ml_reference AS sku, dbo.colores_por_articulo.ml_name AS titulo, '' AS garantia, 21 AS impuesto, 
                      'default' AS precio_titulo1, dod_articulo = dbo.colores_por_articulo.cod_articulo AND piu.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo
WHERE     (dbo.stock_01_14_20_por_talle_v.Talle <> 'X') AND (dbo.stock_01_14_20_por_talle_v.Talle IS NOT NULL) AND 
            