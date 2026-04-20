CREATE VIEW [dbo].[permisos_por_usuarios_por_caja_v] AS
CREATE VIEW [dbo].[permisos_por_usuarios_por_caja_v] AS
	SELECT		c.cod_caja, c.nombre, c.anulado, p.cod_usuario,p.cod_permiso,
				(CASE WHEN (SELECT count(*) FROM cuenta_bancaria cb WHERE cb.cod_caja = c.cod_caja) > 0 THEN 'S' ELSE 'N' END) es_caja_banco
	FROM		permisos_por_usuarios_por_caja p
	INNER JOIN	caja c ON p.cod_caja = c.cod_caja
GO
