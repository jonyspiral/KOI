CREATE VIEW movimientos_caja_v_anul AS

	SELECT 'E' tipo, 'REC' tipo_documento, r.nro_recibo numero, (case when r.cod_cliente is null then r.recibido_de else c.razon_social end) de, 'SPIRAL SHOES S.A.' para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM_importe_operacion = ipoc.cod_importe_operacion
	WHERE op.anulado = 'S'

	UNION ALL

	SELECT (case when tbo.entrada_salida = 'E' then 'E' else 'I' end) tipo, 'TB' tipo_documento, tbo.cod_transferencia_ban numero,
		(case when tbo.entrada_salida = 'Eecha,
		aps.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		aps.anulado
	FROM aporte_socio aps
	LEFT OUTER JOIN socio s ON s.cod_socio = aps.cod_socio
	INNER JOIN importe_por_operacion_c ipoc ON aps.cod_importe_operacion = ipo