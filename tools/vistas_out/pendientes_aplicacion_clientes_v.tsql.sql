CREATE VIEW [dbo].[pendientes_aplicacion_clientes_v] AS
	SELECT	d.cod_cliente, c.razon_social, d.empresa, d.fecha_documento,
			dbo.sumarTiempo(d.fecha_documento, 'dia', 60) fecha_vencimiento,
			d.tipo_docum, d.letra, d.nro_documento, d.observaciones,