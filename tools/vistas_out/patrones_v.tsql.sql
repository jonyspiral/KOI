CREATE VIEW dbo.patrones_v
AS
SELECT     dbo.Patrones_mp_cabecera.cod_color_articulo, dbo.articulos.denom_articulo, dbo.Patrones_mp_cabecera.cod_articulo, 
                      dbo.Patrones_mp_cabecera.version, dbo.Patrones_mp_detalle.cod_material, db