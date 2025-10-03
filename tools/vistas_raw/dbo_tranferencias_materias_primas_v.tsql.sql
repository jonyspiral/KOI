CREATE VIEW [dbo].[tranferencias_materias_primas_v] AS
CREATE VIEW dbo.tranferencias_materias_primas_v
AS
SELECT     TOP 100 PERCENT fecha_alta AS fecha_Movimiento, cod_material, cod_color, cantidad, cant_1, cant_2, cant_3, cant_4, cant_5, cant_6, cant_7, cant_8, 
                      cant_9, cant_10, cod_almacen, motivo, Nro_operacion, efecto_movimiento
FROM         (SELECT     dbo.tranferencias_materias_primas_c.fecha_alta, dbo.tranferencias_materias_primas_c.almacen_destino AS cod_almacen, 
                                              dbo.tranferencias_materias_primas_d.cod_material, dbo.tranferencias_materias_primas_d.cod_color, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cantidad, 0) AS cantidad, ISNULL(dbo.tranferencias_materias_primas_d.cant_1, 0) AS cant_1, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_2, 0) AS cant_2, ISNULL(dbo.tranferencias_materias_primas_d.cant_3, 0) AS cant_3, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_4, 0) AS cant_4, ISNULL(dbo.tranferencias_materias_primas_d.cant_5, 0) AS cant_5, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_6, 0) AS cant_6, ISNULL(dbo.tranferencias_materias_primas_d.cant_7, 0) AS cant_7, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_8, 0) AS cant_8, ISNULL(dbo.tranferencias_materias_primas_d.cant_9, 0) AS cant_9, 
                                              ISNULL(dbo.tranferencias_materias_primas_d.cant_10, 0) AS cant_10, 
                                              dbo.tranferencias_materias_primas_c.nro_tranferencia_mp AS Nro_operacion, 'E' AS efecto_movimiento, 'TRANF POS' AS motivo
                       FROM          dbo.tranferencias_materias_primas_d INNER JOIN
                                              dbo.tranferencias_materias_primas_c ON 
                                              dbo.tranferencias_materias_primas_d.nro_tranferencia_mp = dbo.tranferencias_materias_primas_c.nro_tranferencia_mp
                       UNION ALL
                       SELECT     dbo.tranferencias_materias_primas_c.fecha_alta, dbo.tranferencias_materias_primas_c.almacen_origen AS cod_almacen, 
                                             dbo.tranferencias_materias_primas_d.cod_material, dbo.tranferencias_materias_primas_d.cod_color, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cantidad, 0) AS Expr1, - ISNULL(dbo.tranferencias_materias_primas_d.cant_1, 0) AS Expr2, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_2, 0) AS Expr3, - ISNULL(dbo.tranferencias_materias_primas_d.cant_3, 0) AS Expr4, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_4, 0) AS Expr5, - ISNULL(dbo.tranferencias_materias_primas_d.cant_5, 0) AS Expr6, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_6, 0) AS Expr7, - ISNULL(dbo.tranferencias_materias_primas_d.cant_7, 0) AS Expr8, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_8, 0) AS Expr9, - ISNULL(dbo.tranferencias_materias_primas_d.cant_9, 0) AS cant_9, 
                                             - ISNULL(dbo.tranferencias_materias_primas_d.cant_10, 0) AS cant_10, 
                                             dbo.tranferencias_materias_primas_c.nro_tranferencia_mp AS Nro_operacion, 'S' AS efecto_movimiento, 'TRANF NEG' AS motivo
                       FROM         dbo.tranferencias_materias_primas_d INNER JOIN
                                             dbo.tranferencias_materias_primas_c ON 
                                             dbo.tranferencias_materias_primas_d.nro_tranferencia_mp = dbo.tranferencias_materias_primas_c.nro_tranferencia_mp) tr

GO
