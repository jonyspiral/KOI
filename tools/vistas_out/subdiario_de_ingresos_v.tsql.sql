CREATE VIEW [dbo].[subdiario_de_ingresos_v] AS
	SELECT *, total - efectivo - transferencias - retenciones AS cheques FROM ( --Calculamos los cheques así para que la consulta sea más rápida, 10 segundos contra 13
		SELECT r.nro_recibo numero, r.empresa, 