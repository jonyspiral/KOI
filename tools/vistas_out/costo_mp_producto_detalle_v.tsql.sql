CREATE VIEW dbo.costo_mp_producto_detalle_v
AS
SELECT     dbo.patrones_vigentes_v.cod_articulo, dbo.patrones_vigentes_v.denom_articulo, dbo.patrones_vigentes_v.cod_color_articulo, 
                      dbo.patrones_vigentes_v.tipo_patron, dbo.patrones