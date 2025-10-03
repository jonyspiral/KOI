CREATE VIEW [dbo].[presentismo] AS

CREATE VIEW presentismo AS

SELECT TOP 100 PERCENT t1.fecha, t1.legajo_nro, (CASE WHEN t2.legajo_nro IS NULL THEN 'AUSENTE' ELSE 'PRESENTE' END) presencia
FROM (
	SELECT dias.*, p.legajo_nro
	FROM (
		SELECT fecha
		FROM registro_entradas_salidas
		WHERE fecha IS NOT NULL AND fecha > dbo.toDate('01/01/2013')
		GROUP BY fecha
	) dias
	JOIN (
		SELECT legajo_nro
		FROM personal
		WHERE anulado = 'N' AND legajo_nro IS NOT NULL
	) p ON 1 = 1
) t1
LEFT JOIN registro_entradas_salidas t2
	ON t1.fecha = t2.fecha AND t1.legajo_nro = t2.legajo_nro
ORDER BY t1.fecha
GO
