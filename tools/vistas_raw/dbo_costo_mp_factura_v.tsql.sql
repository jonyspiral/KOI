CREATE VIEW [dbo].[costo_mp_factura_v] AS
CREATE VIEW dbo.costo_mp_factura_v
AS
SELECT     TOP 100 PERCENT dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.punto_venta, 
                      dbo.documentos_cantidades.tipo_docum, dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, 
                      SUM((dbo.costo_producto_total_V.costo + dbo.costo_producto_total_V.costo_linea) * dbo.documentos_cantidades.cantidad) AS costo, 
                      SUM(dbo.documentos_cantidades.cantidad * dbo.documentos_cantidades.precio_unitario_final) AS importe_articulos
FROM         dbo.documentos_cantidades INNER JOIN
                      dbo.costo_producto_total_V ON dbo.documentos_cantidades.cod_articulo = dbo.costo_producto_total_V.cod_articulo AND 
                      dbo.documentos_cantidades.cod_color_articulo = dbo.costo_producto_total_V.cod_color_articulo
GROUP BY dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.tipo_docum, 
                      dbo.documentos_cantidades.letra_documento, dbo.documentos_cantidades.nro_documento, dbo.documentos_cantidades.punto_venta
HAVING      (dbo.documentos_cantidades.fecha > dbo.relativeDate(GETDATE(), 'first', - 3))
ORDER BY dbo.documentos_cantidades.fecha

GO
