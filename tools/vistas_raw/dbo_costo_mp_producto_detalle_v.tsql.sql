CREATE VIEW [dbo].[costo_mp_producto_detalle_v] AS
CREATE VIEW dbo.costo_mp_producto_detalle_v
AS
SELECT     dbo.patrones_vigentes_v.cod_articulo, dbo.patrones_vigentes_v.denom_articulo, dbo.patrones_vigentes_v.cod_color_articulo, 
                      dbo.patrones_vigentes_v.tipo_patron, dbo.patrones_vigentes_v.cod_material, dbo.Materias_primas.precio_unitario AS precio, 
                      dbo.materiales.denom_material AS material, dbo.patrones_vigentes_v.cod_color_material, dbo.patrones_vigentes_v.consumo_par, 
                      dbo.conjuntos.denom_conjunto, dbo.conjuntos.conjunto, dbo.patrones_vigentes_v.factor_conversion, 
                      dbo.Materias_primas.precio_unitario * dbo.patrones_vigentes_v.consumo_par / dbo.patrones_vigentes_v.factor_conversion AS Costo, 
                      dbo.patrones_vigentes_v.cod_seccion, dbo.subrubros_materias_primas.denom_subrubro, dbo.rubros_materias_primas.denom_rubro, 
                      dbo.articulos.cod_linea
FROM         dbo.rubros_materias_primas INNER JOIN
                      dbo.materiales ON dbo.rubros_materias_primas.cod_rubro = dbo.materiales.cod_rubro INNER JOIN
                      dbo.subrubros_materias_primas ON dbo.materiales.cod_rubro = dbo.subrubros_materias_primas.cod_rubro AND 
                      dbo.materiales.cod_subrubro = dbo.subrubros_materias_primas.cod_subrubro INNER JOIN
                      dbo.Materias_primas ON dbo.materiales.cod_material = dbo.Materias_primas.cod_material INNER JOIN
                      dbo.patrones_vigentes_v INNER JOIN
                      dbo.conjuntos ON dbo.patrones_vigentes_v.conjunto = dbo.conjuntos.conjunto ON 
                      dbo.Materias_primas.cod_material = dbo.patrones_vigentes_v.cod_material AND 
                      dbo.Materias_primas.cod_color = dbo.patrones_vigentes_v.cod_color_material INNER JOIN
                      dbo.articulos ON dbo.patrones_vigentes_v.cod_articulo = dbo.articulos.cod_articulo
GROUP BY dbo.conjuntos.denom_conjunto, dbo.patrones_vigentes_v.cod_color_articulo, dbo.patrones_vigentes_v.tipo_patron, 
                      dbo.patrones_vigentes_v.denom_articulo, dbo.patrones_vigentes_v.consumo_par, dbo.conjuntos.conjunto, dbo.materiales.denom_material, 
                      dbo.patrones_vigentes_v.cod_color_material, dbo.patrones_vigentes_v.cod_articulo, dbo.patrones_vigentes_v.cod_material, dbo.conjuntos.conjunto, 
                      dbo.patrones_vigentes_v.factor_conversion, 
                      dbo.Materias_primas.precio_unitario * dbo.patrones_vigentes_v.consumo_par / dbo.patrones_vigentes_v.factor_conversion, 
                      dbo.patrones_vigentes_v.cod_seccion, dbo.materiales.cod_rubro, dbo.materiales.cod_subrubro, dbo.subrubros_materias_primas.denom_subrubro, 
                      dbo.rubros_materias_primas.denom_rubro, dbo.Materias_primas.precio_unitario, dbo.articulos.cod_linea

GO
