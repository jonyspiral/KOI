-- rename_case_tables.sql
-- Generated: 2025-09-01T09:24:33.723611Z
-- Usage: set @db='koi1_stage'; then: USE `koi1_stage`; SOURCE this file.

SET @db := IFNULL(@db, DATABASE());
SELECT CONCAT('Operating on schema: ', @db) AS info;

-- Safety: require lower_case_table_names=0 (case-sensitive FS)
SET @lcts := @@lower_case_table_names;
SELECT @lcts AS lower_case_table_names;

CREATE TABLE IF NOT EXISTS `__case_rename_log`(
  id INT AUTO_INCREMENT PRIMARY KEY,
  old_name VARCHAR(255) NOT NULL,
  new_name VARCHAR(255) NOT NULL,
  executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Canonical names to enforce (with at least one uppercase)
DROP TEMPORARY TABLE IF EXISTS `__canonical_names`;
CREATE TEMPORARY TABLE `__canonical_names`(name VARCHAR(255) PRIMARY KEY) ENGINE=MEMORY;
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Almacenes');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('asientos_parametrizados_scarpsys_VIEJO');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Auditoria_stock_prod_terminado');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Calificaciones_crediticias');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Categorias_Convenio');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('CLAVES_INSTALACION');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Clientes');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('clientes_datos_NOVOSOFT');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Colores_crosstab');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Colores_materias_primas');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Condiciones_iva');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Consumos_tarea');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Contactos');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Cumplimientos_parciales');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Despachos_cabecera');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Despachos_detalle');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Dictamen_comercializacion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Dictamen_produccion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Empaques');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Feriados');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Forecast_detalle');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Forecast_encabezado');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Formas_pago');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Formularios');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Grupos_clientes');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('imputaciones_frecuentes_ANTERIOR');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Incorporacion_item_pedido');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Instrucciones_articulo');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Interes_comercial');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('lineas_productos_syncViewSpiral');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Lista_precios_clientes');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Magnitudes_clientes');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Marcas_syncViewSpiral');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Materias_primas');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('meses_orden_segun_cierre_V_ANT');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('MO_insumida');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Modulos_comercializacion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Modulos_produccion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Notas_debito_credito_cdc');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('NotaStock');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Operadores');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Operadores_puestos');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Orden_fabricacion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Ordenes_compra_cabecera');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Ordenes_compra_detalle');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Ordenes_fc');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Ordenes_fc_detalle');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Paises');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Parametros');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Parametros_Convenio');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Pasos_rutas_produccion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Patrones_mp_cabecera');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Patrones_mp_detalle');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Periodos_liquidacion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Planes_produccion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Programacion_produccion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Proveedores_materias_primas');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Provincias');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Puestos_trabajo');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Puntos_fijos_stock');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Remitos_internos_detalle');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Remitos_proveedor_cabecera');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Remuneraciones_por_articulo');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Rutas_produccion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Sectores_Almacenes');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Seguimiento_ordenes_compra');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Seguimiento_pedidos');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Stock_minimo');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Stock_mp_fc');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Stock_mp_real');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Stock_PT_temp');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('StockBackups');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('StockNotas');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Subd_egresos_temp');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Subd_ingresos_temp');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('sucursales_Clientes_spiral');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Tareas_cabecera');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Tareas_detalle');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Tipo_producto_Stock');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Tipo_sucursal');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Tipos_documento');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Tipos_proveedor');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Transportes');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Unidades_medida');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Unidades_produccion');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Vales_cabecera');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Vales_Detalle');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Vales_detalle_talles');
INSERT IGNORE INTO `__canonical_names`(name) VALUES ('Zonas');

DELIMITER $$
DROP PROCEDURE IF EXISTS `__do_case_renames`$$
CREATE PROCEDURE `__do_case_renames`()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE v_name VARCHAR(255);
  DECLARE v_existing VARCHAR(255);
  DECLARE v_tmp VARCHAR(255);
  DECLARE cur CURSOR FOR SELECT name FROM `__canonical_names`;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO v_name;
    IF done = 1 THEN LEAVE read_loop; END IF;

    -- find existing table that matches case-insensitively
    SELECT table_name INTO v_existing
      FROM information_schema.tables
     WHERE table_schema = @db
       AND LOWER(table_name) = LOWER(v_name)
     LIMIT 1;

    IF v_existing IS NULL THEN
      -- nothing to rename
      ITERATE read_loop;
    END IF;

    -- if already exact case, skip
    IF BINARY v_existing = BINARY v_name THEN
      ITERATE read_loop;
    END IF;

    -- ensure no object (table/view) already has the target name with exact case
    IF EXISTS (SELECT 1 FROM information_schema.tables
                WHERE table_schema=@db AND table_name = v_name) THEN
      -- target exists, skip to avoid collision
      ITERATE read_loop;
    END IF;

    SET v_tmp = CONCAT(v_existing, '__tmp_case_', FLOOR(RAND()*1000000));
    SET @sql := CONCAT('RENAME TABLE `', @db, '`.`', v_existing, '` TO `', @db, '`.`', v_tmp, '`, `', @db, '`.`', v_tmp, '` TO `', @db, '`.`', v_name, '`');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    INSERT INTO `__case_rename_log`(old_name, new_name) VALUES (v_existing, v_name);
  END LOOP;
  CLOSE cur;
END$$
DELIMITER ;

-- Run the renames
-- CALL `__do_case_renames`();

-- Show what happened
SELECT * FROM `__case_rename_log` ORDER BY executed_at DESC, id DESC;

-- (Optional) Generate rollback SQL on demand:
SELECT CONCAT('RENAME TABLE `', new_name, '` TO `', old_name, '`;') AS rollback_sql
  FROM `__case_rename_log` ORDER BY id DESC;
