CREATE VIEW [dbo].[ecomexperts_articulos_update_v] AS
CREATE VIEW dbo.ecomexperts_articulos_update_v
AS
SELECT     TOP 100 PERCENT dbo.colores_por_articulo.ml_reference AS sku, dbo.colores_por_articulo.ml_name AS titulo, '' AS garantia, 21 AS impuesto, 
                      'default' AS precio_titulo1, dbo.colores_por_articulo.mlibre_precio AS precio_valor1, dbo.stock_01_14_20_por_talle_v.cant_1 AS cantidad, 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle AS var_sku, 
                      dbo.articulos.cod_articulo + dbo.colores_por_articulo.cod_color_articulo + dbo.stock_01_14_20_por_talle_v.Talle AS sku_variante, '' AS alt_id, 
                      '' AS ml_item_id, 
                      'SPIRAL SHOES Tienda Online.

*| SKATEBOARDING | SURF | LONGBOARDING | BMX | ZAPATILLAS URBANAS | INDUMENTARIA|*
' + CAST(dbo.colores_por_articulo.ml_description
                       AS VARCHAR(4000)) 
                      + '

Visita nuestra tienda oficial en Mercado Libre:
https://www.mercadolibre.com.ar/tienda/spiral

TALLES CALZADO:
Revisa la guía de talles . Los talles publicados son argentinos. Para un ajuste ideal, mide tu pie apoyando el talón descalzo contra una pared y mide la distancia hasta la punta del dedo más largo. Consulta la equivalencia en centímetros.

PREGUNTAS FRECUENTES:

ENVÍOS:

A Domicilio: Enviamos a todo el país a través de MercadoEnvíos.
A Sucursal: Si retiras en una sucursal del correo, lleva DNI y el código de seguimiento.
Envío FLEX: Entregas en el mismo día si el pedido se realiza antes de las 14 hs. Si es después, llegará al siguiente día hábil (hasta las 22 hs).
AUTENTICIDAD:
Todos los productos son 100% originales. Estás comprando directamente a Spiral Shoes.

POSTVENTA Y CAMBIOS:
Nuestro equipo de Atención al Cliente está disponible de lunes a viernes de 8:30 a 19 hs.
Devoluciones y cambios: ¡Gratis!'
                       AS descripcion, '' AS tags, 0 AS costo_unitario, 'Color' AS atributo2, dbo.colores_por_articulo.denom_color AS variante2, 'Talle' AS atributo1, 
                      dbo.stock_01_14_20_por_talle_v.Talle AS variante1, 1 AS active, 'gold_special' AS var_ml_listing_type_id, '' AS var_ml_category_id, 
                      '' AS var_ml_warranty, 'https://spiralshoes.com/zapatillas/jpg/' + piu.e AS imagen1, 'https://spiralshoes.com/zapatillas/jpg/' + piu.d AS imagen2, 
                      'https://spiralshoes.com/zapatillas/jpg/' + piu.a AS imagen3, 'https://spiralshoes.com/zapatillas/jpg/' + piu.t AS imagen4, 
                      'https://spiralshoes.com/zapatillas/jpg/' + piu.b AS imagen5, 'https://spiralshoes.com/zapatillas/jpg/' + piu.u AS imagen6, 
                      'https://spiralshoes.com/zapatillas/jpg/' + piu.f AS imagen7, 'https://spiralshoes.com/zapatillas/jpg/' + piu.i1 AS imagen8, 
                      'https://spiralshoes.com/zapatillas/jpg/' + piu.i2 AS imagen9, 'https://spiralshoes.com/zapatillas/jpg/' + piu.i3 AS imagen10, 
                      'El producto no tiene código registrado' AS var_motivo_de_gtin_vacío
FROM         dbo.prestashop_imagenes_update piu INNER JOIN
                      dbo.familias_producto INNER JOIN
                      dbo.articulos INNER JOIN
                      dbo.colores_por_articulo ON dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo ON 
                      dbo.familias_producto.id = dbo.articulos.cod_familia_producto INNER JOIN
                      dbo.Marcas ON dbo.articulos.cod_marca = dbo.Marcas.cod_marca INNER JOIN
                      dbo.lineas_productos ON dbo.articulos.cod_linea = dbo.lineas_productos.cod_linea INNER JOIN
                      dbo.stock_01_14_20_por_talle_v ON dbo.colores_por_articulo.cod_articulo = dbo.stock_01_14_20_por_talle_v.cod_articulo AND 
                      dbo.colores_por_articulo.cod_color_articulo = dbo.stock_01_14_20_por_talle_v.cod_color_articulo ON 
                      piu.c
od_articulo = dbo.colores_por_articulo.cod_articulo AND piu.cod_color_articulo = dbo.colores_por_articulo.cod_color_articulo
WHERE     (dbo.stock_01_14_20_por_talle_v.Talle <> 'X') AND (dbo.stock_01_14_20_por_talle_v.Talle IS NOT NULL) AND 
                      (dbo.stock_01_14_20_por_talle_v.Talle <> '') AND (dbo.colores_por_articulo.ecommerce_existe = 'S') AND 
                      (dbo.colores_por_articulo.id_tipo_producto_stock <> '06') AND (dbo.colores_por_articulo.ecommerce_existe = 'S')

GO
