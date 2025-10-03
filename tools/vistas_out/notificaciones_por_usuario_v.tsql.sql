--Es porque la clase NotificacionPorUsuario Usuario y necesita todos sus campos para el fill
--Y además es para que aparezca la fecha y así poder filtrar en el getListObject

CREATE VIEW notificaciones_por_usuario_v AS
SELECT
	a.cod_notificacion, a.v