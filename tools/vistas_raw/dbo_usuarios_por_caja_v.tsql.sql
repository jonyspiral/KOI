CREATE VIEW [dbo].[usuarios_por_caja_v] AS



CREATE VIEW [dbo].[usuarios_por_caja_v] AS
	SELECT		c.cod_caja, c.nombre, p.cod_usuario,
				(CASE WHEN (SELECT count(*) FROM cuenta_bancaria cb WHERE cb.cod_caja = c.cod_caja) > 0 THEN 'S' ELSE 'N' END) es_caja_banco
	FROM		usuarios_por_caja p INNER JOIN
				caja c ON p.cod_caja = c.cod_caja
	GROUP BY	c.cod_caja, c.nombre, p.cod_usuario
GO
