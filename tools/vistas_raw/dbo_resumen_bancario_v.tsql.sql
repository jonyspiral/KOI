CREATE VIEW [dbo].[resumen_bancario_v] AS
CREATE VIEW resumen_bancario_v AS
	SELECT 'E' tipo, 'DC' tipo_documento, dc.cod_acreditar_debitar_cheque numero,
		'Cheque Nº: ' + cast((SELECT TOP 1 c.numero
		FROM cheque c
		INNER JOIN importe_por_operacion_d ipod2 ON ipod2.cod_importe_operacion = ipoc.cod_importe_operacion AND ipod2.tipo_importe = 'C' AND ipod2.cod_importe = c.cod_cheque
		) AS VARCHAR) detalle,
		importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(dcc.fecha,'today',0) fecha,
		dc.empresa empresa,
		cast(dcc.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM acreditar_debitar_cheque_d dc
	INNER JOIN acreditar_debitar_cheque_c dcc ON dc.cod_acreditar_debitar_cheque = dcc.cod_acreditar_debitar_cheque AND dc.empresa = dcc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON dc.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE dc.entrada_salida = 'E' AND dcc.tipo = 'D'

	UNION ALL

	SELECT 'I' tipo, 'AC' tipo_documento, ac.cod_acreditar_debitar_cheque numero,
		'Cheque Nº: ' + cast((SELECT TOP 1 c.numero
		FROM cheque c
		INNER JOIN importe_por_operacion_d ipod2 ON ipod2.cod_importe_operacion = ipoc.cod_importe_operacion AND ipod2.tipo_importe = 'C' AND ipod2.cod_importe = c.cod_cheque
		) AS VARCHAR) detalle,
		importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(acc.fecha,'today',0) fecha,
		ac.empresa empresa,
		cast(acc.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM acreditar_debitar_cheque_d ac
	INNER JOIN acreditar_debitar_cheque_c acc ON acc.cod_acreditar_debitar_cheque = ac.cod_acreditar_debitar_cheque AND ac.empresa = acc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ac.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE ac.entrada_salida = 'S' AND acc.tipo = 'C'

	UNION ALL

	SELECT 'I' tipo, 'DB' tipo_documento, db.cod_deposito_bancario numero,
		'-' detalle,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE db.cod_importe_operacion = ipoc1.cod_importe_operacion) importe,
		ipoc.cod_caja, dbo.relativeDate(dbc.fecha_documento,'today',0) fecha,
		db.empresa empresa,
		cast(dbc.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM deposito_bancario_d db
	INNER JOIN deposito_bancario_c dbc ON dbc.cod_deposito_bancario = db.cod_deposito_bancario AND dbc.empresa = db.empresa
	INNER JOIN importe_por_operacion_c ipoc ON db.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE db.entrada_salida = 'E' AND
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE db.cod_importe_operacion = ipoc1.cod_importe_operacion) > 0

	UNION ALL

	SELECT (case when ti.entrada_salida = 'S' then 'E' else 'I' end) tipo, 'TI' tipo_documento, ti.cod_transferencia_int numero,
		(
		'Desde: ' + cast((SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM transferencia_interna_d ti1
		INNER JOIN importe_por_operacion_c ipoc1 ON ti1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE ti1.entrada_salida = 'S' AND tic.cod_transferencia_int = ti1.cod_transferencia_int AND tic.empresa = ti1.empresa) AS VARCHAR)
		+ ' - ' +
		'Hacia: ' + cast((SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM transferencia_interna_d ti2
		INNER JOIN importe_por_operacion_c ipoc2 ON ti2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE ti2.entrada_salida 
= 'E' AND tic.cod_transferencia_int = ti2.cod_transferencia_int AND tic.empresa = ti2.empresa) AS VARCHAR)
		) detalle,
		importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(tic.fecha_documento,'today',0) fecha,
		ti.empresa empresa,
		cast(tic.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM transferencia_interna_d ti
	INNER JOIN transferencia_interna_c tic ON tic.cod_transferencia_int = ti.cod_transferencia_int AND tic.empresa = ti.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ti.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja

	UNION ALL

	SELECT (case when tbo.entrada_salida = 'E' then 'I' else 'E' end) tipo, 'TB' tipo_documento, tbo.cod_transferencia_ban numero,
		(case when tbo.entrada_salida = 'S' then 'Número transferencia: ' + cast(tbo.numero_transferencia AS VARCHAR) + ' ' else '' end)
		+ (case when tbo.hacia_desde IS NULL then '' else ((case when tbo.entrada_salida = 'S' then 'Receptor: ' else 'Emisor: ' end) + cast(tbo.hacia_desde AS VARCHAR)) end) detalle,
		tbo.importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(tbo.fecha,'today',0) fecha,
		tbo.empresa empresa,
		cast(tbo.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM transferencia_bancaria_operacion tbo
	INNER JOIN cuenta_bancaria cb ON tbo.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
	INNER JOIN caja caja ON caja.cod_caja = cb.cod_caja
	INNER JOIN importe_por_operacion_c ipoc ON tbo.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE tbo.anulado = 'N'

	UNION ALL

	SELECT 'I' tipo, 'VC' tipo_documento, ti.cod_venta_cheques numero,
		(SELECT 'Cantidad de cheques: ' + cast(count(*) AS VARCHAR)
		FROM venta_cheques_d ti2
		INNER JOIN importe_por_operacion_d ipod3 ON ti2.cod_importe_operacion = ipod3.cod_importe_operacion
		INNER JOIN cheque c ON c.cod_cheque = ipod3.cod_importe
		WHERE ti2.entrada_salida = 'S' AND ti2.cod_venta_cheques = ti.cod_venta_cheques AND ti2.empresa = ti.empresa
		) detalle,
		ti.importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(tic.fecha_documento,'today',0) fecha,
		ti.empresa empresa,
		cast(tic.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM venta_cheques_d ti
	INNER JOIN venta_cheques_c tic ON tic.cod_venta_cheques = ti.cod_venta_cheques AND tic.empresa = ti.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ti.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja
	WHERE ti.entrada_salida = 'E'
	
	UNION ALL

	SELECT 'I' tipo, 'PB' tipo_documento, p.nro_prestamo numero,
		'Importe pendiente: ' + cast(cast(p.importe_pendiente AS NUMERIC(10,2)) AS VARCHAR) detalle,
		p.importe_total importe,
		ipoc.cod_caja, dbo.relativeDate(p.fecha_documento,'today',0) fecha,
		p.empresa empresa,
		cast(p.observaciones AS VARCHAR) observaciones,
		ipoc.cod_importe_operacion cod_importe_operacion
	FROM prestamo p
	INNER JOIN importe_por_operacion_c ipoc ON p.cod_importe_operacion = ipoc.cod_importe_operacion
	INNER JOIN caja caja ON caja.cod_caja = ipoc.cod_caja
	WHERE p.anulado = 'N'
GO
