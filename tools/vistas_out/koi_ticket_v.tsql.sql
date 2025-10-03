
CREATE VIEW koi_ticket_v AS
	SELECT TOP 100 PERCENT * FROM (
		-- 1. Los que tienen fecha de resolución (con o sin responsable)
		SELECT *, 1 orden1, fecha_estimada_resolucion orden2, NULL orden3 FROM koi_ticket
		WHERE anulado = 'N' AND fecha_estim