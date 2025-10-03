CREATE VIEW `almacenes_por_seccion_v` AS

CREATE VIEW almacenes_por_seccion_v AS
SELECT a.cod_seccion, b.*
FROM almacenes_por_seccion a
INNER JOIN almacenes b ON a.cod_almacen = b.cod_almacen;
