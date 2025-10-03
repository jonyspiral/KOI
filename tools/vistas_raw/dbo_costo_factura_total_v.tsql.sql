CREATE VIEW [dbo].[costo_factura_total_v] AS
CREATE VIEW dbo.costo_factura_total_v
AS
SELECT     TOP 100 PERCENT dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.punto_venta, 
                      dbo.documentos_cantidades.tipo_docum, dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, 
                      SUM(dbo.costo_producto_total_V.costo * dbo.documentos_cantidades.cantidad) AS costo, 
                      SUM(dbo.documentos_cantidades.cantidad * dbo.documentos_cantidades.precio_unitario_final) 
                      * (CASE dbo.documentos_cantidades.tipo_docum WHEN 'NCR' THEN - 1 ELSE 1 END) AS importe_articulos
FROM         dbo.documentos_cantidades INNER JOIN
                      dbo.costo_producto_total_V ON dbo.documentos_cantidades.cod_articulo = dbo.costo_producto_total_V.cod_articulo AND 
                      dbo.documentos_cantidades.cod_color_articulo = dbo.costo_producto_total_V.cod_color_articulo
GROUP BY dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.tipo_docum, 
                      dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, dbo.documentos_cantidades.punto_venta
ORDER BY dbo.documentos_cantidades.fecha

GO
