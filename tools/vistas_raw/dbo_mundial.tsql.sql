CREATE VIEW [dbo].[mundial] AS

CREATE VIEW mundial AS
SELECT j.id, j.nombre, ISNULL(puntaje, 0) puntaje FROM (
	SELECT
		j.id jugador,
		SUM((CASE WHEN pj.goles_1 = p.goles_1 AND pj.goles_2 = p.goles_2 THEN
			3
		ELSE
			(CASE WHEN (pj.goles_1 > pj.goles_2 AND p.goles_1 > p.goles_2) OR (pj.goles_1 < pj.goles_2 AND p.goles_1 < p.goles_2) OR (pj.goles_1 = pj.goles_2 AND p.goles_1 = p.goles_2) THEN
				2
			ELSE
				0
			END)
		END)) puntaje
	FROM mundial_partidos_jugador pj
	LEFT JOIN mundial_partidos p ON pj.id_partido = p.id
	LEFT JOIN mundial_jugadores j ON pj.id_jugador = j.id
	GROUP BY j.id
) a
RIGHT JOIN mundial_jugadores j ON a.jugador = j.id
GO
