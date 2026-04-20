CREATE VIEW [dbo].[patrones_v] AS
CREATE VIEW dbo.patrones_v
AS
SELECT     dbo.Patrones_mp_cabecera.cod_color_articulo, dbo.articulos.denom_articulo, dbo.Patrones_mp_cabecera.cod_articulo, 
                      dbo.Patrones_mp_cabecera.version, dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material, 
                      dbo.Patrones_mp_detalle.cod_seccion, dbo.Patrones_mp_detalle.consumo_par, dbo.Patrones_mp_detalle.conjunto, dbo.articulos.naturaleza, 
                      dbo.materiales.denom_material
FROM         dbo.Patrones_mp_detalle INNER JOIN
                      dbo.Patrones_mp_cabecera ON dbo.Patrones_mp_detalle.cod_articulo = dbo.Patrones_mp_cabecera.cod_articulo AND 
                      dbo.Patrones_mp_detalle.cod_color_articulo = dbo.Patrones_mp_cabecera.cod_color_articulo AND 
                      dbo.Patrones_mp_detalle.version = dbo.Patrones_mp_cabecera.version INNER JOIN
                      dbo.Materias_primas ON dbo.Patrones_mp_detalle.cod_material = dbo.Materias_primas.cod_material AND 
                      dbo.Patrones_mp_detalle.cod_color_material = dbo.Materias_primas.cod_color INNER JOIN
                      dbo.materiales ON dbo.Materias_primas.cod_material = dbo.materiales.cod_material INNER JOIN
                      dbo.colores_por_articulo ON dbo.Patrones_mp_detalle.cod_articulo = dbo.colores_por_articulo.cod_articulo AND 
                      dbo.Patrones_mp_detalle.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo INNER JOIN
                      dbo.articulos ON dbo.colores_por_articulo.cod_articulo = dbo.articulos.cod_articulo

GO
