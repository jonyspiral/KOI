
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
	SUpo_docum_u_operacion = 'BD' AND
		tipo_valor = 'C'
	GROUP BY caja_fecha

	UNION

	--Órdenes de pago
	SELECT
		caja_fecha fecha,
		0 cd_importe_1, 0 cd_importe_2, 0 cd_importe_3, 0 cd_importe_4, 0 cd_importe_5, 0 cd_importe_6,
		0 oi_importe_1, 0