CREATE VIEW [dbo].[documentos_vendedor] AS

CREATE VIEW [dbo].[documentos_vendedor] AS
	SELECT
			d.*, c.cod_vendedor, c.razon_social
	FROM documentos d
	LEFT JOIN clientes c ON d.cod_cliente = c.cod_cli

GO
