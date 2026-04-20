CREATE VIEW dbo.patrones_se_vigentes_v
AS
SELECT     TOP 100 PERCENT dbo.Patrones_mp_detalle.cod_seccion, dbo.Patrones_mp_cabecera.cod_articulo, dbo.articulos.denom_articulo, 
                      dbo.Patrones_mp_detalle.cod_color_articulo, dbo.Patron