CREATE VIEW [dbo].[usuarios_por_tipo_notificacion_v] AS

CREATE VIEW usuarios_por_tipo_notificacion_v AS

SELECT a.cod_tipo_notificacion, a.eliminable, b.*
FROM usuarios_por_tipo_notificacion a
INNER JOIN users b ON a.cod_usuario = b.cod_usuario
GO
