CREATE VIEW [dbo].[importes_op_acumulado_mes] AS


CREATE VIEW [dbo].[importes_op_acumulado_mes] AS
	SELECT p.cod_prov cod_proveedor, SUM(ISNULL(op.importe_sujeto_ret, 0)) importe_acumulado_mes, SUM(ISNULL(re.importe_retenido, 0)) importe_retenido_mes
	FROM proveedores_datos p
		LEFT JOIN orden_de_pago op ON
			p.cod_prov = op.cod_proveedor AND
			op.anulado = 'N' AND
			op.fecha_documento > dbo.relativeDate(GETDATE(), 'first', 0) AND
			op.fecha_documento < dbo.relativeDate(GETDATE(), 'last', 0)
		LEFT JOIN retenciones_impositivas_efectuad re ON
			re.cod_prov = p.cod_prov AND
			re.fecha_retencion > dbo.relativeDate(GETDATE(), 'first', 0) AND
			re.fecha_retencion < dbo.relativeDate(GETDATE(), 'last', 0)
	WHERE p.anulado = 'N' AND op.anulado = 'N' AND op.empresa = '1'
	GROUP BY p.cod_prov
GO
