DROP PROCEDURE IF EXISTS sp_stock_mp_a_fecha;

DELIMITER $$

CREATE PROCEDURE sp_stock_mp_a_fecha(
    IN p_codAlmacen VARCHAR(10),
    IN p_codMaterial VARCHAR(10),
    IN p_codColor VARCHAR(10),
    IN p_fecha VARCHAR(19)
)
BEGIN
    DECLARE v_fecha DATETIME;

    SET v_fecha = COALESCE(
        NULLIF(STR_TO_DATE(p_fecha, '%d/%m/%Y %H:%i:%s'), NULL),
        NULLIF(STR_TO_DATE(p_fecha, '%d/%m/%Y'), NULL),
        NULLIF(STR_TO_DATE(p_fecha, '%Y-%m-%d %H:%i:%s'), NULL),
        NULLIF(STR_TO_DATE(p_fecha, '%Y-%m-%d'), NULL),
        NOW()
    );

    SELECT
        s.cod_almacen,
        s.cod_material,
        s.cod_color,
        SUM(s.cantidad) AS cantidad,
        m.denom_material AS nombre_material,
        cmp.denom_color AS nombre_color,
        m.cod_rango AS cod_rango,
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
        SELECT *
        FROM stock_mp_tabla

        UNION

        SELECT
            cod_almacen,
            cod_material,
            cod_color,
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
        FROM movimientos_stock_mp
        WHERE fecha_alta >= DATE_ADD(DATE(v_fecha), INTERVAL 1 DAY)
    ) s
    INNER JOIN Materias_primas mp
        ON s.cod_material = mp.cod_material
       AND s.cod_color = mp.cod_color
    INNER JOIN Colores_materias_primas cmp
        ON s.cod_color = cmp.cod_color
    INNER JOIN materiales m
        ON s.cod_material = m.cod_material
    WHERE (IFNULL(p_codAlmacen, '') = '' OR s.cod_almacen = p_codAlmacen)
      AND (IFNULL(p_codMaterial, '') = '' OR s.cod_material = p_codMaterial)
      AND (IFNULL(p_codColor, '') = '' OR s.cod_color = p_codColor)
    GROUP BY
        s.cod_almacen,
        s.cod_material,
        s.cod_color,
        m.denom_material,
        cmp.denom_color,
        m.cod_rango
    ORDER BY
        CASE WHEN IFNULL(p_codMaterial, '') <> '' THEN s.cod_material END ASC,
        s.cod_almacen ASC,
        CASE WHEN IFNULL(p_codMaterial, '') = '' THEN s.cod_material END ASC,
        s.cod_color ASC;
END$$

DELIMITER ;
