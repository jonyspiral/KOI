CREATE VIEW [dbo].[gestion_proveedores_1] AS

CREATE VIEW gestion_proveedores_1 AS
	SELECT p.cod_prov, p.razon_social, p.cuit, p.imputacion_especifica, i.denominacion, p.anulado, p.observaciones_gestion, a.saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN (
		SELECT p.cod_prov, ISNULL(SUM((CASE WHEN a.tipo_docum = 'NDB' OR a.tipo_docum = 'FAC' THEN 1 ELSE -1 END) * a.importe_pendiente), 0) saldo
		FROM proveedores_datos p
		LEFT JOIN documento_proveedor_aplicacion_v a ON p.cod_prov = a.cod_proveedor AND a.importe_pendiente > 0 AND a.factura_gastos = 'N' AND empresa = 1
		GROUP BY p.cod_prov
	) a ON p.cod_prov = a.cod_prov
	LEFT JOIN plan_cuentas i ON i.cuenta = p.imputacion_especifica

GO
