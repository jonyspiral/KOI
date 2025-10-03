
CREATE VIEW mundial_detalle AS
SELECT 
	pj.id_jugador, j.nombre nombre_jugador, p.id id_partido, p.nombre nombre_partido, pj.goles_1, pj.goles_2,
	(CASE WHEN p.goles_1 IS NOT NULL AND p.goles_2 IS NOT NULL THEN 1 ELSE 0 END) jugado,
	(CASE WHEN pj.g