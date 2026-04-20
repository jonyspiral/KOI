
CREATE VIEW dbo.stock_mp_vw AS
SELECT	s.cod_almacen, s.cod_material, s.cod_color,
		ISNULL(s.cant_1, 0) AS S1,
		ISNULL(s.cant_2, 0) AS S2,
		ISNULL(s.cant_3, 0) AS S3,
		ISNULL(s.cant_4, 0) AS S4,
		ISNULL(s.cant_5, 0) AS S5,
		ISNULL(s.cant_6, 