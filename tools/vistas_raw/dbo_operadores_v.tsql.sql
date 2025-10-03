CREATE VIEW [dbo].[operadores_v] AS

CREATE VIEW operadores_v AS
	SELECT		p.cod_personal, p.legajo_nro, p.cod_categoria, p.apellido, p.nombres, p.fecha_ultima_modificacion, p.autor_ultima_modificacion, p.anulado, p.funcion, p.cuil, 
				p.fecha_antiguedad_gremio, p.fecha_ingreso, p.centro_costos, p.remun_bruta_mes, p.remun_bruta_hora, p.retrib_unidad_producida, p.costo_rem_y_cs_mes, 
				p.costo_rem_y_cs_hora, p.obra_social, p.produccion_unid_mes, p.produccion_unid_hora, p.restricciones_funcionales, p.inasistencias_acumuladas, 
				p.llegadas_tarde_acumuladas, p.fecha_egreso, p.calle, p.numero, p.piso, p.departamento, p.localidad, p.fecha_nacimiento, p.partido_departamento, p.cod_postal, 
				p.doc_identidad_tipo, p.doc_identidad_nro, p.tel_domicilio, p.tel_celular, p.e_mail, p.fax_domicilio, p.codigo_sist_anterior, p.sanciones_veces, p.provincia, 
				p.casillero_nro, p.funcion_time, p.seccion, p.ingreso_fecha, p.baja_fecha, p.retribucion_modalidad, p.valor_pares, p.valor_hora, p.valor_hora_1, p.valor_quincena, 
				p.valor_mes, p.valor_mes_1, p.dni, p.calle_transv_1, p.calle_transv_2, p.fotografia, p.situacion, p.asignar, p.liquidar_feriados, p.marca_tarjeta, 
				p.valor_hora_merienda, p.fecha_nacimiento1, p.categoria_convenio, p.faja_horaria, p.tarea_1, p.tarea_2, p.cod_faja_horaria, p.tarjeta_impresa, p.objetivo_1, 
				p.objetivo_2, p.objetivo_3, p.premio_1, p.premio_2, p.premio_3, p.cod_pais, p.cod_localidad, p.cod_localidad_nro, p.ficha, o.cod_operador, o.tipo_operador,
				o.comision_variable, o.porc_comision_vtas
	FROM		dbo.Operadores AS o
				INNER JOIN dbo.personal AS p ON o.cod_personal = p.cod_personal

GO
