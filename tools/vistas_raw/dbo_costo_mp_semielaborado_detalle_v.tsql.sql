CREATE VIEW [dbo].[costo_mp_semielaborado_detalle_v] AS
CREATE VIEW dbo.costo_mp_semielaborado_detalle_v
AS
SELECT     dbo.patrones_se_vigentes_v.cod_articulo, dbo.patrones_se_vigentes_v.denom_articulo, dbo.patrones_se_vigentes_v.cod_color_articulo, 
                      dbo.patrones_se_vigentes_v.tipo_patron, dbo.patrones_se_vigentes_v.cod_material, dbo.Materias_primas.precio_unitario AS precio, 
                      dbo.materiales.denom_material AS material, dbo.patrones_se_vigentes_v.cod_color_material, dbo.patrones_se_vigentes_v.consumo_par, 
                      dbo.conjuntos.denom_conjunto, dbo.conjuntos.conjunto, dbo.patrones_se_vigentes_v.factor_conversion, dbo.patrones_se_vigentes_v.cod_seccion, 
                      dbo.subrubros_materias_primas.denom_subrubro, dbo.rubros_materias_primas.denom_rubro, dbo.articulos.cod_linea, dbo.materiales.cod_subrubro, 
                      dbo.Materias_primas.precio_unitario * dbo.patrones_se_vigentes_v.consumo_par / dbo.patrones_se_vigentes_v.factor_conversion AS costo
FROM         dbo.patrones_se_vigentes_v INNER JOIN
                      dbo.conjuntos ON dbo.patrones_se_vigentes_v.conjunto = dbo.conjuntos.conjunto INNER JOIN
                      dbo.articulos ON dbo.patrones_se_vigentes_v.cod_articulo = dbo.articulos.cod_articulo INNER JOIN
                      dbo.rubros_materias_primas INNER JOIN
                      dbo.materiales ON dbo.rubros_materias_primas.cod_rubro = dbo.materiales.cod_rubro INNER JOIN
                      dbo.subrubros_materias_primas ON dbo.materiales.cod_rubro = dbo.subrubros_materias_primas.cod_rubro AND 
                      dbo.materiales.cod_subrubro = dbo.subrubros_materias_primas.cod_subrubro INNER JOIN
                      dbo.Materias_primas ON dbo.materiales.cod_material = dbo.Materias_primas.cod_material ON 
                      dbo.patrones_se_vigentes_v.cod_material = dbo.Materias_primas.cod_material AND 
                      dbo.patrones_se_vigentes_v.cod_color_material = dbo.Materias_primas.cod_color
GROUP BY dbo.conjuntos.denom_conjunto, dbo.patrones_se_vigentes_v.consumo_par, dbo.conjuntos.conjunto, dbo.materiales.denom_material, 
                      dbo.patrones_se_vigentes_v.cod_color_material, dbo.conjuntos.conjunto, dbo.materiales.cod_rubro, dbo.materiales.cod_subrubro, 
                      dbo.subrubros_materias_primas.denom_subrubro, dbo.rubros_materias_primas.denom_rubro, dbo.Materias_primas.precio_unitario, 
                      dbo.articulos.cod_linea, dbo.patrones_se_vigentes_v.cod_seccion, dbo.patrones_se_vigentes_v.cod_articulo, 
                      dbo.patrones_se_vigentes_v.denom_articulo, dbo.patrones_se_vigentes_v.cod_color_articulo, dbo.patrones_se_vigentes_v.cod_material, 
                      dbo.patrones_se_vigentes_v.tipo_patron, dbo.patrones_se_vigentes_v.factor_conversion, 
                      dbo.Materias_primas.precio_unitario * dbo.patrones_se_vigentes_v.consumo_par / dbo.patrones_se_vigentes_v.factor_conversion

GO
