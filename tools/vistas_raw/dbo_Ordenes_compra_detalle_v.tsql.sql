CREATE VIEW [dbo].[Ordenes_compra_detalle_v] AS



CREATE VIEW Ordenes_compra_detalle_v AS
	SELECT d.*, c.cod_proveedor, c.fecha_emision, c.es_hexagono
	FROM Ordenes_compra_cabecera c
	INNER JOIN Ordenes_compra_detalle d ON c.cod_sucursal = d.cod_sucursal AND c.nro_orden_compra = d.nro_orden_compra 


GO
