CREATE VIEW [dbo].[cajas_posibles_transferencia_interna_v] AS

CREATE VIEW [dbo].[cajas_posibles_transferencia_interna_v] AS
	SELECT cpti.*, c1.nombre nombre_caja_salida, c2.nombre nombre_caja_entrada
	FROM cajas_posibles_transferencia_interna cpti
	INNER JOIN caja c1 ON cpti.cod_caja_salida = c1.cod_caja
	INNER JOIN caja c2 ON cpti.cod_caja_entrada = c2.cod_caja
GO
