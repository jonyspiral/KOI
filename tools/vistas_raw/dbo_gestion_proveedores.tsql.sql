CREATE VIEW [dbo].[gestion_proveedores] AS

CREATE VIEW gestion_proveedores AS
	SELECT p.cod_prov, p.razon_social, p.cuit, p.imputacion_especifica, i.denominacion, p.anulado, p.observaciones_gestion, a.saldo, total_cheques
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN (
		SELECT p.cod_prov, ISNULL(SUM((CASE WHEN a.tipo_docum = 'NDB' OR a.tipo_docum = 'FAC' THEN 1 ELSE -1 END) * a.importe_pendiente), 0) saldo
		FROM proveedores_datos p
		LEFT JOIN documento_proveedor_aplicacion_v a ON p.cod_prov = a.cod_proveedor AND a.importe_pendiente > 0 AND a.factura_gastos = 'N'
		GROUP BY p.cod_prov
	) a ON p.cod_prov = a.cod_prov
	LEFT JOIN plan_cuentas i ON i.cuenta = p.imputacion_especifica
	LEFT JOIN (
		SELECT cod_proveedor, SUM(importe) total_cheques
		FROM cheque
		WHERE fecha_vencimiento >= dbo.relativeDate(GETDATE(), 'today', 0) AND anulado = 'N' AND cod_proveedor IS NOT NULL
		GROUP BY cod_proveedor
	) total_cheques ON p.cod_prov = total_cheques.cod_proveedor


GO
