CREATE VIEW [dbo].[listado_proveedores_v] AS

CREATE VIEW dbo.listado_proveedores_v AS
	SELECT		p.*, pr.denom_provincia, l.denom_localidad
	FROM		proveedores_v p
				LEFT OUTER JOIN provincias pr ON pr.cod_provincia = p.provincia
				LEFT OUTER JOIN localidades l ON l.cod_localidad = p.localidad
	WHERE		p.anulado = 'N'
GO
