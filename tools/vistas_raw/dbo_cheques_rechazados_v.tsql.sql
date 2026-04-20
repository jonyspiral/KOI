CREATE VIEW [dbo].[cheques_rechazados_v] AS


CREATE VIEW cheques_rechazados_v AS
	SELECT fecha_documento fecha, c.fecha_vencimiento, c.empresa, cli.cod_cli, cli.razon_social cliente_razon_social, p.cod_prov, p.razon_social proveedor_razon_social,
	c.librador_nombre, b.nombre banco_nombre, c.numero, c.importe, m.nombre_motivo, rc.observaciones, o.cod_operador cod_vendedor, (pe.nombres + ' ' + pe.apellido) nombre_vendedor
	FROM cheque c
	INNER JOIN rechazo_de_cheque_c rc ON c.cod_rechazo_cheque = rc.cod_rechazo_cheque AND c.empresa = rc.empresa
	LEFT OUTER JOIN clientes cli ON cli.cod_cli = c.cod_cliente
	LEFT OUTER JOIN operadores o ON cli.cod_vendedor = o.cod_operador
	LEFT OUTER JOIN personal pe ON pe.cod_personal = o.cod_personal
	LEFT OUTER JOIN proveedores_datos p ON p.cod_prov = c.cod_proveedor
	INNER JOIN banco b ON b.cod_banco = c.cod_banco
	INNER JOIN motivo m ON m.cod_motivo = rc.motivo
	WHERE c.cod_rechazo_cheque IS NOT NULL
GO
