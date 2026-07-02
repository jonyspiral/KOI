DROP PROCEDURE IF EXISTS saldo_clientes_a_fecha;

DELIMITER $$

CREATE PROCEDURE saldo_clientes_a_fecha(IN p_fecha VARCHAR(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci)
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

    IF v_fecha IS NULL THEN
        SIGNAL SQLSTATE '22007'
            SET MESSAGE_TEXT = 'saldo_clientes_a_fecha: formato de fecha invalido';
    END IF;

    SELECT
        c.cod_cli,
        d.empresa,
        IFNULL(SUM(
            (CASE
                WHEN d.tipo_docum = 'NDB' OR d.tipo_docum = 'FAC' THEN 1
                ELSE -1
            END) * d.importe_total
        ), 0) AS saldo
    FROM Clientes c
    LEFT JOIN documentos d
        ON c.cod_cli = d.cod_cliente
       AND d.fecha < DATE_ADD(DATE(v_fecha), INTERVAL 1 DAY)
    GROUP BY c.cod_cli, d.empresa;
END$$

DELIMITER ;
