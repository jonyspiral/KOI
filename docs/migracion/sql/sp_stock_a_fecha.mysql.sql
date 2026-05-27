DROP PROCEDURE IF EXISTS sp_stock_a_fecha;

DELIMITER $$

CREATE PROCEDURE sp_stock_a_fecha(
    IN p_codAlmacen VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
    IN p_codArticulo VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
    IN p_codColor VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
    IN p_codTipo VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
    IN p_fecha VARCHAR(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
)
BEGIN
    DECLARE v_fecha DATETIME;

    IF p_fecha LIKE '%-%' THEN
        IF p_fecha LIKE '%:%' THEN
            SET v_fecha = STR_TO_DATE(p_fecha, '%Y-%m-%d %H:%i:%s');
        ELSE
            SET v_fecha = STR_TO_DATE(p_fecha, '%Y-%m-%d');
        END IF;
    ELSEIF p_fecha LIKE '%/%' THEN
        IF p_fecha LIKE '%:%' THEN
            SET v_fecha = STR_TO_DATE(p_fecha, '%d/%m/%Y %H:%i:%s');
        ELSE
            SET v_fecha = STR_TO_DATE(p_fecha, '%d/%m/%Y');
        END IF;
    ELSE
        SET v_fecha = NOW();
    END IF;

    SELECT
        s.cod_almacen,
        s.cod_articulo,
        s.cod_color_articulo,
        SUM(s.cantidad) AS cantidad,
        a.denom_articulo AS nombre_articulo,
        cxa.denom_color AS nombre_color,
        a.cod_rango AS cod_rango,
        tps.denom_tipo_producto AS tipo,
        SUM(s.cant_1) AS cant_1,
        SUM(s.cant_2) AS cant_2,
        SUM(s.cant_3) AS cant_3,
        SUM(s.cant_4) AS cant_4,
        SUM(s.cant_5) AS cant_5,
        SUM(s.cant_6) AS cant_6,
        SUM(s.cant_7) AS cant_7,
        SUM(s.cant_8) AS cant_8,
        SUM(s.cant_9) AS cant_9,
        SUM(s.cant_10) AS cant_10
    FROM (
        SELECT
            cod_almacen,
            cod_articulo,
            cod_color_articulo,
            cantidad,
            cant_1,
            cant_2,
            cant_3,
            cant_4,
            cant_5,
            cant_6,
            cant_7,
            cant_8,
            cant_9,
            cant_10
        FROM stock

        UNION

        SELECT
            cod_almacen,
            cod_articulo,
            cod_color_articulo,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cantidad AS cantidad,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_1 AS cant_1,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_2 AS cant_2,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_3 AS cant_3,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_4 AS cant_4,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_5 AS cant_5,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_6 AS cant_6,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_7 AS cant_7,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_8 AS cant_8,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_9 AS cant_9,
            (CASE tipo_movimiento WHEN 'NEG' THEN -1 ELSE 1 END) * cant_10 AS cant_10
        FROM movimientos_stock
        WHERE fecha_alta >= DATE_ADD(DATE(v_fecha), INTERVAL 1 DAY)
    ) s
    INNER JOIN colores_por_articulo cxa
        ON s.cod_articulo = cxa.cod_articulo
       AND s.cod_color_articulo = cxa.cod_color_articulo
    INNER JOIN articulos a
        ON s.cod_articulo = a.cod_articulo
    INNER JOIN tipo_producto_stock tps
        ON cxa.id_tipo_producto_stock = tps.id_tipo_producto_stock
    WHERE (IFNULL(p_codAlmacen, '') = '' OR s.cod_almacen = p_codAlmacen)
      AND (IFNULL(p_codArticulo, '') = '' OR s.cod_articulo = p_codArticulo)
      AND (IFNULL(p_codColor, '') = '' OR s.cod_color_articulo = p_codColor)
      AND (IFNULL(p_codTipo, '') = '' OR tps.denom_tipo_producto = p_codTipo)
    GROUP BY
        s.cod_almacen,
        s.cod_articulo,
        s.cod_color_articulo,
        a.denom_articulo,
        cxa.denom_color,
        a.cod_rango,
        tps.denom_tipo_producto
    ORDER BY
        CASE WHEN IFNULL(p_codArticulo, '') <> '' THEN s.cod_articulo END ASC,
        s.cod_almacen ASC,
        CASE WHEN IFNULL(p_codArticulo, '') = '' THEN s.cod_articulo END ASC,
        s.cod_color_articulo ASC;
END$$

DELIMITER ;
