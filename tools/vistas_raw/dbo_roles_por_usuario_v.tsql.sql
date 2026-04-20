CREATE VIEW [dbo].[roles_por_usuario_v] AS

--View para no hacer un JOIN en el mapper
CREATE VIEW roles_por_usuario_v AS
SELECT rpu.cod_usuario cod_usuario, r.cod_rol cod_rol, r.nombre nombre, r.tipo tipo, r.anulado anulado
FROM roles_por_usuario rpu 
INNER JOIN roles r ON rpu.cod_rol = r.cod_rol 

GO
