CREATE VIEW [dbo].[remitos_c_v] AS
CREATE VIEW remitos_c_v AS 
SELECT
	r.*,
	c.razon_social
FROM
	remitos_c r
INNER JOIN clientes c ON c.cod_cli = r.cod_cliente

GO
