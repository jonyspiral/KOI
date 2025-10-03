CREATE VIEW [dbo].[ultimos_precios_mp] AS
CREATE VIEW dbo.ultimos_precios_mp
AS
SELECT     d.cod_material, d.cod_color,
                          (SELECT     TOP 1 ISNULL(d2.precio_unitario, 0)
                            FROM          Ordenes_compra_detalle d2
                            WHERE      d.cod_material = d2.cod_material AND d.cod_color = d2.cod_color
                            ORDER BY d2.fecha_alta DESC) AS Expr1
FROM         dbo.Ordenes_compra_detalle d INNER JOIN
                      dbo.Ordenes_compra_cabecera c ON d.cod_orden_de_compra = c.cod_orden_de_compra
WHERE     (c.es_hexagono = 'N')
GROUP BY d.cod_material, d.cod_color

GO
