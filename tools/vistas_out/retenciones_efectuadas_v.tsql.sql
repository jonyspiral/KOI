CREATE VIEW retenciones_efectuadas_v AS
SELECT
	r.fecha, r.nombre razon_social, r.cuit, r.importe importe_retencion, o.nro_orden_de_pago, o.importe_total importe_orden_de_pago
FROM retencion_efectuada r
LEFT JOIN importe_por_operacion_d ixo ON ixo.tip