CREATE VIEW [dbo].[Stock_pt_detalle] AS

CREATE VIEW [dbo].[stock_pt_detalle] AS
SELECT	CAST(SUBSTRING(CONVERT(VARCHAR, fecha_alta, 103), 0, 11) AS DATETIME) AS fecha_movimiento,
		fecha_alta fecha_alta, cod_almacen, cod_articulo, cod_color_articulo,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_1 c_1,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_2 c_2,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_3 c_3,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_4 c_4,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_5 c_5,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_6 c_6,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_7 c_7,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_8 c_8,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_9 c_9,
		(CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_10 c_10,
		observaciones tipo_movimiento
FROM movimientos_stock
GO
