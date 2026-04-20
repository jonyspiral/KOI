CREATE VIEW [dbo].[indicadores_por_rol_v] AS

CREATE VIEW indicadores_por_rol_v AS
	SELECT TOP 100 PERCENT ixr.cod_indicador, i.nombre nombre_indicador, r.*
	FROM indicadores_por_rol ixr
	INNER JOIN roles r ON ixr.cod_rol = r.cod_rol
	INNER JOIN indicadores i ON i.cod_indicador = ixr.cod_indicador
	ORDER BY nombre_indicador ASC, ixr.cod_indicador ASC


GO
