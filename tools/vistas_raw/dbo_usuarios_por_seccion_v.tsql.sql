CREATE VIEW [dbo].[usuarios_por_seccion_v] AS

CREATE VIEW [dbo].[usuarios_por_seccion_v] AS
SELECT a.cod_seccion, b.*
FROM usuarios_por_seccion a
INNER JOIN users b ON a.cod_usuario = b.cod_usuario
GO
