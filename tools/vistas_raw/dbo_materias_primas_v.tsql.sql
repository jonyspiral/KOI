CREATE VIEW [dbo].[materias_primas_v] AS

CREATE VIEW materias_primas_v AS
	SELECT mp.*, cmp.denom_color, cmp.abrev_color
	FROM Materias_primas mp
	INNER JOIN Colores_materias_primas cmp ON mp.cod_color = cmp.cod_color
GO
