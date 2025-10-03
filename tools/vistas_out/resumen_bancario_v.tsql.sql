CREATE VIEW resumen_bancario_v AS
	SELECT 'E' tipo, 'DC' tipo_documento, dc.cod_acreditar_debitar_cheque numero,
		'Cheque Nº: ' + cast((SELECT TOP 1 c.numero
		FROM cheque c
		INNER JOIN importe_por_operacion_d ipod2 ON ipod2.cod_importe_operacion = = 'E' AND tic.cod_transferencia_int = ti2.cod_transferencia_int AND tic.empresa = ti2.empresa) AS VARCHAR)
		) detalle,
		importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(tic.fecha_documento,'today',0) fecha,
		ti.empresa empresa,
		cast(tic.