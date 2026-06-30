# Inventario DB App Encinitas

Fecha de analisis: 2026-06-30 13:27:09
Alcance: analisis estatico de PHP y documentacion (`.md`, `.txt`) en `C:/dev/encinitas`.
Exclusiones: `.git`, `vendor`, `node_modules`, `img`, `css`, `js`, `fonts`, comprimidos y logs; tambien librerias de terceros (`includes/myPHPExcel`, `includes/PHPMailer`, `ws/storelocator/NotORM`) y copias `* - copia.php`.

## Resumen por objeto

| objeto | tipo: tabla/view/procedure/function | cantidad de referencias | archivos involucrados | estado |
|---|---|---:|---|---|
| acreditar_debitar_cheque_c | tabla | 2 | factory/Mapper.php | detectado |
| acreditar_debitar_cheque_d | tabla | 2 | factory/Mapper.php | detectado |
| ajustes_stock | tabla | 2 | factory/Mapper.php | detectado |
| ajustes_stock_mp | tabla | 2 | factory/Mapper.php | detectado |
| almacenes_por_seccion | tabla | 2 | factory/Mapper.php | detectado |
| almacenes_por_seccion_v | tabla | 1 | factory/Mapper.php | detectado |
| aporte_socio | tabla | 2 | factory/Mapper.php | detectado |
| areas_empresa | tabla | 2 | factory/Mapper.php | detectado |
| asientos_contables | tabla | 2 | factory/Mapper.php | detectado |
| asientos_modelo_c | tabla | 2 | factory/Mapper.php | detectado |
| asientos_modelo_d | tabla | 2 | factory/Mapper.php | detectado |
| autorizaciones_personas | tabla | 2 | factory/Mapper.php | detectado |
| autorizaciones_tipos | tabla | 1 | factory/Mapper.php | detectado |
| banco_propio | tabla | 2 | factory/Mapper.php | detectado |
| cajas_posibles_transferencia_interna | tabla | 2 | factory/Mapper.php | detectado |
| cambios_situacion_cliente | tabla | 2 | factory/Mapper.php | detectado |
| categorias_calzado_usuarios | tabla | 1 | factory/Mapper.php | detectado |
| chequera_c | tabla | 2 | factory/Mapper.php | detectado |
| chequera_d | tabla | 3 | factory/Mapper.php | detectado |
| cobro_cheque_ventanilla_c | tabla | 2 | factory/Mapper.php | detectado |
| cobro_cheque_ventanilla_d | tabla | 2 | factory/Mapper.php | detectado |
| cobro_cheque_ventanilla_temporal | tabla | 2 | factory/Mapper.php | detectado |
| color_por_articulo | tabla | 1 | clases/ColorPorArticulo.php | detectado |
| Colores_materias_primas | tabla | 1 | factory/Mapper.php | detectado |
| colores_por_articulo | tabla | 1 | factory/Mapper.php | detectado |
| condiciones_iva | tabla | 1 | factory/Mapper.php | detectado |
| confirmaciones_stock | tabla | 2 | factory/Mapper.php | detectado |
| consumos_stock_mp | tabla | 2 | factory/Mapper.php | detectado |
| cuenta_bancaria | tabla | 2 | factory/Mapper.php | detectado |
| curvas_por_articulo | tabla | 3 | factory/Mapper.php | detectado |
| deposito_bancario_c | tabla | 2 | factory/Mapper.php | detectado |
| deposito_bancario_d | tabla | 2 | factory/Mapper.php | detectado |
| deposito_bancario_temporal | tabla | 2 | factory/Mapper.php | detectado |
| despachos_c | tabla | 2 | factory/Mapper.php | detectado |
| despachos_d | tabla | 1 | factory/Mapper.php | detectado |
| devoluciones_a_cliente_c | tabla | 2 | factory/Mapper.php | detectado |
| devoluciones_a_cliente_d | tabla | 1 | factory/Mapper.php | detectado |
| documento_gasto_datos | tabla | 2 | factory/Mapper.php | detectado |
| documento_proveedor_c | tabla | 2 | factory/Mapper.php | detectado |
| documento_proveedor_d | tabla | 2 | factory/Mapper.php | detectado |
| documento_proveedor_h | tabla | 2 | factory/Mapper.php | detectado |
| documentos_c | tabla | 6 | factory/Mapper.php | detectado |
| documentos_d | tabla | 2 | factory/Mapper.php | detectado |
| documentos_h | tabla | 3 | factory/Mapper.php | detectado |
| ecommerce_coupons | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_customers | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_deliverys | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_order_details | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_order_status | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_orders | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_payment_methods | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_payments | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_servicios_andreani | tabla | 2 | factory/Mapper.php | detectado |
| ecommerce_usergroups | tabla | 2 | factory/Mapper.php | detectado |
| ejercicios_contables | tabla | 2 | factory/Mapper.php | detectado |
| filas_asientos_contables | tabla | 2 | factory/Mapper.php | detectado |
| Forecast_detalle | tabla | 2 | factory/Mapper.php | detectado |
| Forecast_encabezado | tabla | 1 | factory/Mapper.php | detectado |
| Formas_pago | tabla | 1 | factory/Mapper.php | detectado |
| funcionalidades_por_rol | tabla | 3 | factory/Mapper.php | detectado |
| garantias_c | tabla | 1 | factory/Mapper.php | detectado |
| garantias_d | tabla | 2 | factory/Mapper.php | detectado |
| gestiones_clientes_cobranza | tabla | 2 | factory/Mapper.php | detectado |
| grupo_empresa | tabla | 1 | factory/Mapper.php | detectado |
| Grupos_clientes | tabla | 2 | factory/Mapper.php | detectado |
| horarios_por_secciones | tabla | 2 | factory/Mapper.php | detectado |
| importe_por_operacion_c | tabla | 2 | factory/Mapper.php | detectado |
| importe_por_operacion_d | tabla | 1 | factory/Mapper.php | detectado |
| impuesto_por_documento_proveedor | tabla | 2 | factory/Mapper.php | detectado |
| indicadores_por_rol | tabla | 2 | factory/Mapper.php | detectado |
| ingreso_cheque_propio | tabla | 2 | factory/Mapper.php | detectado |
| instrucciones_articulo | tabla | 1 | factory/Mapper.php | detectado |
| koi_sessions | tabla | 3 | clases/UsuarioLogin.php | detectado |
| koi_ticket | tabla | 2 | factory/Mapper.php | detectado |
| lineas_productos | tabla | 2 | factory/Mapper.php | detectado |
| Materias_primas | tabla | 1 | factory/Mapper.php | detectado |
| modulos_basicos | tabla | 1 | factory/Mapper.php | detectado |
| motivos_ausentismo | tabla | 2 | factory/Mapper.php | detectado |
| movimientos_almacen | tabla | 2 | factory/Mapper.php | detectado |
| movimientos_almacen_confirmacion | tabla | 2 | factory/Mapper.php | detectado |
| movimientos_almacen_confirmacion_mp | tabla | 2 | factory/Mapper.php | detectado |
| movimientos_almacen_mp | tabla | 2 | factory/Mapper.php | detectado |
| movimientos_stock | tabla | 2 | factory/Mapper.php | detectado |
| movimientos_stock_mp | tabla | 2 | factory/Mapper.php | detectado |
| nombre_sp | procedure | 1 | docs/migracion/stored-procedures-mysql.md | detectado |
| notas_credito_causalidades | tabla | 2 | factory/Mapper.php | detectado |
| notificaciones_por_usuario | tabla | 1 | factory/Mapper.php | detectado |
| orden_de_pago | tabla | 2 | factory/Mapper.php | detectado |
| Orden_fabricacion | tabla | 2 | factory/Mapper.php | detectado |
| ordenes_compra_cabecera | tabla | 2 | factory/Mapper.php | detectado |
| Ordenes_compra_detalle | tabla | 2 | factory/Mapper.php | detectado |
| parametros_contabilidad | tabla | 1 | factory/Mapper.php | detectado |
| Pasos_rutas_produccion | tabla | 1 | factory/Mapper.php | detectado |
| patrones_mp_cabecera | tabla | 1 | factory/Mapper.php | detectado |
| Patrones_mp_detalle | tabla | 3 | factory/Mapper.php | detectado |
| pedidos_c | tabla | 2 | factory/Mapper.php | detectado |
| pedidos_d | tabla | 2 | factory/Mapper.php | detectado |
| pedidos_d_v | tabla | 1 | content/sistema/indicadores/index.php | detectado |
| periodos_fiscales_cierres | tabla | 2 | factory/Mapper.php | detectado |
| periodos_fiscales_tipos | tabla | 2 | factory/Mapper.php | detectado |
| permisos_por_caja | tabla | 2 | factory/Mapper.php | detectado |
| permisos_por_usuarios_por_caja | tabla | 2 | factory/Mapper.php | detectado |
| persona_gasto | tabla | 2 | factory/Mapper.php | detectado |
| plan_cuentas | tabla | 1 | factory/Mapper.php | detectado |
| Planes_produccion | tabla | 2 | factory/Mapper.php | detectado |
| presupuesto_c | tabla | 2 | factory/Mapper.php | detectado |
| presupuesto_d | tabla | 3 | factory/Mapper.php | detectado |
| presupuesto_orden_compra | tabla | 3 | factory/Mapper.php | detectado |
| proveedores_datos | tabla | 4 | factory/Mapper.php | detectado |
| Proveedores_materias_primas | tabla | 1 | factory/Mapper.php | detectado |
| rango_talles | tabla | 2 | factory/Mapper.php | detectado |
| rechazo_de_cheque_c | tabla | 2 | factory/Mapper.php | detectado |
| rechazo_de_cheque_d | tabla | 2 | factory/Mapper.php | detectado |
| registro_entradas_salidas | tabla | 3 | factory/Mapper.php | detectado |
| reingreso_cheque_cartera | tabla | 2 | factory/Mapper.php | detectado |
| remito_orden_de_compra | tabla | 4 | factory/Mapper.php | detectado |
| remitos_c | tabla | 2 | factory/Mapper.php | detectado |
| remitos_proveedor_cabecera | tabla | 3 | factory/Mapper.php | detectado |
| remitos_proveedor_detalle | tabla | 4 | factory/Mapper.php | detectado |
| rendicion_de_gastos | tabla | 2 | factory/Mapper.php | detectado |
| retencion_efectuada | tabla | 2 | factory/Mapper.php | detectado |
| retencion_ganancias_tabla | tabla | 2 | factory/Mapper.php | detectado |
| retencion_sufrida | tabla | 2 | factory/Mapper.php | detectado |
| retenciones_ganancias_honorarios | tabla | 2 | factory/Mapper.php | detectado |
| retiro_socio | tabla | 2 | factory/Mapper.php | detectado |
| REVISAR_MANUALMENTE | unknown | 7 | factory/Datos.php; factory/Factory.php | revisar manualmente |
| roles_por_tipo_notificacion | tabla | 3 | factory/Mapper.php | detectado |
| roles_por_usuario | tabla | 4 | factory/Mapper.php | detectado |
| roles_por_usuario_v | tabla | 1 | factory/Mapper.php | detectado |
| rubros_iva | tabla | 1 | factory/Mapper.php | detectado |
| rutas_produccion | tabla | 3 | factory/Mapper.php | detectado |
| secciones_produccion | tabla | 2 | factory/Mapper.php | detectado |
| solicitud_de_fondos_c | tabla | 2 | factory/Mapper.php | detectado |
| solicitud_de_fondos_d | tabla | 2 | factory/Mapper.php | detectado |
| stock_mp_tabla | tabla | 1 | factory/Mapper.php | detectado |
| stock_produccion_incumplida_40_v | tabla | 1 | content/api/funciones.php | detectado |
| sucursales_clientes | tabla | 2 | factory/Mapper.php | detectado |
| Tareas_cabecera | tabla | 3 | factory/Mapper.php | detectado |
| Tareas_detalle | tabla | 1 | factory/Mapper.php | detectado |
| tipo_factura | tabla | 2 | factory/Mapper.php | detectado |
| tipo_producto_stock | tabla | 2 | factory/Mapper.php | detectado |
| tipo_retencion | tabla | 3 | factory/Mapper.php | detectado |
| tipos_notificacion | tabla | 2 | factory/Mapper.php | detectado |
| Tipos_proveedor | tabla | 2 | factory/Mapper.php | detectado |
| transferencia_bancaria_importe | tabla | 2 | factory/Mapper.php | detectado |
| transferencia_bancaria_operacion | tabla | 2 | factory/Mapper.php | detectado |
| transferencia_interna_c | tabla | 2 | factory/Mapper.php | detectado |
| transferencia_interna_d | tabla | 2 | factory/Mapper.php | detectado |
| Unidades_medida | tabla | 1 | factory/Mapper.php | detectado |
| usuarios_por_almacen | tabla | 2 | factory/Mapper.php | detectado |
| usuarios_por_almacen_v | tabla | 1 | factory/Mapper.php | detectado |
| usuarios_por_area_empresa | tabla | 3 | factory/Mapper.php | detectado |
| usuarios_por_area_empresa_v | tabla | 1 | factory/Mapper.php | detectado |
| usuarios_por_caja | tabla | 2 | factory/Mapper.php | detectado |
| usuarios_por_seccion | tabla | 2 | factory/Mapper.php | detectado |
| usuarios_por_seccion_v | tabla | 1 | factory/Mapper.php | detectado |
| usuarios_por_tipo_notificacion | tabla | 3 | factory/Mapper.php | detectado |
| venta_cheques_c | tabla | 2 | factory/Mapper.php | detectado |
| venta_cheques_d | tabla | 2 | factory/Mapper.php | detectado |
| venta_cheques_temporal | tabla | 2 | factory/Mapper.php | detectado |
| zonas_geo | tabla | 1 | factory/Mapper.php | detectado |

