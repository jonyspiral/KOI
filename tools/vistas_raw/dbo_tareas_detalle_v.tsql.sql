CREATE VIEW [dbo].[tareas_detalle_v] AS

CREATE VIEW tareas_detalle_v AS
	SELECT td.*, tc.anulado, o.cod_articulo, o.cod_color_articulo, o.version, c.vigente, a.vigente articulo_vigente, a.naturaleza
	FROM Tareas_detalle td
	INNER JOIN Tareas_cabecera tc ON tc.nro_orden_fabricacion = td.nro_orden_fabricacion AND tc.nro_tarea = td.nro_tarea
	INNER JOIN Orden_fabricacion o ON o.nro_orden_fabricacion = tc.nro_orden_fabricacion
	INNER JOIN colores_por_articulo c ON c.cod_articulo = o.cod_articulo AND c.cod_color_articulo = o.cod_color_articulo
	INNER JOIN articulos a ON a.cod_articulo = o.cod_articulo


GO
