CREATE VIEW [dbo].[documentos_d_v] AS
CREATE VIEW documentos_d_v AS 
SELECT
	d.*,
	c.anulado,
	c.cod_cliente
FROM
	documentos_d d
LEFT JOIN documentos_c c ON d.empresa = c.empresa AND c.punto_venta = d.punto_venta AND d.tipo_docum = c.tipo_docum AND d.nro_documento = c.nro_documento AND d.letra = c.letra

GO
