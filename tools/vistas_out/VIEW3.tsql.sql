CREATE VIEW dbo.VIEW3
AS
SELECT     TOP 100 PERCENT dbo.colores_por_articulo.catalogo_orden_pagina AS [Position], 'Spiral' AS Manufacturer, '' AS Supplier, 
                      dbo.colores_por_articulo.cod_articulo + dbo.colores_por_articulo.cod_coloe, 
                      dbo.familias_producto.nombre AS familia, familias_producto_2.nombre AS familia_ecommere, dbo.colores_por_articulo.ecommerce_existe, 
                      dbo.colores_por_articulo.catalogo, dbo.colores_por_articulo.id_tipo_prod