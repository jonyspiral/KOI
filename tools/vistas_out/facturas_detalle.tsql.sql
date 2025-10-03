
CREATE  VIEW dbo.facturas_detalle
AS
SELECT     TOP 100 PERCENT dbo.Despachos_detalle.nro_despacho_nro AS nro_despacho, dbo.Despachos_detalle.nro_item_despacho AS nro_item, 
                      dbo.Despachos_detalle.cod_empresa_despacho AS empresa, 
                      dbo.Despachos_detalle.cod_empresa_despacho = dbo.remitos_c.empresa
WHERE     (dbo.Despachos_detalle.anulado = 'N')

