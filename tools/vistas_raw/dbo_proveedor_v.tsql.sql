CREATE VIEW [dbo].[proveedor_v] AS


CREATE VIEW [dbo].[proveedor_v] AS
	SELECT p.cod_prov, p.tipo_proveedor, p.fecha_baja, p.vivo, p.razon_social, p.denom_fantasia, p.cuit, p.condicion_iva, p.rubro, p.imputacion_en_compra, p.calle, p.numero, 
		p.piso, p.oficina_depto, p.cod_postal, p.localidad, p.partido_departamento, p.provincia, p.pais, p.provincia_vieja, p.PAIS_viejo, p.telefono_1, p.telefono_2, p.fax, 
		p.e_mail, p.limite_credito, p.cuit_viejo, p.iva_viejo, p.vta_viejo, p.plazo_pago, p.vendedor, p.retener_imp_ganancias, p.concepto_reten_ganancias, p.retener_iva, 
		p.retener_ingr_brutos, p.jurisd_1_ingr_brutos, p.jurisd_2_ingr_brutos, p.retencion_especial, p.cuenta_acumuladora, p.denominacion_cta_acum, 
		p.FECHA_ULTima_modificacion, p.autor_ultima_modificacion, p.anulado, p.margen, p.pagina_web, p.horarios_atencion, p.persona_en_fca, p.lista_precios_imprime, 
		p.NombreContacto, p.CargoContacto, p.NumTeléfono3, p.NumCelular, p.Notas, p.DireccionComercial, p.plazo_pago_primera_entrega, p.primera_entrega, 
		p.codigo_sist_anterior, p.r_social_en_doc, p.cod_transporte, p.lugar_de_retiro, p.horario_de_retiro, p.observaciones, p.plazo_pago_real, p.comentario, 
		p.cod_localidad, p.cod_localidad_nro, ISNULL(ia.importe_acumulado_mes, 0) importe_acumulado_mes, ISNULL(ia.importe_retenido_mes, 0) importe_retenido_mes,
		p.imputacion_general,p.imputacion_especifica, p.cod_imputacion_haber, p.autorizado,
		gp1.saldo saldo_1, gp2.saldo saldo_2, (gp1.saldo + gp2.saldo) saldo
	FROM dbo.proveedores_datos AS p 
	LEFT JOIN dbo.importes_op_acumulado_mes AS ia ON p.cod_prov = ia.cod_proveedor
	LEFT JOIN gestion_proveedores_1 AS gp1 ON p.cod_prov = gp1.cod_prov
	LEFT JOIN gestion_proveedores_2 AS gp2 ON p.cod_prov = gp2.cod_prov



GO
