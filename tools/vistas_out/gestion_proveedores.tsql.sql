
CREATE VIEW gestion_proveedores AS
	SELECT p.cod_prov, p.razon_social, p.cuit, p.imputacion_especifica, i.denominacion, p.anulado, p.observaciones_gestion, a.saldo, total_cheques
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN (
		SELECT p.cod_prov, IS