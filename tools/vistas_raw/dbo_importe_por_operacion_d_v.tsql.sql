CREATE VIEW [dbo].[importe_por_operacion_d_v] AS

	CREATE VIEW importe_por_operacion_d_v AS
		SELECT d.*, c.tipo_transferencia, c.cod_caja, c.fecha_caja, c.fecha_alta
		FROM importe_por_operacion_d d
		INNER JOIN importe_por_operacion_c c ON d.cod_importe_operacion = c.cod_importe_operacion
GO
