CREATE VIEW dbo.costo_mp_semielaborado_detalle_v
AS
SELECT     dbo.patrones_se_vigentes_v.cod_articulo, dbo.patrones_se_vigentes_v.denom_articulo, dbo.patrones_se_vigentes_v.cod_color_articulo, 
                      dbo.patrones_se_vigentes_v.tipo_pat