## Referencias detalladas

Detalle completo en `docs/migracion/referencias_db_app.csv`.

| objeto | tipo | operacion | archivo | linea | estado |
|---|---|---|---|---:|---|
| color_por_articulo | tabla | FROM | clases/ColorPorArticulo.php | 420 | detectado |
| koi_sessions | tabla | UPDATE | clases/UsuarioLogin.php | 55 | detectado |
| koi_sessions | tabla | INSERT INTO | clases/UsuarioLogin.php | 55 | detectado |
| koi_sessions | tabla | FROM | clases/UsuarioLogin.php | 55 | detectado |
| stock_produccion_incumplida_40_v | tabla | FROM | content/api/funciones.php | 13 | detectado |
| pedidos_d_v | tabla | FROM | content/sistema/indicadores/index.php | 152 | detectado |
| nombre_sp | procedure | CALL | docs/migracion/stored-procedures-mysql.md | 13 | detectado |
| REVISAR_MANUALMENTE | unknown | EjecutarStoredProcedure* | factory/Datos.php | 124 | revisar manualmente |
| REVISAR_MANUALMENTE | unknown | EjecutarStoredProcedure* | factory/Datos.php | 137 | revisar manualmente |
| REVISAR_MANUALMENTE | unknown | EjecutarStoredProcedure* | factory/Datos.php | 138 | revisar manualmente |
| REVISAR_MANUALMENTE | unknown | EjecutarStoredProcedure* | factory/Datos.php | 143 | revisar manualmente |
| REVISAR_MANUALMENTE | unknown | EjecutarStoredProcedure* | factory/Datos.php | 144 | revisar manualmente |
| REVISAR_MANUALMENTE | unknown | EjecutarStoredProcedure* | factory/Factory.php | 123 | revisar manualmente |
| REVISAR_MANUALMENTE | unknown | EjecutarStoredProcedure* | factory/Factory.php | 133 | revisar manualmente |
| acreditar_debitar_cheque_d | tabla | INSERT INTO | factory/Mapper.php | 4051 | detectado |
| acreditar_debitar_cheque_d | tabla | FROM | factory/Mapper.php | 4067 | detectado |
| acreditar_debitar_cheque_c | tabla | INSERT INTO | factory/Mapper.php | 4086 | detectado |
| acreditar_debitar_cheque_c | tabla | FROM | factory/Mapper.php | 4108 | detectado |
| ajustes_stock | tabla | INSERT INTO | factory/Mapper.php | 4126 | detectado |
| ajustes_stock | tabla | FROM | factory/Mapper.php | 4154 | detectado |
| ajustes_stock_mp | tabla | INSERT INTO | factory/Mapper.php | 4171 | detectado |
| ajustes_stock_mp | tabla | FROM | factory/Mapper.php | 4199 | detectado |
| almacenes_por_seccion_v | tabla | FROM | factory/Mapper.php | 4252 | detectado |
| almacenes_por_seccion | tabla | INSERT INTO | factory/Mapper.php | 4256 | detectado |
| almacenes_por_seccion | tabla | DELETE FROM | factory/Mapper.php | 4264 | detectado |
| aporte_socio | tabla | INSERT INTO | factory/Mapper.php | 4285 | detectado |
| aporte_socio | tabla | FROM | factory/Mapper.php | 4329 | detectado |
| areas_empresa | tabla | INSERT INTO | factory/Mapper.php | 4347 | detectado |
| usuarios_por_area_empresa | tabla | DELETE FROM | factory/Mapper.php | 4363 | detectado |
| areas_empresa | tabla | FROM | factory/Mapper.php | 4378 | detectado |
| asientos_contables | tabla | INSERT INTO | factory/Mapper.php | 4503 | detectado |
| filas_asientos_contables | tabla | DELETE FROM | factory/Mapper.php | 4525 | detectado |
| asientos_contables | tabla | FROM | factory/Mapper.php | 4542 | detectado |
| asientos_modelo_c | tabla | INSERT INTO | factory/Mapper.php | 4559 | detectado |
| asientos_modelo_d | tabla | DELETE FROM | factory/Mapper.php | 4573 | detectado |
| asientos_modelo_c | tabla | FROM | factory/Mapper.php | 4587 | detectado |
| asientos_modelo_d | tabla | INSERT INTO | factory/Mapper.php | 4605 | detectado |
| autorizaciones_personas | tabla | INSERT INTO | factory/Mapper.php | 4688 | detectado |
| autorizaciones_personas | tabla | DELETE FROM | factory/Mapper.php | 4699 | detectado |
| autorizaciones_tipos | tabla | INSERT INTO | factory/Mapper.php | 4719 | detectado |
| banco_propio | tabla | INSERT INTO | factory/Mapper.php | 4793 | detectado |
| banco_propio | tabla | FROM | factory/Mapper.php | 4852 | detectado |
| cajas_posibles_transferencia_interna | tabla | INSERT INTO | factory/Mapper.php | 4937 | detectado |
| cajas_posibles_transferencia_interna | tabla | DELETE FROM | factory/Mapper.php | 4946 | detectado |
| cambios_situacion_cliente | tabla | INSERT INTO | factory/Mapper.php | 4964 | detectado |
| cambios_situacion_cliente | tabla | FROM | factory/Mapper.php | 4982 | detectado |
| categorias_calzado_usuarios | tabla | INSERT INTO | factory/Mapper.php | 4999 | detectado |
| notas_credito_causalidades | tabla | INSERT INTO | factory/Mapper.php | 5039 | detectado |
| notas_credito_causalidades | tabla | DELETE FROM | factory/Mapper.php | 5049 | detectado |
| periodos_fiscales_cierres | tabla | INSERT INTO | factory/Mapper.php | 5069 | detectado |
| periodos_fiscales_cierres | tabla | FROM | factory/Mapper.php | 5101 | detectado |
| chequera_c | tabla | INSERT INTO | factory/Mapper.php | 5206 | detectado |
| chequera_c | tabla | FROM | factory/Mapper.php | 5231 | detectado |
| chequera_d | tabla | INSERT INTO | factory/Mapper.php | 5248 | detectado |
| chequera_d | tabla | DELETE FROM | factory/Mapper.php | 5262 | detectado |
| chequera_d | tabla | FROM | factory/Mapper.php | 5265 | detectado |
| cobro_cheque_ventanilla_d | tabla | INSERT INTO | factory/Mapper.php | 5485 | detectado |
| cobro_cheque_ventanilla_d | tabla | FROM | factory/Mapper.php | 5501 | detectado |
| cobro_cheque_ventanilla_c | tabla | INSERT INTO | factory/Mapper.php | 5520 | detectado |
| cobro_cheque_ventanilla_c | tabla | FROM | factory/Mapper.php | 5549 | detectado |
| cobro_cheque_ventanilla_temporal | tabla | INSERT INTO | factory/Mapper.php | 5567 | detectado |
| cobro_cheque_ventanilla_temporal | tabla | FROM | factory/Mapper.php | 5603 | detectado |
| Colores_materias_primas | tabla | INSERT INTO | factory/Mapper.php | 5620 | detectado |
| Materias_primas | tabla | INSERT INTO | factory/Mapper.php | 5658 | detectado |
| colores_por_articulo | tabla | INSERT INTO | factory/Mapper.php | 5704 | detectado |
| curvas_por_articulo | tabla | DELETE FROM | factory/Mapper.php | 5726 | detectado |
| condiciones_iva | tabla | INSERT INTO | factory/Mapper.php | 5845 | detectado |
| confirmaciones_stock | tabla | INSERT INTO | factory/Mapper.php | 5896 | detectado |
| confirmaciones_stock | tabla | FROM | factory/Mapper.php | 5927 | detectado |
| consumos_stock_mp | tabla | INSERT INTO | factory/Mapper.php | 6073 | detectado |
| consumos_stock_mp | tabla | FROM | factory/Mapper.php | 6097 | detectado |
| cuenta_bancaria | tabla | INSERT INTO | factory/Mapper.php | 6114 | detectado |
| cuenta_bancaria | tabla | FROM | factory/Mapper.php | 6152 | detectado |
| curvas_por_articulo | tabla | INSERT INTO | factory/Mapper.php | 6288 | detectado |
| curvas_por_articulo | tabla | DELETE FROM | factory/Mapper.php | 6298 | detectado |
| modulos_basicos | tabla | INSERT INTO | factory/Mapper.php | 6318 | detectado |
| deposito_bancario_d | tabla | INSERT INTO | factory/Mapper.php | 6372 | detectado |
| deposito_bancario_d | tabla | FROM | factory/Mapper.php | 6388 | detectado |
| deposito_bancario_c | tabla | INSERT INTO | factory/Mapper.php | 6407 | detectado |
| deposito_bancario_c | tabla | FROM | factory/Mapper.php | 6438 | detectado |
| deposito_bancario_temporal | tabla | INSERT INTO | factory/Mapper.php | 6456 | detectado |
| deposito_bancario_temporal | tabla | FROM | factory/Mapper.php | 6502 | detectado |
| despachos_c | tabla | INSERT INTO | factory/Mapper.php | 6519 | detectado |
| despachos_c | tabla | FROM | factory/Mapper.php | 6565 | detectado |
| despachos_d | tabla | INSERT INTO | factory/Mapper.php | 6583 | detectado |
| devoluciones_a_cliente_c | tabla | INSERT INTO | factory/Mapper.php | 6671 | detectado |
| devoluciones_a_cliente_c | tabla | FROM | factory/Mapper.php | 6698 | detectado |
| devoluciones_a_cliente_d | tabla | INSERT INTO | factory/Mapper.php | 6715 | detectado |
| documento_gasto_datos | tabla | INSERT INTO | factory/Mapper.php | 6921 | detectado |
| documento_gasto_datos | tabla | FROM | factory/Mapper.php | 6969 | detectado |
| documentos_h | tabla | INSERT INTO | factory/Mapper.php | 7013 | detectado |
| documentos_h | tabla | DELETE FROM | factory/Mapper.php | 7044 | detectado |
| documentos_h | tabla | FROM | factory/Mapper.php | 7047 | detectado |
| documentos_d | tabla | INSERT INTO | factory/Mapper.php | 7069 | detectado |
| documentos_d | tabla | DELETE FROM | factory/Mapper.php | 7115 | detectado |
| documento_proveedor_c | tabla | INSERT INTO | factory/Mapper.php | 7138 | detectado |
| documento_proveedor_c | tabla | FROM | factory/Mapper.php | 7221 | detectado |
| documento_proveedor_h | tabla | INSERT INTO | factory/Mapper.php | 7347 | detectado |
| documento_proveedor_h | tabla | DELETE FROM | factory/Mapper.php | 7366 | detectado |
| documento_proveedor_d | tabla | INSERT INTO | factory/Mapper.php | 7387 | detectado |
| documento_proveedor_d | tabla | FROM | factory/Mapper.php | 7445 | detectado |
| ecommerce_coupons | tabla | INSERT INTO | factory/Mapper.php | 7463 | detectado |
| ecommerce_coupons | tabla | FROM | factory/Mapper.php | 7504 | detectado |
| ecommerce_customers | tabla | INSERT INTO | factory/Mapper.php | 7521 | detectado |
| ecommerce_customers | tabla | FROM | factory/Mapper.php | 7570 | detectado |
| ecommerce_deliverys | tabla | INSERT INTO | factory/Mapper.php | 7587 | detectado |
| ecommerce_deliverys | tabla | FROM | factory/Mapper.php | 7626 | detectado |
| ecommerce_orders | tabla | INSERT INTO | factory/Mapper.php | 7643 | detectado |
| ecommerce_orders | tabla | FROM | factory/Mapper.php | 7697 | detectado |
| ecommerce_order_details | tabla | INSERT INTO | factory/Mapper.php | 7714 | detectado |
| ecommerce_order_details | tabla | FROM | factory/Mapper.php | 7747 | detectado |
| ecommerce_order_status | tabla | INSERT INTO | factory/Mapper.php | 7764 | detectado |
| ecommerce_order_status | tabla | FROM | factory/Mapper.php | 7802 | detectado |
| ecommerce_payments | tabla | INSERT INTO | factory/Mapper.php | 7852 | detectado |
| ecommerce_payments | tabla | FROM | factory/Mapper.php | 7890 | detectado |
| ecommerce_payment_methods | tabla | INSERT INTO | factory/Mapper.php | 7907 | detectado |
| ecommerce_payment_methods | tabla | FROM | factory/Mapper.php | 7933 | detectado |
| ecommerce_servicios_andreani | tabla | INSERT INTO | factory/Mapper.php | 7950 | detectado |
| ecommerce_servicios_andreani | tabla | FROM | factory/Mapper.php | 7979 | detectado |
| ecommerce_usergroups | tabla | INSERT INTO | factory/Mapper.php | 7996 | detectado |
| ecommerce_usergroups | tabla | FROM | factory/Mapper.php | 8025 | detectado |
| ejercicios_contables | tabla | INSERT INTO | factory/Mapper.php | 8084 | detectado |
| ejercicios_contables | tabla | FROM | factory/Mapper.php | 8116 | detectado |
| documentos_c | tabla | INSERT INTO | factory/Mapper.php | 8214 | detectado |
| documentos_c | tabla | FROM | factory/Mapper.php | 8333 | detectado |
| horarios_por_secciones | tabla | INSERT INTO | factory/Mapper.php | 8357 | detectado |
| horarios_por_secciones | tabla | FROM | factory/Mapper.php | 8382 | detectado |
| proveedores_datos | tabla | INSERT INTO | factory/Mapper.php | 8399 | detectado |
| proveedores_datos | tabla | FROM | factory/Mapper.php | 8546 | detectado |
| registro_entradas_salidas | tabla | INSERT INTO | factory/Mapper.php | 8563 | detectado |
| registro_entradas_salidas | tabla | DELETE FROM | factory/Mapper.php | 8595 | detectado |
| registro_entradas_salidas | tabla | FROM | factory/Mapper.php | 8598 | detectado |
| filas_asientos_contables | tabla | INSERT INTO | factory/Mapper.php | 8616 | detectado |
| Forecast_encabezado | tabla | INSERT INTO | factory/Mapper.php | 8662 | detectado |
| Forecast_detalle | tabla | DELETE FROM | factory/Mapper.php | 8680 | detectado |
| Forecast_detalle | tabla | INSERT INTO | factory/Mapper.php | 8713 | detectado |
| Formas_pago | tabla | INSERT INTO | factory/Mapper.php | 8750 | detectado |
| funcionalidades_por_rol | tabla | INSERT INTO | factory/Mapper.php | 8839 | detectado |
| funcionalidades_por_rol | tabla | DELETE FROM | factory/Mapper.php | 8848 | detectado |
| garantias_c | tabla | INSERT INTO | factory/Mapper.php | 8922 | detectado |
| garantias_d | tabla | INSERT INTO | factory/Mapper.php | 8977 | detectado |
| garantias_d | tabla | FROM | factory/Mapper.php | 9007 | detectado |
| gestiones_clientes_cobranza | tabla | INSERT INTO | factory/Mapper.php | 9024 | detectado |
| gestiones_clientes_cobranza | tabla | FROM | factory/Mapper.php | 9057 | detectado |
| grupo_empresa | tabla | INSERT INTO | factory/Mapper.php | 9074 | detectado |
| koi_ticket | tabla | INSERT INTO | factory/Mapper.php | 9126 | detectado |
| koi_ticket | tabla | FROM | factory/Mapper.php | 9171 | detectado |
| importe_por_operacion_c | tabla | INSERT INTO | factory/Mapper.php | 9251 | detectado |
| importe_por_operacion_c | tabla | FROM | factory/Mapper.php | 9267 | detectado |
| importe_por_operacion_d | tabla | INSERT INTO | factory/Mapper.php | 9286 | detectado |
| plan_cuentas | tabla | INSERT INTO | factory/Mapper.php | 9320 | detectado |
| impuesto_por_documento_proveedor | tabla | INSERT INTO | factory/Mapper.php | 9425 | detectado |
| impuesto_por_documento_proveedor | tabla | DELETE FROM | factory/Mapper.php | 9444 | detectado |
| indicadores_por_rol | tabla | DELETE FROM | factory/Mapper.php | 9491 | detectado |
| indicadores_por_rol | tabla | INSERT INTO | factory/Mapper.php | 9529 | detectado |
| ingreso_cheque_propio | tabla | INSERT INTO | factory/Mapper.php | 9555 | detectado |
| ingreso_cheque_propio | tabla | FROM | factory/Mapper.php | 9573 | detectado |
| instrucciones_articulo | tabla | INSERT INTO | factory/Mapper.php | 9592 | detectado |
| lineas_productos | tabla | INSERT INTO | factory/Mapper.php | 9637 | detectado |
| lineas_productos | tabla | FROM | factory/Mapper.php | 9673 | detectado |
| motivos_ausentismo | tabla | INSERT INTO | factory/Mapper.php | 9843 | detectado |
| motivos_ausentismo | tabla | FROM | factory/Mapper.php | 9869 | detectado |
| movimientos_almacen | tabla | INSERT INTO | factory/Mapper.php | 9886 | detectado |
| movimientos_almacen | tabla | FROM | factory/Mapper.php | 9916 | detectado |
| movimientos_almacen_confirmacion | tabla | INSERT INTO | factory/Mapper.php | 9933 | detectado |
| movimientos_almacen_confirmacion | tabla | FROM | factory/Mapper.php | 9975 | detectado |
| movimientos_almacen_mp | tabla | INSERT INTO | factory/Mapper.php | 9992 | detectado |
| movimientos_almacen_mp | tabla | FROM | factory/Mapper.php | 10022 | detectado |
| movimientos_almacen_confirmacion_mp | tabla | INSERT INTO | factory/Mapper.php | 10039 | detectado |
| movimientos_almacen_confirmacion_mp | tabla | FROM | factory/Mapper.php | 10081 | detectado |
| movimientos_stock | tabla | INSERT INTO | factory/Mapper.php | 10098 | detectado |
| movimientos_stock | tabla | FROM | factory/Mapper.php | 10130 | detectado |
| movimientos_stock_mp | tabla | INSERT INTO | factory/Mapper.php | 10147 | detectado |
| movimientos_stock_mp | tabla | FROM | factory/Mapper.php | 10179 | detectado |
| documentos_c | tabla | INSERT INTO | factory/Mapper.php | 10194 | detectado |
| documentos_c | tabla | FROM | factory/Mapper.php | 10300 | detectado |
| documentos_c | tabla | INSERT INTO | factory/Mapper.php | 10322 | detectado |
| documentos_c | tabla | FROM | factory/Mapper.php | 10414 | detectado |
| notificaciones_por_usuario | tabla | INSERT INTO | factory/Mapper.php | 10485 | detectado |
| ordenes_compra_cabecera | tabla | INSERT INTO | factory/Mapper.php | 10706 | detectado |
| ordenes_compra_cabecera | tabla | FROM | factory/Mapper.php | 10754 | detectado |
| Ordenes_compra_detalle | tabla | INSERT INTO | factory/Mapper.php | 10772 | detectado |
| Ordenes_compra_detalle | tabla | FROM | factory/Mapper.php | 10841 | detectado |
| Orden_fabricacion | tabla | INSERT INTO | factory/Mapper.php | 10859 | detectado |
| Orden_fabricacion | tabla | FROM | factory/Mapper.php | 10918 | detectado |
| orden_de_pago | tabla | INSERT INTO | factory/Mapper.php | 10936 | detectado |
| orden_de_pago | tabla | FROM | factory/Mapper.php | 10994 | detectado |
| parametros_contabilidad | tabla | INSERT INTO | factory/Mapper.php | 11079 | detectado |
| patrones_mp_cabecera | tabla | INSERT INTO | factory/Mapper.php | 11125 | detectado |
| Patrones_mp_detalle | tabla | DELETE FROM | factory/Mapper.php | 11151 | detectado |
| patrones_mp_detalle | tabla | INSERT INTO | factory/Mapper.php | 11188 | detectado |
| Patrones_mp_detalle | tabla | FROM | factory/Mapper.php | 11234 | detectado |
| pedidos_c | tabla | INSERT INTO | factory/Mapper.php | 11254 | detectado |
| pedidos_d | tabla | DELETE FROM | factory/Mapper.php | 11296 | detectado |
| pedidos_c | tabla | FROM | factory/Mapper.php | 11320 | detectado |
| pedidos_d | tabla | INSERT INTO | factory/Mapper.php | 11338 | detectado |
| permisos_por_caja | tabla | INSERT INTO | factory/Mapper.php | 11403 | detectado |
| permisos_por_caja | tabla | DELETE FROM | factory/Mapper.php | 11411 | detectado |
| permisos_por_usuarios_por_caja | tabla | INSERT INTO | factory/Mapper.php | 11431 | detectado |
| permisos_por_usuarios_por_caja | tabla | DELETE FROM | factory/Mapper.php | 11442 | detectado |
| persona_gasto | tabla | INSERT INTO | factory/Mapper.php | 11460 | detectado |
| persona_gasto | tabla | FROM | factory/Mapper.php | 11486 | detectado |
| Planes_produccion | tabla | INSERT INTO | factory/Mapper.php | 11671 | detectado |
| Planes_produccion | tabla | FROM | factory/Mapper.php | 11698 | detectado |
| presupuesto_c | tabla | INSERT INTO | factory/Mapper.php | 11840 | detectado |
| presupuesto_d | tabla | DELETE FROM | factory/Mapper.php | 11862 | detectado |
| presupuesto_c | tabla | FROM | factory/Mapper.php | 11878 | detectado |
| presupuesto_d | tabla | INSERT INTO | factory/Mapper.php | 11896 | detectado |
| presupuesto_d | tabla | FROM | factory/Mapper.php | 11952 | detectado |
| presupuesto_orden_compra | tabla | INSERT INTO | factory/Mapper.php | 11970 | detectado |
| presupuesto_orden_compra | tabla | DELETE FROM | factory/Mapper.php | 11985 | detectado |
| presupuesto_orden_compra | tabla | FROM | factory/Mapper.php | 11988 | detectado |
| proveedores_datos | tabla | INSERT INTO | factory/Mapper.php | 12006 | detectado |
| proveedores_datos | tabla | FROM | factory/Mapper.php | 12150 | detectado |
| Proveedores_materias_primas | tabla | INSERT INTO | factory/Mapper.php | 12169 | detectado |
| rango_talles | tabla | INSERT INTO | factory/Mapper.php | 12273 | detectado |
| rango_talles | tabla | FROM | factory/Mapper.php | 12309 | detectado |
| rechazo_de_cheque_d | tabla | INSERT INTO | factory/Mapper.php | 12328 | detectado |
| rechazo_de_cheque_d | tabla | FROM | factory/Mapper.php | 12344 | detectado |
| rechazo_de_cheque_c | tabla | INSERT INTO | factory/Mapper.php | 12364 | detectado |
| rechazo_de_cheque_c | tabla | FROM | factory/Mapper.php | 12392 | detectado |
| reingreso_cheque_cartera | tabla | INSERT INTO | factory/Mapper.php | 12493 | detectado |
| reingreso_cheque_cartera | tabla | FROM | factory/Mapper.php | 12521 | detectado |
| remitos_c | tabla | INSERT INTO | factory/Mapper.php | 12541 | detectado |
| remitos_c | tabla | FROM | factory/Mapper.php | 12598 | detectado |
| remito_orden_de_compra | tabla | INSERT INTO | factory/Mapper.php | 12617 | detectado |
| remito_orden_de_compra | tabla | DELETE FROM | factory/Mapper.php | 12653 | detectado |
| remito_orden_de_compra | tabla | FROM | factory/Mapper.php | 12656 | detectado |
| remitos_proveedor_cabecera | tabla | INSERT INTO | factory/Mapper.php | 12673 | detectado |
| remito_orden_de_compra | tabla | DELETE FROM | factory/Mapper.php | 12705 | detectado |
| remitos_proveedor_detalle | tabla | DELETE FROM | factory/Mapper.php | 12707 | detectado |
| remitos_proveedor_cabecera | tabla | DELETE FROM | factory/Mapper.php | 12709 | detectado |
| remitos_proveedor_cabecera | tabla | FROM | factory/Mapper.php | 12712 | detectado |
| remitos_proveedor_detalle | tabla | INSERT INTO | factory/Mapper.php | 12730 | detectado |
| remitos_proveedor_detalle | tabla | DELETE FROM | factory/Mapper.php | 12769 | detectado |
| remitos_proveedor_detalle | tabla | FROM | factory/Mapper.php | 12773 | detectado |
| rendicion_de_gastos | tabla | INSERT INTO | factory/Mapper.php | 12792 | detectado |
| rendicion_de_gastos | tabla | FROM | factory/Mapper.php | 12833 | detectado |
| retencion_efectuada | tabla | INSERT INTO | factory/Mapper.php | 12851 | detectado |
| retencion_efectuada | tabla | FROM | factory/Mapper.php | 12893 | detectado |
| retenciones_ganancias_honorarios | tabla | INSERT INTO | factory/Mapper.php | 12912 | detectado |
| retenciones_ganancias_honorarios | tabla | FROM | factory/Mapper.php | 12945 | detectado |
| retencion_sufrida | tabla | INSERT INTO | factory/Mapper.php | 12962 | detectado |
| retencion_sufrida | tabla | FROM | factory/Mapper.php | 13001 | detectado |
| retencion_ganancias_tabla | tabla | INSERT INTO | factory/Mapper.php | 13020 | detectado |
| retencion_ganancias_tabla | tabla | FROM | factory/Mapper.php | 13056 | detectado |
| retiro_socio | tabla | INSERT INTO | factory/Mapper.php | 13074 | detectado |
| retiro_socio | tabla | FROM | factory/Mapper.php | 13118 | detectado |
| funcionalidades_por_rol | tabla | DELETE FROM | factory/Mapper.php | 13149 | detectado |
| roles_por_tipo_notificacion | tabla | INSERT INTO | factory/Mapper.php | 13178 | detectado |
| roles_por_tipo_notificacion | tabla | DELETE FROM | factory/Mapper.php | 13188 | detectado |
| roles_por_usuario_v | tabla | FROM | factory/Mapper.php | 13205 | detectado |
| roles_por_usuario | tabla | INSERT INTO | factory/Mapper.php | 13209 | detectado |
| roles_por_usuario | tabla | DELETE FROM | factory/Mapper.php | 13217 | detectado |
| Grupos_clientes | tabla | INSERT INTO | factory/Mapper.php | 13236 | detectado |
| Grupos_clientes | tabla | DELETE FROM | factory/Mapper.php | 13248 | detectado |
| rubros_iva | tabla | INSERT INTO | factory/Mapper.php | 13266 | detectado |
| rutas_produccion | tabla | INSERT INTO | factory/Mapper.php | 13302 | detectado |
| rutas_produccion | tabla | FROM | factory/Mapper.php | 13325 | detectado |
| Rutas_produccion | tabla | FROM | factory/Mapper.php | 13344 | detectado |
| Pasos_rutas_produccion | tabla | INSERT INTO | factory/Mapper.php | 13345 | detectado |
| secciones_produccion | tabla | INSERT INTO | factory/Mapper.php | 13406 | detectado |
| secciones_produccion | tabla | FROM | factory/Mapper.php | 13455 | detectado |
| solicitud_de_fondos_c | tabla | INSERT INTO | factory/Mapper.php | 13553 | detectado |
| solicitud_de_fondos_c | tabla | FROM | factory/Mapper.php | 13572 | detectado |
| solicitud_de_fondos_d | tabla | INSERT INTO | factory/Mapper.php | 13589 | detectado |
| solicitud_de_fondos_d | tabla | FROM | factory/Mapper.php | 13605 | detectado |
| stock_mp_tabla | tabla | INSERT INTO | factory/Mapper.php | 13673 | detectado |
| sucursales_clientes | tabla | INSERT INTO | factory/Mapper.php | 13730 | detectado |
| sucursales_clientes | tabla | FROM | factory/Mapper.php | 13884 | detectado |
| Tareas_cabecera | tabla | INSERT INTO | factory/Mapper.php | 13903 | detectado |
| Tareas_cabecera | tabla | FROM | factory/Mapper.php | 13960 | detectado |
| Tareas_detalle | tabla | INSERT INTO | factory/Mapper.php | 13979 | detectado |
| Tareas_cabecera | tabla | FROM | factory/Mapper.php | 14046 | detectado |
| tipo_factura | tabla | INSERT INTO | factory/Mapper.php | 14096 | detectado |
| tipo_factura | tabla | FROM | factory/Mapper.php | 14125 | detectado |
| tipos_notificacion | tabla | INSERT INTO | factory/Mapper.php | 14142 | detectado |
| usuarios_por_tipo_notificacion | tabla | DELETE FROM | factory/Mapper.php | 14179 | detectado |
| roles_por_tipo_notificacion | tabla | DELETE FROM | factory/Mapper.php | 14181 | detectado |
| tipos_notificacion | tabla | FROM | factory/Mapper.php | 14189 | detectado |
| periodos_fiscales_tipos | tabla | INSERT INTO | factory/Mapper.php | 14206 | detectado |
| periodos_fiscales_tipos | tabla | FROM | factory/Mapper.php | 14232 | detectado |
| tipo_producto_stock | tabla | INSERT INTO | factory/Mapper.php | 14249 | detectado |
| tipo_producto_stock | tabla | FROM | factory/Mapper.php | 14270 | detectado |
| Tipos_proveedor | tabla | INSERT INTO | factory/Mapper.php | 14287 | detectado |
| Tipos_proveedor | tabla | DELETE FROM | factory/Mapper.php | 14299 | detectado |
| tipo_retencion | tabla | INSERT INTO | factory/Mapper.php | 14317 | detectado |
| tipo_retencion | tabla | DELETE FROM | factory/Mapper.php | 14332 | detectado |
| tipo_retencion | tabla | FROM | factory/Mapper.php | 14335 | detectado |
| transferencia_bancaria_operacion | tabla | INSERT INTO | factory/Mapper.php | 14414 | detectado |
| transferencia_bancaria_operacion | tabla | FROM | factory/Mapper.php | 14450 | detectado |
| transferencia_bancaria_importe | tabla | INSERT INTO | factory/Mapper.php | 14467 | detectado |
| transferencia_bancaria_importe | tabla | FROM | factory/Mapper.php | 14479 | detectado |
| transferencia_interna_d | tabla | INSERT INTO | factory/Mapper.php | 14498 | detectado |
| transferencia_interna_d | tabla | FROM | factory/Mapper.php | 14514 | detectado |
| transferencia_interna_c | tabla | INSERT INTO | factory/Mapper.php | 14534 | detectado |
| transferencia_interna_c | tabla | FROM | factory/Mapper.php | 14559 | detectado |
| Unidades_medida | tabla | INSERT INTO | factory/Mapper.php | 14577 | detectado |
| roles_por_usuario | tabla | DELETE FROM | factory/Mapper.php | 14606 | detectado |
| roles_por_usuario | tabla | DELETE FROM | factory/Mapper.php | 14688 | detectado |
| usuarios_por_almacen_v | tabla | FROM | factory/Mapper.php | 14709 | detectado |
| usuarios_por_almacen | tabla | INSERT INTO | factory/Mapper.php | 14713 | detectado |
| usuarios_por_almacen | tabla | DELETE FROM | factory/Mapper.php | 14721 | detectado |
| usuarios_por_area_empresa_v | tabla | FROM | factory/Mapper.php | 14736 | detectado |
| usuarios_por_area_empresa | tabla | INSERT INTO | factory/Mapper.php | 14740 | detectado |
| usuarios_por_area_empresa | tabla | DELETE FROM | factory/Mapper.php | 14748 | detectado |
| usuarios_por_caja | tabla | INSERT INTO | factory/Mapper.php | 14768 | detectado |
| usuarios_por_caja | tabla | DELETE FROM | factory/Mapper.php | 14776 | detectado |
| usuarios_por_seccion_v | tabla | FROM | factory/Mapper.php | 14790 | detectado |
| usuarios_por_seccion | tabla | INSERT INTO | factory/Mapper.php | 14794 | detectado |
| usuarios_por_seccion | tabla | DELETE FROM | factory/Mapper.php | 14802 | detectado |
| usuarios_por_tipo_notificacion | tabla | INSERT INTO | factory/Mapper.php | 14822 | detectado |
| usuarios_por_tipo_notificacion | tabla | DELETE FROM | factory/Mapper.php | 14832 | detectado |
| venta_cheques_d | tabla | INSERT INTO | factory/Mapper.php | 14862 | detectado |
| venta_cheques_d | tabla | FROM | factory/Mapper.php | 14878 | detectado |
| venta_cheques_c | tabla | INSERT INTO | factory/Mapper.php | 14897 | detectado |
| venta_cheques_c | tabla | FROM | factory/Mapper.php | 14924 | detectado |
| venta_cheques_temporal | tabla | INSERT INTO | factory/Mapper.php | 14942 | detectado |
| venta_cheques_temporal | tabla | FROM | factory/Mapper.php | 14980 | detectado |
| zonas_geo | tabla | INSERT INTO | factory/Mapper.php | 14997 | detectado |

