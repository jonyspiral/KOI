CREATE VIEW [dbo].[reporte_articulos_v] AS
CREATE VIEW [dbo].[reporte_articulos_v] AS
SELECT     dbo.documentos_c.cod_cliente, dbo.Clientes.razon_social, dbo.articulos.denom_articulo, dbo.documentos_cantidades.cod_articulo, 
                      dbo.documentos_cantidades.cod_color_articulo, dbo.documentos_cantidades.cantidad AS pares, dbo.documentos_cantidades.precio_unitario_final, 
                      dbo.documentos_c.fecha_documento AS fecha, dbo.documentos_c.empresa
FROM         dbo.documentos_cantidades LEFT OUTER JOIN
                      dbo.articulos ON dbo.documentos_cantidades.cod_articulo = dbo.articulos.cod_articulo LEFT OUTER JOIN
                      dbo.documentos_c ON dbo.documentos_c.empresa = dbo.documentos_cantidades.empresa AND 
                      dbo.documentos_c.punto_venta = dbo.documentos_cantidades.punto_venta AND dbo.documentos_c.tipo_docum = dbo.documentos_cantidades.tipo_docum AND 
                      dbo.documentos_c.nro_documento = dbo.documentos_cantidades.nro_documento AND 
                      dbo.documentos_c.letra = dbo.documentos_cantidades.letra_documento LEFT OUTER JOIN
                      dbo.Clientes ON dbo.documentos_c.cod_cliente = dbo.Clientes.cod_cliente
WHERE		dbo.documentos_c.anulado = 'N'
GO
