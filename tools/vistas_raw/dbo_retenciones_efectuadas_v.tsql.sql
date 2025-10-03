CREATE VIEW [dbo].[retenciones_efectuadas_v] AS
CREATE VIEW retenciones_efectuadas_v AS
SELECT
	r.fecha, r.nombre razon_social, r.cuit, r.importe importe_retencion, o.nro_orden_de_pago, o.importe_total importe_orden_de_pago
FROM retencion_efectuada r
LEFT JOIN importe_por_operacion_d ixo ON ixo.tipo_importe = 'R' AND ixo.cod_importe = r.cod_retencion
LEFT JOIN orden_de_pago o ON o.cod_importe_operacion = ixo.cod_importe_operacion
WHERE r.anulado = 'N' AND r.importe > 0 AND o.anulado = 'N'
GO
