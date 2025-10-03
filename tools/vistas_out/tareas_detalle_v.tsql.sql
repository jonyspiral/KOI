
CREATE VIEW tareas_detalle_v AS
	SELECT td.*, tc.anulado, o.cod_articulo, o.cod_color_articulo, o.version, c.vigente, a.vigente articulo_vigente, a.naturaleza
	FROM Tareas_detalle td
	INNER JOIN Tareas_cabecera tc ON tc.nro_orden_fabricacion = td.nro