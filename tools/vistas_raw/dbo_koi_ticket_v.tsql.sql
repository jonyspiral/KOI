CREATE VIEW [dbo].[koi_ticket_v] AS

CREATE VIEW koi_ticket_v AS
	SELECT TOP 100 PERCENT * FROM (
		-- 1. Los que tienen fecha de resolución (con o sin responsable)
		SELECT *, 1 orden1, fecha_estimada_resolucion orden2, NULL orden3 FROM koi_ticket
		WHERE anulado = 'N' AND fecha_estimada_resolucion IS NOT NULL AND fecha_cierre IS NULL
	UNION ALL
		-- 2. Los que tienen sólo responsable
		SELECT *, 2 orden1, fecha_estimada_resolucion orden2, NULL orden3 FROM koi_ticket
		WHERE anulado = 'N' AND fecha_estimada_resolucion IS NULL AND cod_responsable IS NOT NULL AND fecha_cierre IS NULL
	UNION ALL
		-- 3. Los que tienen no tienen fecha de resolución ni responsable
		SELECT *, 3 orden1, NULL orden2, prioridad orden3 FROM koi_ticket
		WHERE anulado = 'N' AND (fecha_estimada_resolucion IS NULL AND cod_responsable IS NULL) AND fecha_cierre IS NULL
	UNION ALL
		-- 4. Los tickets cerrados
		SELECT *, 4 orden1, NULL orden2, fecha_cierre orden3 FROM koi_ticket
		WHERE anulado = 'N' AND fecha_cierre IS NOT NULL
	) a
	ORDER BY orden1 ASC, orden2 ASC, orden3 DESC
GO
