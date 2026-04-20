
CREATE VIEW gestion_proveedores_1 AS
	SELECT p.cod_prov, p.razon_social, p.cuit, p.imputacion_especifica, i.denominacion, p.anulado, p.observaciones_gestion, a.saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN (
		SELECT p.cod_prov, ISNULL(SUM((CAS