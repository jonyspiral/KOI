CREATE VIEW [dbo].[mundial_detalle] AS

CREATE VIEW mundial_detalle AS
SELECT 
	pj.id_jugador, j.nombre nombre_jugador, p.id id_partido, p.nombre nombre_partido, pj.goles_1, pj.goles_2,
	(CASE WHEN p.goles_1 IS NOT NULL AND p.goles_2 IS NOT NULL THEN 1 ELSE 0 END) jugado,
	(CASE WHEN pj.goles_1 = p.goles_1 AND pj.goles_2 = p.goles_2 THEN
		3
	ELSE
		(CASE WHEN (pj.goles_1 > pj.goles_2 AND p.goles_1 > p.goles_2) OR (pj.goles_1 < pj.goles_2 AND p.goles_1 < p.goles_2) OR (pj.goles_1 = pj.goles_2 AND p.goles_1 = p.goles_2) THEN
			2
		ELSE
			0
		END)
	END) puntaje
FROM mundial_partidos_jugador pj
LEFT JOIN mundial_partidos p ON pj.id_partido = p.id
LEFT JOIN mundial_jugadores j ON pj.id_jugador = j.id
GO
