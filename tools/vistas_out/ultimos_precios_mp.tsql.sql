CREATE VIEW dbo.ultimos_precios_mp
AS
SELECT     d.cod_material, d.cod_color,
                          (SELECT     TOP 1 ISNULL(d2.precio_unitario, 0)
                            FROM          Ordenes_compra_detalle d2
                            WH