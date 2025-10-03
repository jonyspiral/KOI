CREATE VIEW [dbo].[pedidos_clientes_articulo_vendedor] AS
CREATE VIEW dbo.pedidos_clientes_articulo_vendedor
AS
SELECT     TOP 100 PERCENT dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, dbo.pedidos_d.pendiente, dbo.pedidos_d.cantidad AS Pedido, 
                      dbo.pedidos_d.pend_1 AS P1, dbo.pedidos_d.pend_2 AS P2, dbo.pedidos_d.pend_3 AS P3, dbo.pedidos_d.pend_4 AS P4, 
                      dbo.pedidos_d.pend_5 AS P5, dbo.pedidos_d.pend_6 AS P6, dbo.pedidos_d.pend_7 AS P7, dbo.pedidos_d.pend_8 AS P8, 
                      dbo.pedidos_d.pend_9 AS P9, dbo.pedidos_d.pend_10 AS P10, dbo.Clientes.cod_cli, dbo.personal.apellido + N',' + LEFT(dbo.personal.nombres, 1) 
                      AS vendedor, dbo.Clientes.Situacion, dbo.Clientes.razon_social, dbo.Clientes.denom_fantasia, dbo.Clientes.cuit, dbo.pedidos_c.nro_pedido, 
                      dbo.pedidos_c.fecha_alta, dbo.Clientes.cod_localidad_nro, dbo.Clientes.localidad, dbo.pedidos_c.anulado AS anulado_c, 
                      dbo.pedidos_d.anulado AS anulado_d, dbo.pedidos_c.id_estado_pedido, dbo.Clientes.cod_calificacion
FROM         dbo.personal RIGHT OUTER JOIN
                      dbo.pedidos_c INNER JOIN
                      dbo.Clientes ON dbo.pedidos_c.cod_cliente = dbo.Clientes.cod_cli INNER JOIN
                      dbo.pedidos_d ON dbo.pedidos_c.empresa = dbo.pedidos_d.empresa AND dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido LEFT OUTER JOIN
                      dbo.Operadores ON dbo.pedidos_c.cod_vendedor = dbo.Operadores.cod_operador ON 
                      dbo.personal.cod_personal = dbo.Operadores.cod_personal
WHERE     (dbo.pedidos_c.fecha_alta > CONVERT(DATETIME, '2016-01-01 00:00:00', 102)) AND (dbo.pedidos_d.anulado = 'N') AND (dbo.pedidos_c.anulado = 'N')
ORDER BY dbo.pedidos_c.fecha_alta, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo

GO
