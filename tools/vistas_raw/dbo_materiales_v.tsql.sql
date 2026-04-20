CREATE VIEW [dbo].[materiales_v] AS
CREATE VIEW materiales_v AS
	SELECT m.*, a.cod_articulo, a.naturaleza
	FROM materiales m
	LEFT OUTER JOIN articulos a ON m.cod_material = a.cod_material

GO
