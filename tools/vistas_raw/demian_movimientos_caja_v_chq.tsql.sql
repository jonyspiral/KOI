CREATE VIEW [demian].[movimientos_caja_v_chq] AS
CREATE VIEW movimientos_caja_v_chq AS 

SELECT * FROM (
	SELECT 'E' tipo, 'DC' tipo_documento, dc.cod_acreditar_debitar_cheque numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d dc1
		INNER JOIN importe_por_operacion_c ipoc1 ON dc1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE dc1.entrada_salida = 'S' AND dcc.cod_acreditar_debitar_cheque = dc1.cod_acreditar_debitar_cheque AND dcc.empresa = dc1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d dc2
		INNER JOIN importe_por_operacion_c ipoc2 ON dc2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE dc2.entrada_salida = 'E' AND dcc.cod_acreditar_debitar_cheque = dc2.cod_acreditar_debitar_cheque AND dcc.empresa = dc2.empresa) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE dc.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE dc.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(dcc.fecha,'today',0) fecha,
		dc.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM acreditar_debitar_cheque_d dc
	INNER JOIN acreditar_debitar_cheque_c dcc ON dc.cod_acreditar_debitar_cheque = dcc.cod_acreditar_debitar_cheque AND dc.empresa = dcc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON dc.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE dc.entrada_salida = 'S' AND dcc.tipo = 'D'
) a

UNION ALL

SELECT * FROM (
	SELECT (case when ac.entrada_salida = 'E' then 'I' else 'E' end) tipo, 'AC' tipo_documento, ac.cod_acreditar_debitar_cheque numero,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d ac1
		INNER JOIN importe_por_operacion_c ipoc1 ON ac1.cod_importe_operacion = ipoc1.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc1.cod_caja
		WHERE ac1.entrada_salida = 'S' AND acc.cod_acreditar_debitar_cheque = ac1.cod_acreditar_debitar_cheque AND acc.empresa = ac1.empresa) de,
		(SELECT '[' + cast(caja.cod_caja AS VARCHAR) + '] ' + caja.nombre
		FROM acreditar_debitar_cheque_d ac2
		INNER JOIN importe_por_operacion_c ipoc2 ON ac2.cod_importe_operacion = ipoc2.cod_importe_operacion
		INNER JOIN caja caja ON caja.cod_caja = ipoc2.cod_caja
		WHERE ac2.entrada_salida = 'E' AND acc.cod_acreditar_debitar_cheque = ac2.cod_acreditar_debitar_cheque AND acc.empresa = ac2.empresa) para,
		(SELECT ISNULL(SUM(e1.importe), 0)
		FROM importe_por_operacion_c ipoc1
		INNER JOIN importe_por_operacion_d ipod1 ON ipoc1.cod_importe_operacion = ipod1.cod_importe_operacion AND ipod1.tipo_importe = 'E'
		INNER JOIN efectivo e1 ON ipod1.cod_importe = e1.cod_efectivo
		WHERE ac.cod_importe_operacion = ipoc1.cod_importe_operacion) efectivo,
		(SELECT ISNULL(SUM(c1.importe), 0)
		FROM importe_por_operacion_c ipoc2
		INNER JOIN importe_por_operacion_d ipod2 ON ipoc2.cod_importe_operacion = ipod2.cod_importe_operacion AND ipod2.tipo_importe = 'C'
		INNER JOIN cheque c1 ON ipod2.cod_importe = c1.cod_cheque
		WHERE ac.cod_importe_operacion = ipoc2.cod_importe_operacion) cheques,
		0 transferencias,
		0 retenciones,
		importe_total total,
		ipoc.cod_caja, dbo.relativeDate(acc.fecha,'t
oday',0) fecha,
		ac.empresa empresa,
		ipoc.cod_importe_operacion cod_importe_operacion,
		'N' anulado
	FROM acreditar_debitar_cheque_d ac
	INNER JOIN acreditar_debitar_cheque_c acc ON acc.cod_acreditar_debitar_cheque = ac.cod_acreditar_debitar_cheque AND ac.empresa = acc.empresa
	INNER JOIN importe_por_operacion_c ipoc ON ac.cod_importe_operacion = ipoc.cod_importe_operacion
	WHERE acc.tipo = 'C'
) b


GO
