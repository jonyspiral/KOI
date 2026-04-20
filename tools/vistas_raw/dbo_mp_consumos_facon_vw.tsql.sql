CREATE VIEW [dbo].[mp_consumos_facon_vw] AS
CREATE VIEW dbo.mp_consumos_facon_vw
AS
SELECT     fecha_ultima_modificacion AS fecha_movimiento, COD_MATERIAL, cod_color, - (1 * ISNULL(cantidad, 0)) AS cant, - (1 * ISNULL(cant_1, 0)) AS c1, - (1 * ISNULL(cant_2, 
                      0)) AS c2, - (1 * ISNULL(cant_3, 0)) AS c3, - (1 * ISNULL(cant_4, 0)) AS c4, - (1 * ISNULL(cant_5, 0)) AS c5, - (1 * ISNULL(cant_6, 0)) AS c6, - (1 * ISNULL(cant_7, 0)) AS c7, 
                      - (1 * ISNULL(cant_8, 0)) AS c8, '01' AS cod_almacen, 'Consumo facon: ' + COD_OPERADOR AS motivo
FROM         dbo.Stock_mp_fc

GO
