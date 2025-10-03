CREATE VIEW [dbo].[mp_mov_extraor_vw] AS
CREATE VIEW dbo.mp_mov_extraor_vw
AS
SELECT     fecha_movimiento, cod_material, cod_color_material AS cod_color, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cantidad, 0) 
                      AS cantidad, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_1, 0) AS c1, 
                      (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_2, 0) AS c2, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) 
                      * ISNULL(cant_3, 0) AS c3, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_4, 0) AS c4, 
                      (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_5, 0) AS c5, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) 
                      * ISNULL(cant_6, 0) AS c6, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_7, 0) AS c7, 
                      (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_8, 0) AS c8, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) 
                      * ISNULL(cant_9, 0) AS c9, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cant_10, 0) AS c10, cod_almacen, 'Ajuste' AS Motivo, 
                      clave_tabla AS nro_operacion, efecto_movimiento
FROM         dbo.materias_primas_movim_extraor

GO
