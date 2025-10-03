CREATE VIEW [dbo].[caja_v] AS

CREATE VIEW caja_v AS
	SELECT		c.cod_caja, c.cod_duenio, c.cod_caja_padre, c.nombre, c.fecha_limite, c.dias_cierre, ISNULL(c.importe_efectivo, 0) AS importe_efectivo, c.importe_descubierto, 
				c.importe_maximo, c.cod_imputacion, c.caja_banco, c.anulado, c.fecha_alta, c.fecha_baja, c.fecha_ultima_mod, SUM(ISNULL(g.importe, 0)) AS importe_gastitos, c.disp_para_negociar
	FROM		dbo.caja AS c LEFT OUTER JOIN
				dbo.gastito AS g ON c.cod_caja = g.cod_caja AND g.cod_rendicion_gastos IS NULL
	GROUP BY	c.cod_caja, c.cod_duenio, c.cod_caja_padre, c.nombre, c.fecha_limite, c.dias_cierre, c.importe_efectivo, c.importe_descubierto, c.importe_maximo, c.cod_imputacion,
				c.caja_banco, c.anulado, c.fecha_alta, c.fecha_baja, c.fecha_ultima_mod, c.disp_para_negociar
GO
