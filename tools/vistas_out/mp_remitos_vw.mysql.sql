CREATE OR REPLACE VIEW tmp_auto AS 
/*SELECT Remitos_proveedor_cabecera.fecha_recepcion AS fecha_movimiento, remitos_proveedor_detalle.cod_material, remitos_proveedor_detalle.cod_color, IFNULL(remitos_proveedor_detalle.cantidad, 0) * materiale;;
