
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