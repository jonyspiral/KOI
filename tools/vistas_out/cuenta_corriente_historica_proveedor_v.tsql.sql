CREATE VIEW [dbo].[cuenta_corriente_historica_proveedor_v] AS
	SELECT
		dp.empresa, dp.punto_venta, dp.tipo_docum, dp.nro_documento, dp.letra, dp.cod_proveedor, dbo.relativeDate(dp.fecha,'today',0) fecha, dp.observaciones, (case when dp.tipo_docum = 'NC