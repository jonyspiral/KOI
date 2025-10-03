CREATE VIEW [dbo].[ficha_tecnica_patrones_d] AS
CREATE VIEW dbo.ficha_tecnica_patrones_d
AS
SELECT     p.cod_articulo, p.cod_color_articulo, p.version, p.nro_item, p.cod_pieza, p.cod_material, p.cod_color_material, p.cod_seccion, p.fecha_alta, p.item_nuevo, 
                      p.consumo_par, p.consumo_batch, p.sckrap_batch, p.sckrap_porcentual, p.conjunto, p.varia, p.escalado, p.escala_desplazada, p.tipo_patron, p.trazable, 
                      p.asignado_lote, p.cant_entregada, p.entregado, m.unidad_medida AS ums, m.denom_material AS denominacion_material, c.denom_conjunto, p.cod_temporada
FROM         dbo.Patrones_mp_detalle AS p LEFT OUTER JOIN
                      dbo.materiales AS m ON p.cod_material = m.cod_material LEFT OUTER JOIN
                      dbo.conjuntos AS c ON p.conjunto = c.conjunto

GO
