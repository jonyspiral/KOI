CREATE VIEW [dbo].[banco_propio_v] AS

CREATE VIEW [dbo].[banco_propio_v] AS
	SELECT b.nombre, bp.*
	FROM banco b
	INNER JOIN banco_propio bp ON b.cod_banco = bp.cod_banco
GO
