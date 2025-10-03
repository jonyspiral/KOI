
CREATE VIEW presentismo AS

SELECT TOP 100 PERCENT t1.fecha, t1.legajo_nro, (CASE WHEN t2.legajo_nro IS NULL THEN 'AUSENTE' ELSE 'PRESENTE' END) presencia
FROM (
	SELECT dias.*, p.legajo_nro
	FROM (
		SELECT fecha
		FROM registro_entradas_salidas