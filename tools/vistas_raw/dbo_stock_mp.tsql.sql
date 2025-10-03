CREATE VIEW [dbo].[stock_mp] AS
CREATE VIEW dbo.stock_mp
AS
SELECT     TOP 100 PERCENT dbo.Materias_primas.cod_material, dbo.materiales.denom_material, dbo.Materias_primas.cod_color, a.cod_almacen, SUM(a.cant) 
                      AS cant, SUM(a.c1) AS c1, SUM(a.c2) AS c2, SUM(a.c3) AS c3, SUM(a.c4) AS c4, SUM(a.c5) AS c5, SUM(a.c6) AS c6, SUM(a.c7) AS c7, SUM(a.c8) AS c8, 
                      SUM(a.c9) AS c9, SUM(a.c10) AS c10, dbo.rubros_materias_primas.denom_rubro, dbo.rango_talles.posic_1, 
                      dbo.Materias_primas.precio_unitario / dbo.materiales.factor_conversion AS precio_unitario, SUM(ISNULL(a.cant, 0) 
                      * ISNULL(dbo.Materias_primas.precio_unitario, 0) / dbo.materiales.factor_conversion) AS valor, dbo.materiales.unidad_medida AS UMS, 
                      dbo.materiales.factor_conversion
FROM         dbo.Materias_primas INNER JOIN
                      dbo.materiales ON dbo.Materias_primas.cod_material = dbo.materiales.cod_material LEFT OUTER JOIN
                          (SELECT     cod_material, cod_color, cant, c1, c2, c3, c4, c5, c6, c7, c8, c9, c10, cod_almacen
                            FROM          (SELECT     cod_material, cod_color, ISNULL(cantidad, 0) AS cant, ISNULL(c1, 0) AS c1, ISNULL(c2, 0) AS c2, ISNULL(c3, 0) AS c3, 
                                                                           ISNULL(c4, 0) AS c4, ISNULL(c5, 0) AS c5, ISNULL(c6, 0) AS c6, ISNULL(c7, 0) AS c7, ISNULL(c8, 0) AS c8, ISNULL(c9, 0) AS c9, 
                                                                           ISNULL(c10, 0) AS c10, cod_almacen
                                                    FROM          dbo.mp_mov_extraor_vw
                                                    UNION ALL
                                                    SELECT     cod_material, cod_color, cantidad AS cant, c1, c2, c3, c4, c5, c6, c7, c8, c9, c10, cod_almacen
                                                    FROM         dbo.mp_remitos_vw
                                                    UNION ALL
                                                    SELECT     cod_material, cod_color, ISNULL(cantidad, 0) AS cant, ISNULL(cant_1, 0) AS c1, ISNULL(cant_2, 0) AS c2, ISNULL(cant_3, 0) 
                                                                          AS c3, ISNULL(cant_4, 0) AS c4, ISNULL(cant_5, 0) AS c5, ISNULL(cant_6, 0) AS c6, ISNULL(cant_7, 0) AS c7, ISNULL(cant_8, 0) 
                                                                          AS c8, ISNULL(cant_9, 0) AS c9, ISNULL(cant_10, 0) AS c10, cod_almacen
                                                    FROM         dbo.tranferencias_materias_primas_v) conconsumo
                            WHERE      (cod_almacen <> '16')) a ON dbo.Materias_primas.cod_material = a.cod_material COLLATE Modern_Spanish_CI_AS AND 
                      dbo.Materias_primas.cod_color = a.cod_color COLLATE Modern_Spanish_CI_AS LEFT OUTER JOIN
                      dbo.rubros_materias_primas ON dbo.materiales.cod_rubro = dbo.rubros_materias_primas.cod_rubro LEFT OUTER JOIN
                      dbo.rango_talles ON dbo.materiales.cod_rango = dbo.rango_talles.cod_rango
WHERE     (dbo.Materias_primas.anulado = 'N') AND (dbo.materiales.anulado = 'N')
GROUP BY dbo.Materias_primas.cod_material, dbo.Materias_primas.cod_color, a.cod_almacen, dbo.materiales.denom_material, 
                      dbo.rubros_materias_primas.denom_rubro, dbo.rango_talles.posic_1, dbo.materiales.unidad_medida, 
                      dbo.Materias_primas.precio_unitario / dbo.materiales.factor_conversion, dbo.materiales.factor_conversion
ORDER BY dbo.materiales.denom_material, dbo.Materias_primas.cod_color, a.cod_almacen

GO
