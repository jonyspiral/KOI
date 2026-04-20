CREATE VIEW [dbo].[promedio_tolerancia_pagados] AS

CREATE VIEW promedio_tolerancia_pagados
AS
SELECT 
	casiPagas.documento_fecha,
	casiPagas.cod_cli,
	casiPagas.empresa,
	casiPagas.factura_nro,
	casiPagas.letra_factura,
	promedioHijas.promedio
FROM
(
	SELECT
		facturasNuevas.documento_fecha,
		facturasNuevas.cod_cli,
		facturasNuevas.empresa,
		facturasNuevas.factura_nro,
		facturasNuevas.letra_factura
	FROM
		(SELECT d.documento_fecha, d.cod_cli, d.empresa, d.factura_nro, d.letra_factura, d.total_factura FROM docum_clientes_cabecera d WHERE
			d.documento_fecha > CONVERT(DATETIME, '01/12/2011', 103) AND
			(d.anulada = 'N' OR d.anulada IS NULL) AND
			(d.tipo_docum = 'FAC' OR d.tipo_docum = 'NDB') AND
			d.grado = 'M'
		) facturasNuevas
	LEFT JOIN docum_suma_hijas hijas ON (facturasNuevas.empresa = hijas.empresa AND facturasNuevas.factura_nro = hijas.factura_nro AND facturasNuevas.letra_factura = hijas.letra_factura)
	--WHERE
		--hijas.sumaHijas > (facturasNuevas.total_factura * 0.95)
) casiPagas

LEFT JOIN
(
	SELECT
		a1.empresa empresa,
		a1.factura_nro factura_nro,
		a1.letra_factura letra_factura,
		SUM((DATEDIFF(DAY, a2.documento_fecha, 
			(CASE a1.docum_cancel_tipo WHEN 'NCR' THEN (a2.documento_fecha) WHEN 'REC' THEN (a3.fecha_recibo) ELSE a1.fecha_docum_anulacion END))
			 * (a1.total_factura / a2.total_factura))) promedio
	FROM
		docum_clientes_cabecera a1
	LEFT JOIN
		docum_clientes_cabecera a2 ON (
			a1.empresa = a2.empresa AND a1.factura_nro = a2.factura_nro AND 
			a1.letra_factura = a2.letra_factura AND a2.grado = 'M' AND 
			a1.tipo_docum = a2.tipo_docum AND
			(a2.anulada = 'N' OR a2.anulada IS NULL)
		)
	LEFT JOIN
		recibo_cabecera a3 ON (
			a1.empresa = a3.empresa AND a1.docum_cancel_nro = a3.recibo_nro AND 
			(a3.anulado = 'N' OR a3.anulado IS NULL)
		)
	WHERE
		a1.documento_fecha > CONVERT(DATETIME, '01/12/2011', 103) AND
		a1.grado = 'H' AND 
		(a1.tipo_docum = 'FAC' OR a1.tipo_docum = 'NDB')
	GROUP BY a1.empresa, a1.factura_nro, a1.letra_factura
) promedioHijas
ON
casiPagas.empresa = promedioHijas.empresa AND
casiPagas.factura_nro = promedioHijas.factura_nro AND
casiPagas.letra_factura = promedioHijas.letra_factura

WHERE promedio IS NOT NULL

GO
