CREATE VIEW [dbo].[facturacion_cantidades_por_articulo_v] AS
CREATE VIEW dbo.facturacion_cantidades_por_articulo_v
AS
SELECT     dbo.documentos_cantidades.fecha, dbo.documentos_cantidades.empresa, dbo.documentos_cantidades.cod_almacen, 
                      dbo.documentos_cantidades.cod_articulo, dbo.articulos.denom_articulo, dbo.documentos_cantidades.cod_color_articulo, 
                      dbo.documentos_cantidades.cantidad, (CASE tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) * ISNULL(precio_unitario_final, 0) precio_unitario_final, 
                      (CASE tipo_docum WHEN 'NCR' THEN (- 1) ELSE 1 END) * cantidad * precio_unitario_final total, dbo.articulos.cod_linea, 
                      dbo.lineas_productos.denom_linea, dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.catalogo
FROM         dbo.documentos_cantidades INNER JOIN
                      dbo.colores_por_articulo ON dbo.documentos_cantidades.cod_articulo = dbo.colores_por_articulo.cod_articulo AND 
                      dbo.documentos_cantidades.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo INNER JOIN
                      dbo.articulos ON dbo.colores_por_articulo.cod_articulo = dbo.articulos.cod_articulo INNER JOIN
                      dbo.lineas_productos ON dbo.articulos.cod_linea = dbo.lineas_productos.cod_linea

GO
