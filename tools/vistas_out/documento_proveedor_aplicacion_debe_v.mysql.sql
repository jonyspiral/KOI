CREATE OR REPLACE VIEW documento_proveedor_aplicacion_debe_v AS SELECT * FROM documento_proveedor_aplicacion_v WHERE tipo_docum = 'FAC' OR tipo_docum = 'NDB';;
