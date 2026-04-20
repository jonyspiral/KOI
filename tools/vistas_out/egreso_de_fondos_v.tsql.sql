CREATE VIEW [dbo].[egreso_de_fondos_v] AS
	SELECT op.nro_orden_de_pago numero, op.empresa, (case when op.cod_proveedor is null then op.beneficiario else ('[' + cast(p.cod_prov AS VARCHAR) + '] ' + p.razon_social) end) de_para,
		(SELECT ISNULL(SUM(e1.im