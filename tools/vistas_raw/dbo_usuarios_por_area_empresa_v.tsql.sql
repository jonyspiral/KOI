CREATE VIEW [dbo].[usuarios_por_area_empresa_v] AS

CREATE VIEW usuarios_por_area_empresa_v AS
	SELECT a.id_area_empresa, b.*
	FROM usuarios_por_area_empresa a
	INNER JOIN users b ON a.cod_usuario = b.cod_usuario
GO
