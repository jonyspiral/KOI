CREATE VIEW dbo.mp_mov_extraor_vw
AS
SELECT     fecha_movimiento, cod_material, cod_color_material AS cod_color, (CASE WHEN efecto_movimiento = 'S' THEN - 1 ELSE 1 END) * ISNULL(cantidad, 0) 
                      AS cantidad, (CASE WHEN efecto_movimie