
CREATE VIEW mundial AS
SELECT j.id, j.nombre, ISNULL(puntaje, 0) puntaje FROM (
	SELECT
		j.id jugador,
		SUM((CASE WHEN pj.goles_1 = p.goles_1 AND pj.goles_2 = p.goles_2 THEN
			3
		ELSE
			(CASE WHEN (pj.goles_1 > pj.goles_2 AND p.goles_1 > p.g