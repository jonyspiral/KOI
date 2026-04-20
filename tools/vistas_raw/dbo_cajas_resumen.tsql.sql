CREATE VIEW [dbo].[cajas_resumen] AS

CREATE VIEW cajas_resumen AS 

SELECT 
	fecha,
	(
		CASE
		WHEN (SELECT 1 FROM caja_saldos_diarios WHERE caja_fecha = fecha) = 1 THEN 'S'
		ELSE 'N'
		END
	) caja_cerrada,
	SUM(cd_importe_1) cd_importe_1,
	SUM(cd_importe_2) cd_importe_2,
	SUM(cd_importe_3) cd_importe_3,
	SUM(cd_importe_4) cd_importe_4,
	SUM(cd_importe_5) cd_importe_5,
	SUM(cd_importe_6) cd_importe_6,
	SUM(oi_importe_1) oi_importe_1,
	SUM(oi_importe_2) oi_importe_2,
	SUM(oi_importe_3) oi_importe_3,
	SUM(oi_importe_4) oi_importe_4,
	SUM(oi_importe_5) oi_importe_5,
	SUM(oi_importe_6) oi_importe_6,
	SUM(db_importe_1) db_importe_1,
	SUM(db_importe_2) db_importe_2,
	SUM(op_importe_1) op_importe_1,
	SUM(op_importe_2) op_importe_2,
	SUM(gr_importe_1) gr_importe_1,
	SUM(cc_importe_1) cc_importe_1,
	SUM(cc_importe_2) cc_importe_2,
	SUM(cc_importe_3) cc_importe_3,
	SUM(cc_importe_4) cc_importe_4,
	SUM(cc_importe_5) cc_importe_5,
	SUM(cc_importe_6) cc_importe_6,
	SUM(cc_importe_total) cc_importe_total
FROM
(
	--Cobranza Deudores (los CD de documentos)
	SELECT
		fecha_caja fecha,
		COALESCE(SUM(do.importe_1), 0) cd_importe_1,
		COALESCE(SUM(do.importe_2), 0) cd_importe_2,
		COALESCE(SUM(do.importe_3), 0) cd_importe_3,
		COALESCE(SUM(do.importe_4), 0) cd_importe_4,
		COALESCE(SUM(do.importe_5), 0) cd_importe_5,
		COALESCE(SUM(do.importe_6), 0) cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM documentos do
	WHERE
		fecha_caja IS NOT NULL AND
		tipo_docum = 'REC'	AND
		operacion_tipo = 'CD'
	GROUP BY fecha_caja

	UNION

	--Otros Ingresos (los OI de documentos)
	SELECT
		fecha_caja fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		COALESCE(SUM(do2.importe_1), 0) oi_importe_1,
		COALESCE(SUM(do2.importe_2), 0) oi_importe_2,
		COALESCE(SUM(do2.importe_3), 0) oi_importe_3,
		COALESCE(SUM(do2.importe_4), 0) oi_importe_4,
		COALESCE(SUM(do2.importe_5), 0) oi_importe_5,
		COALESCE(SUM(do2.importe_6), 0) oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM documentos do2
	WHERE
		fecha_caja IS NOT NULL AND
		tipo_docum = 'REC'	AND
		operacion_tipo = 'OI'
	GROUP BY fecha_caja

	UNION

	--Depósitos Bancarios Efectivo (los BD y E de bancos_depositos_cabecera)
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		COALESCE(SUM(ba1.total_boleta_deposito), 0) db_importe_1,
		0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM bancos_depositos_cabecera ba1
	WHERE
		caja_fecha IS NOT NULL AND
		tipo_docum_u_operacion = 'BD' AND
		tipo_valor = 'E'
	GROUP BY caja_fecha

	UNION

	--Depósitos Bancarios Cheques (los BD y C de bancos_depositos_cabecera)
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1,
		COALESCE(SUM(ba2.total_boleta_deposito), 0) db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM bancos_depositos_cabecera ba2
	WHERE
		caja_fecha IS NOT NULL AND
		ti
po_docum_u_operacion = 'BD' AND
		tipo_valor = 'C'
	GROUP BY caja_fecha

	UNION

	--Órdenes de pago
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		COALESCE(SUM(op.imp_entreg_en_valor_1), 0) op_importe_1,
		COALESCE(SUM(op.imp_entreg_en_valor_2), 0) op_importe_2,
		0 gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM ordenes_de_pago op
	WHERE
		caja_fecha IS NOT NULL AND
		operacion_tipo = 'OP'
	GROUP BY caja_fecha

	UNION

	--Gastos por Rendición varios (de gastos_rendicion)
	SELECT
		fecha_rendicion fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		COALESCE(SUM(gr.total_importe), 0) gr_importe_1,
		0 cc_importe_1, 0 cc_importe_2, 0 cc_importe_3, 0 cc_importe_4, 0 cc_importe_5, 0 cc_importe_6, 0 cc_importe_total
	FROM gastos_rendicion gr
	WHERE
		fecha_rendicion IS NOT NULL
	GROUP BY fecha_rendicion

	UNION

	--Cajas Cerradas
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0 oi_importe_2, 0 oi_importe_3, 0 oi_importe_4, 0 oi_importe_5, 0 oi_importe_6,
		0 db_importe_1, 0 db_importe_2,
		0 op_importe_1, 0 op_importe_2,
		0 gr_importe_1,
		cc.importe_recibido_en_valor_1 cc_importe_1,
		cc.importe_recibido_en_valor_2 cc_importe_2,
		cc.importe_recibido_en_valor_3 cc_importe_3,
		cc.importe_recibido_en_valor_4 cc_importe_4,
		cc.importe_recibido_en_valor_5 cc_importe_5,
		cc.importe_recibido_en_valor_6 cc_importe_6,
		ISNULL(cc.importe_recibido_en_valor_1, 0) + 
			ISNULL(cc.importe_recibido_en_valor_2, 0) + 
			ISNULL(cc.importe_recibido_en_valor_3, 0) + 
			ISNULL(cc.importe_recibido_en_valor_4, 0) + 
			ISNULL(cc.importe_recibido_en_valor_5, 0) + 
			ISNULL(cc.importe_recibido_en_valor_6, 0)
			cc_importe_total
	FROM caja_saldos_diarios cc
	WHERE
		caja_fecha IS NOT NULL

) a
GROUP BY fecha
GO
