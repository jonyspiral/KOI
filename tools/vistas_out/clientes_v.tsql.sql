


CREATE VIEW clientes_v AS
	SELECT (o.nombres + ' ' + o.apellido) nombre_vendedor, debe.fecha_debe, debe.importe_pendiente_debe, haber.fecha_haber, haber.importe_pendiente_haber, a.saldo,
			plazos.dias_promedio_pago, ISNULL(total_cheques.total_che