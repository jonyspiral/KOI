CREATE VIEW [dbo].[tareas_cabecera_v] AS
CREATE VIEW tareas_cabecera_v AS
	SELECT tc.*, o.cod_articulo, o.cod_color_articulo, o.version
	FROM Tareas_cabecera tc
	INNER JOIN Orden_fabricacion o ON o.nro_orden_fabricacion = tc.nro_orden_fabricacion

GO
