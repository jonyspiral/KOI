CREATE VIEW dbo.mp_consumos_facon_vw
AS
SELECT     fecha_ultima_modificacion AS fecha_movimiento, COD_MATERIAL, cod_color, - (1 * ISNULL(cantidad, 0)) AS cant, - (1 * ISNULL(cant_1, 0)) AS c1, - (1 * ISNULL(cant_2, 
                      0)) AS c2, - (