CREATE VIEW dbo.compras_pendientes
AS
SELECT     TOP 100 PERCENT dbo.Ordenes_compra_detalle.cod_material, dbo.Ordenes_compra_detalle.cod_color, 
                      SUM(dbo.Ordenes_compra_detalle.cantidad_pendiente) AS pendiente
FROM         dbo.Ord