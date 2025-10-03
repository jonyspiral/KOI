
CREATE VIEW fichajes_v AS
SELECT
	r.clave_tabla, r.legajo_nro, r.movimiento_tipo, r.fecha, r.entrada_horario, r.salida_horario, r.con_anomalias, r.ubicacion_tipo, r.ubicacion_confirmada,
	r.diferencia_entrada, r.diferencia_salida, p.cod_personal, p.s