CREATE VIEW [dbo].[reporte_articulos_v] AS
SELECT     dbo.documentos_c.cod_cliente, dbo.Clientes.razon_social, dbo.articulos.denom_articulo, dbo.documentos_cantidades.cod_articulo, 
                      dbo.documentos_cantidades.cod_color_articulo, dbo