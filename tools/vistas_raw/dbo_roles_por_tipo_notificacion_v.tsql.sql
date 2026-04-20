CREATE VIEW [dbo].[roles_por_tipo_notificacion_v] AS

CREATE VIEW roles_por_tipo_notificacion_v AS
SELECT a.cod_tipo_notificacion, a.eliminable, b.*
FROM roles_por_tipo_notificacion a
INNER JOIN roles b ON a.cod_rol = b.cod_rol
GO
