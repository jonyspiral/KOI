CREATE VIEW [dbo].[proveedores_materias_primas_v] AS
CREATE VIEW proveedores_materias_primas_v AS
	SELECT pmp.*, m.denom_material
	FROM proveedores_materias_primas pmp
	INNER JOIN materiales m ON m.cod_material = pmp.cod_material


GO
