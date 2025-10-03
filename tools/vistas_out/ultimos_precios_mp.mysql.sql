CREATE OR REPLACE VIEW ultimos_precios_mpASSELECT d.cod_material, d.cod_color, (SELECT IFNULL(d2.precio_unitario, 0) FROM Ordenes_compra_detalle d2 WH;;
