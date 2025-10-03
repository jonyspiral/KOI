CREATE VIEW [dbo].[compras_pendientes] AS
CREATE VIEW dbo.compras_pendientes
AS
SELECT     TOP 100 PERCENT dbo.Ordenes_compra_detalle.cod_material, dbo.Ordenes_compra_detalle.cod_color, 
                      SUM(dbo.Ordenes_compra_detalle.cantidad_pendiente) AS pendiente
FROM         dbo.Ordenes_compra_detalle INNER JOIN
                      dbo.Ordenes_compra_cabecera ON 
                      dbo.Ordenes_compra_detalle.cod_orden_de_compra = dbo.Ordenes_compra_cabecera.cod_orden_de_compra
WHERE     (dbo.Ordenes_compra_detalle.cantidad_pendiente > 0.001) AND (dbo.Ordenes_compra_cabecera.anulado = 'N')
GROUP BY dbo.Ordenes_compra_detalle.cod_material, dbo.Ordenes_compra_detalle.cod_color, dbo.Ordenes_compra_cabecera.es_hexagono
HAVING      (dbo.Ordenes_compra_cabecera.es_hexagono = 'N')
ORDER BY dbo.Ordenes_compra_detalle.cod_material, dbo.Ordenes_compra_detalle.cod_color

GO
