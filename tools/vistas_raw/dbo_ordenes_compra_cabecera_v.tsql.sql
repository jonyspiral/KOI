CREATE VIEW [dbo].[ordenes_compra_cabecera_v] AS

CREATE VIEW ordenes_compra_cabecera_v AS
	SELECT		c.*, (SELECT SUM(cantidad_pendiente) FROM ordenes_compra_detalle d WHERE c.cod_orden_de_compra = d.cod_orden_de_compra) pendiente
	FROM		ordenes_compra_cabecera c
GO
