CREATE VIEW [dbo].[clientes_v] AS



CREATE VIEW clientes_v AS
	SELECT (o.nombres + ' ' + o.apellido) nombre_vendedor, debe.fecha_debe, debe.importe_pendiente_debe, haber.fecha_haber, haber.importe_pendiente_haber, a.saldo,
			plazos.dias_promedio_pago, ISNULL(total_cheques.total_cheques, 0) total_cheques, ISNULL(pagos_ingresados_mes.pagos_ingresados_mes, 0) pagos_ingresados_mes, c.*
	FROM clientes c
	LEFT JOIN operadores_v o ON c.cod_vendedor = o.cod_operador
	LEFT JOIN (
		SELECT c.cod_cli, ISNULL(SUM((CASE WHEN d.tipo_docum = 'NDB' OR d.tipo_docum = 'FAC' THEN 1 ELSE -1 END) * d.importe_pendiente), 0) saldo
		FROM Clientes c
		LEFT JOIN documentos d ON c.cod_cli = d.cod_cliente AND d.importe_pendiente > 0
		GROUP BY c.cod_cli
	) a ON a.cod_cli = c.cod_cli
	LEFT JOIN (
		SELECT a.cod_cliente, debe.fecha fecha_debe, SUM(a.importe_pendiente) importe_pendiente_debe
		FROM documentos a
		INNER JOIN (
			SELECT cod_cliente, MIN(fecha) fecha
			FROM documentos
			WHERE importe_pendiente > 0 AND (tipo_docum = 'FAC' OR tipo_docum = 'NDB')
			GROUP BY cod_cliente
		) debe ON a.cod_cliente = debe.cod_cliente AND a.fecha = debe.fecha
		WHERE a.cod_cliente > 0
		GROUP BY a.cod_cliente, debe.fecha
	) debe ON c.cod_cli = debe.cod_cliente
	LEFT JOIN (
		SELECT a.cod_cliente, haber.fecha fecha_haber, SUM(a.importe_pendiente) importe_pendiente_haber
		FROM documentos a
		INNER JOIN (
			SELECT cod_cliente, MIN(fecha) fecha
			FROM documentos
			WHERE importe_pendiente > 0 AND (tipo_docum = 'NCR' OR tipo_docum = 'REC')
			GROUP BY cod_cliente
		) haber ON a.cod_cliente = haber.cod_cliente AND a.fecha = haber.fecha
		WHERE a.cod_cliente > 0
		GROUP BY a.cod_cliente, haber.fecha
	) haber ON c.cod_cli = haber.cod_cliente
	LEFT JOIN (
		SELECT cod_cliente, AVG(ISNULL(dias_promedio_pago, 0)) dias_promedio_pago
		FROM documentos a
		WHERE fecha >= dbo.relativeDate(GETDATE(), 'first', -6) AND fecha > dbo.toDate('01/09/2013') AND dias_promedio_pago IS NOT NULL
		GROUP BY cod_cliente
	) plazos ON c.cod_cli = plazos.cod_cliente
	LEFT JOIN (
		SELECT cod_cliente, SUM(importe) total_cheques
		FROM cheque
		WHERE fecha_vencimiento >= dbo.relativeDate(GETDATE(), 'today', 0) AND anulado = 'N' AND cod_cliente IS NOT NULL
		GROUP BY cod_cliente
	) total_cheques ON c.cod_cli = total_cheques.cod_cliente
	LEFT JOIN (
		SELECT cod_cliente, SUM(importe_total) pagos_ingresados_mes
		FROM recibo
		WHERE month(fecha_documento) = month(GETDATE()) AND year(fecha_documento) = year(GETDATE()) AND anulado = 'N' AND cod_cliente IS NOT NULL
		GROUP BY cod_cliente
	) pagos_ingresados_mes ON c.cod_cli = pagos_ingresados_mes.cod_cliente


GO
