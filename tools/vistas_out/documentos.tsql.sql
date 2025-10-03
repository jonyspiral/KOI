

CREATE VIEW [dbo].[documentos] AS
	SELECT
			empresa, punto_venta, tipo_docum, numero, letra, nro_comprobante, anulado, tipo_docum_2, cod_cliente, cod_sucursal, cod_usuario,
			cancel_nro_documento, causa, CAST(observaciones AS VARCHAR(8000)) obser