CREATE VIEW [dbo].[notificaciones_por_usuario_v] AS
--Es porque la clase NotificacionPorUsuario Usuario y necesita todos sus campos para el fill
--Y además es para que aparezca la fecha y así poder filtrar en el getListObject

CREATE VIEW notificaciones_por_usuario_v AS
SELECT
	a.cod_notificacion, a.vista, a.anulado, a.eliminable, CONVERT(CHAR(19), a.fecha_ultima_mod, 120) fecha_ultima_mod,
	c.cod_usuario, c.tipo, c.cod_personal, c.cod_contacto, c.fechaAlta, c.fechaBaja, c.fechaUltimaAct, c.fechaUltimaMod
FROM notificaciones_por_usuario a
INNER JOIN users c ON a.cod_usuario = c.cod_usuario
GO
