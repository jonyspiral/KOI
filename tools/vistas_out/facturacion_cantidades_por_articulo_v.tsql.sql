CREATE VIEW dbo.facturacion_cantidades_por_articulo_v
AS
SELECT     dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.cod_almacen, 
                      dbo.documentos_cantidades.cod_articulo, dbo.articulos.