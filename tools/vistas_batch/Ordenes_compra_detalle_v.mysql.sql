CREATE OR REPLACE VIEW Ordenes_compra_detalle_v AS
1> 2> 1> 2> 3> 4> 5> 6> 7> 8> 9> text
CREATE VIEW Ordenes_compra_detalle_v AS
	SELECT d.*, c.cod_proveedor, c.fecha_emision, c.es_hexagono
	FROM Ordenes_compra_cabecera c
	INNER JOIN Ordenes_compra_detalle d ON c.cod_sucursal = d.cod_sucursal AND c.nro_orden_compra = d.nro_orden_compra ;
