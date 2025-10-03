
CREATE VIEW [dbo].[cuenta_corriente_historica] AS
	SELECT TOP 100 PERCENT r.empresa empresa, 1 punto_venta, 'REC' tipo_docum, '' tipo_docum_2, r.nro_recibo numero, 'R' letra, NULL nro_comprobante,
						   r.cod_cliente cod_cliente, r.fecha_documento 