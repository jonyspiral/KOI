CREATE VIEW [dbo].[facturas_detalle] AS

CREATE  VIEW dbo.facturas_detalle
AS
SELECT     TOP 100 PERCENT dbo.Despachos_detalle.nro_despacho_nro AS nro_despacho, dbo.Despachos_detalle.nro_item_despacho AS nro_item, 
                      dbo.Despachos_detalle.cod_empresa_despacho AS empresa, dbo.Despachos_cabecera.cod_cli AS cod_cliente, 
                      dbo.Despachos_cabecera.cod_suc AS cod_sucursal, dbo.Despachos_detalle.nro_pedido_nro AS nro_pedido, dbo.Despachos_detalle.anulado, 
                      dbo.Despachos_detalle.pendiente, dbo.remitos_c.nro_remito, dbo.remitos_c.letra AS letra_remito, 
                      dbo.Despachos_detalle.fecha_ultima_modificacion AS fecha_alta, dbo.Despachos_detalle.cod_almacen, dbo.Despachos_detalle.cod_articulo, 
                      dbo.Despachos_detalle.cod_color, 
                      CASE lista_aplicable WHEN 'D' THEN (CASE precio_al_facturar WHEN 'N' THEN Precio_unitario ELSE precio_distrib END) 
                      ELSE (CASE precio_al_facturar WHEN 'N' THEN Precio_unitario ELSE precio_mayorista_usd END) END AS precio, dbo.remitos_c.nro_factura, 
                      dbo.remitos_c.letra_factura, ISNULL(dbo.Despachos_detalle.cant_1, 0) + ISNULL(dbo.Despachos_detalle.cant_2, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_3, 0) + ISNULL(dbo.Despachos_detalle.cant_4, 0) + ISNULL(dbo.Despachos_detalle.cant_5, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_6, 0) + ISNULL(dbo.Despachos_detalle.cant_7, 0) + ISNULL(dbo.Despachos_detalle.cant_8, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_9, 0) + ISNULL(dbo.Despachos_detalle.cant_10, 0) AS Cantidad, dbo.Despachos_detalle.precio_unitario, 
                      (ISNULL(dbo.Despachos_detalle.cant_1, 0) + ISNULL(dbo.Despachos_detalle.cant_2, 0) + ISNULL(dbo.Despachos_detalle.cant_3, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_4, 0) + ISNULL(dbo.Despachos_detalle.cant_5, 0) + ISNULL(dbo.Despachos_detalle.cant_6, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_7, 0) + ISNULL(dbo.Despachos_detalle.cant_8, 0) + ISNULL(dbo.Despachos_detalle.cant_9, 0) 
                      + ISNULL(dbo.Despachos_detalle.cant_10, 0)) 
                      * (CASE lista_aplicable WHEN 'D' THEN (CASE precio_al_facturar WHEN 'N' THEN Precio_unitario ELSE precio_distrib END) 
                      ELSE (CASE precio_al_facturar WHEN 'N' THEN Precio_unitario ELSE precio_mayorista_usd END) END) AS importe_total, 
                      dbo.Despachos_detalle.precio_al_facturar, dbo.colores_por_articulo.precio_distrib, dbo.colores_por_articulo.precio_mayorista_usd, 
                      dbo.Clientes.lista_aplicable, dbo.Despachos_detalle.cant_1, dbo.Despachos_detalle.cant_2, dbo.Despachos_detalle.cant_3, 
                      dbo.Despachos_detalle.cant_4, dbo.Despachos_detalle.cant_5, dbo.Despachos_detalle.cant_6, dbo.Despachos_detalle.cant_7, 
                      dbo.Despachos_detalle.cant_8, dbo.Despachos_detalle.cant_9, dbo.Despachos_detalle.cant_10
FROM         dbo.Clientes INNER JOIN
                      dbo.Despachos_cabecera ON dbo.Clientes.cod_cli = dbo.Despachos_cabecera.cod_cli RIGHT OUTER JOIN
                      dbo.Despachos_detalle INNER JOIN
                      dbo.colores_por_articulo ON dbo.Despachos_detalle.cod_articulo = dbo.colores_por_articulo.cod_articulo AND 
                      dbo.Despachos_detalle.cod_color = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.Despachos_cabecera.nro_despacho = dbo.Despachos_detalle.nro_despacho AND 
                      dbo.Despachos_cabecera.cod_sucursal_despacho = dbo.Despachos_detalle.cod_sucursal_despacho AND 
                      dbo.Despachos_cabecera.cod_empresa_despacho = dbo.Despachos_detalle.cod_empresa_despacho LEFT OUTER JOIN
                      dbo.remitos_c ON dbo.Despachos_detalle.nro_remito = dbo.remitos_c.nro_remito AND dbo.Despachos_detalle.letra_remito = dbo.remitos_c.letra AND 

                      dbo.Despachos_detalle.cod_empresa_despacho = dbo.remitos_c.empresa
WHERE     (dbo.Despachos_detalle.anulado = 'N')


GO
