CREATE VIEW [dbo].[prestashop_imagenes_update] AS
CREATE VIEW dbo.prestashop_imagenes_update
AS
SELECT     cpa.cod_articulo, cpa.cod_color_articulo,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'e') AS e,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'd') AS d,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'a') AS a,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 't') AS t,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'b') AS b,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'u') AS u,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'f') AS f,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'i1') AS i1,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'i2') AS i2,
                          (SELECT     imagen
                            FROM          dbo.articulos_imagenes AS ai
                            WHERE      cpa.cod_articulo = ai.articulo AND cpa.cod_color_articulo = ai.codigo_color AND lado_imagen = 'i3') AS i3
FROM         dbo.colores_por_articulo cpa INNER JOIN
                      dbo.articulos ON cpa.cod_articulo = dbo.articulos.cod_articulo
WHERE     (cpa.ecommerce_existe = 'S')

GO
