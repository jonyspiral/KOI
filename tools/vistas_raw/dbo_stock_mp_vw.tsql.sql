CREATE VIEW [dbo].[stock_mp_vw] AS

CREATE VIEW dbo.stock_mp_vw AS
SELECT	s.cod_almacen, s.cod_material, s.cod_color,
		ISNULL(s.cant_1, 0) AS S1,
		ISNULL(s.cant_2, 0) AS S2,
		ISNULL(s.cant_3, 0) AS S3,
		ISNULL(s.cant_4, 0) AS S4,
		ISNULL(s.cant_5, 0) AS S5,
		ISNULL(s.cant_6, 0) AS S6,
		ISNULL(s.cant_7, 0) AS S7,
		ISNULL(s.cant_8, 0) AS S8,
		ISNULL(s.cant_9, 0) AS S9,
		ISNULL(s.cant_10, 0) AS S10,
		ISNULL(s.cantidad, 0) AS cant_s,
		al.denom_almacen nombre_almacen, m.denom_material nombre_material, cmp.denom_color nombre_color,
		rt.cod_rango_nro cod_rango, rt.denom_rango, rt.posic_1, mp.fecha_validacion_stock
FROM stock_mp_tabla s
INNER JOIN Materias_primas mp ON s.cod_material = mp.cod_material AND s.cod_color = mp.cod_color
INNER JOIN Colores_materias_primas cmp ON s.cod_color = cmp.cod_color
INNER JOIN materiales m ON s.cod_material = m.cod_material
LEFT JOIN rango_talles rt ON rt.cod_rango = m.cod_rango
INNER JOIN almacenes al ON al.cod_almacen = s.cod_almacen
GO
