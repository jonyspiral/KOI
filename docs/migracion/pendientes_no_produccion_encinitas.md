# Pendientes No Produccion - Encinitas

Fecha de analisis: 2026-06-30
Origen obligatorio: `encinitas`
Objetivo: relevar tablas, views, procedures y functions pendientes para operar modulos no productivos sobre SQL Server.

## Criterio aplicado

- Base principal: `docs/migracion/inventario_db_app.md` y `docs/migracion/referencias_db_app.csv`.
- Exclusiones estrictas: todo objeto presente en `docs/migracion/produccion_operativa_spiral.md/.csv`, referencias bajo `content/produccion/` y objetos vinculados a ordenes de fabricacion, tareas, rutas, patrones, stock de produccion, compras productivas y reportes productivos.
- Complemento de evidencia: barrido de llamadas literales a `getArrayFromView()` y `getArrayFromStoredProcedure()` fuera de `content/produccion/`, para recuperar views y procedures no nominados en el CSV base.
- Prioridad: `CRITICA` para circuitos transaccionales o procedures activos; `ALTA` para maestros/seguridad con escritura; `MEDIA` para consultas/reportes; `BAJA` para soporte residual.

## Resumen

- Objetos relevados: **121**
- Comercial y clientes: **9**
- Ventas y documentos: **15**
- Compras no productivas: **9**
- Tesorería: **35**
- E-commerce: **11**
- Administración: **11**
- Seguridad, usuarios y notificaciones: **15**
- Contabilidad: **14**
- Soporte / revisar manualmente: **2**

## Detalle

| modulo | nombre SQL exacto | tipo | operaciones detectadas | archivo y linea | prioridad | dependencias previas | origen | propuesta de etapa de migracion |
|---|---|---|---|---|---|---|---|---|
| Comercial y clientes | cambios_situacion_cliente | tabla | insercion, lectura | factory/Mapper.php:4964; factory/Mapper.php:4982 | ALTA | Maestros comerciales y de clientes relacionados. | encinitas | Etapa 2 - Operacion comercial |
| Comercial y clientes | curvas_por_articulo | tabla | borrado, insercion | factory/Mapper.php:5726; factory/Mapper.php:6288; factory/Mapper.php:6298 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 2 - Operacion comercial |
| Comercial y clientes | gestiones_clientes_cobranza | tabla | insercion, lectura | factory/Mapper.php:9024; factory/Mapper.php:9057 | ALTA | Maestros comerciales y de clientes relacionados. | encinitas | Etapa 2 - Operacion comercial |
| Comercial y clientes | Grupos_clientes | tabla | borrado, insercion | factory/Mapper.php:13236; factory/Mapper.php:13248 | ALTA | Maestros comerciales y de clientes relacionados. | encinitas | Etapa 2 - Operacion comercial |
| Comercial y clientes | lineas_productos | tabla | insercion, lectura | factory/Mapper.php:9637; factory/Mapper.php:9673 | ALTA | Maestros comerciales y de clientes relacionados. | encinitas | Etapa 2 - Operacion comercial |
| Comercial y clientes | rango_talles | tabla | insercion, lectura | factory/Mapper.php:12273; factory/Mapper.php:12309 | ALTA | Maestros comerciales y de clientes relacionados. | encinitas | Etapa 2 - Operacion comercial |
| Comercial y clientes | sucursales_clientes | tabla | insercion, lectura | factory/Mapper.php:13730; factory/Mapper.php:13884 | ALTA | Maestros comerciales y de clientes relacionados. | encinitas | Etapa 2 - Operacion comercial |
| Comercial y clientes | zonas_geo | tabla | insercion | factory/Mapper.php:14997 | ALTA | Maestros comerciales y de clientes relacionados. | encinitas | Etapa 2 - Operacion comercial |
| Comercial y clientes | color_por_articulo | tabla | lectura | clases/ColorPorArticulo.php:420 | MEDIA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | despachos_c | tabla | insercion, lectura | factory/Mapper.php:6519; factory/Mapper.php:6565 | CRITICA | Clientes, tipos de comprobante y cabeceras relacionadas. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | despachos_d | tabla | insercion | factory/Mapper.php:6583 | CRITICA | despachos_c | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | devoluciones_a_cliente_c | tabla | insercion, lectura | factory/Mapper.php:6671; factory/Mapper.php:6698 | CRITICA | Clientes, tipos de comprobante y cabeceras relacionadas. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | devoluciones_a_cliente_d | tabla | insercion | factory/Mapper.php:6715 | CRITICA | devoluciones_a_cliente_c | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | documentos_c | tabla | insercion, lectura | factory/Mapper.php:8214; factory/Mapper.php:8333; factory/Mapper.php:10194; factory/Mapper.php:10300; factory/Mapper.php:10322; factory/Mapper.php:10414 | CRITICA | Clientes, tipos de comprobante y cabeceras relacionadas. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | documentos_d | tabla | borrado, insercion | factory/Mapper.php:7069; factory/Mapper.php:7115 | CRITICA | documentos_c | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | documentos_h | tabla | borrado, insercion, lectura | factory/Mapper.php:7013; factory/Mapper.php:7044; factory/Mapper.php:7047 | CRITICA | documentos_c | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | garantias_c | tabla | insercion | factory/Mapper.php:8922 | CRITICA | Clientes, tipos de comprobante y cabeceras relacionadas. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | garantias_d | tabla | insercion, lectura | factory/Mapper.php:8977; factory/Mapper.php:9007 | CRITICA | garantias_c | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | notas_credito_causalidades | tabla | borrado, insercion | factory/Mapper.php:5039; factory/Mapper.php:5049 | CRITICA | Clientes, tipos de comprobante y cabeceras relacionadas. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | pedidos_c | tabla | insercion, lectura | factory/Mapper.php:11254; factory/Mapper.php:11320 | CRITICA | Clientes, tipos de comprobante y cabeceras relacionadas. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | pedidos_d | tabla | borrado, insercion | factory/Mapper.php:11296; factory/Mapper.php:11338 | CRITICA | pedidos_c | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | remitos_c | tabla | insercion, lectura | factory/Mapper.php:12541; factory/Mapper.php:12598 | CRITICA | Clientes, tipos de comprobante y cabeceras relacionadas. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | tipo_factura | tabla | insercion, lectura | factory/Mapper.php:14096; factory/Mapper.php:14125 | CRITICA | Clientes, tipos de comprobante y cabeceras relacionadas. | encinitas | Etapa 2 - Operacion comercial |
| Ventas y documentos | pedidos_d_v | view | lectura | content/sistema/indicadores/index.php:152 | MEDIA | Tablas base del reporte/view; revisar definicion SQL en encinitas. | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | documento_gasto_datos | tabla | insercion, lectura | factory/Mapper.php:6921; factory/Mapper.php:6969 | CRITICA | Proveedores, tipos e impuestos asociados. | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | documento_proveedor_c | tabla | insercion, lectura | factory/Mapper.php:7138; factory/Mapper.php:7221 | CRITICA | Proveedores, tipos e impuestos asociados. | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | documento_proveedor_d | tabla | insercion, lectura | factory/Mapper.php:7387; factory/Mapper.php:7445 | CRITICA | documento_proveedor_c | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | documento_proveedor_h | tabla | borrado, insercion | factory/Mapper.php:7347; factory/Mapper.php:7366 | CRITICA | documento_proveedor_c | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | impuesto_por_documento_proveedor | tabla | borrado, insercion | factory/Mapper.php:9425; factory/Mapper.php:9444 | CRITICA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | proveedores_datos | tabla | insercion, lectura | factory/Mapper.php:8399; factory/Mapper.php:8546; factory/Mapper.php:12006; factory/Mapper.php:12150 | CRITICA | Proveedores, tipos e impuestos asociados. | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | remitos_proveedor_cabecera | tabla | borrado, insercion, lectura | factory/Mapper.php:12673; factory/Mapper.php:12709; factory/Mapper.php:12712 | CRITICA | Proveedores, tipos e impuestos asociados. | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | remitos_proveedor_detalle | tabla | borrado, insercion, lectura | factory/Mapper.php:12707; factory/Mapper.php:12730; factory/Mapper.php:12769; factory/Mapper.php:12773 | CRITICA | remitos_proveedor_cabecera | encinitas | Etapa 2 - Operacion comercial |
| Compras no productivas | Tipos_proveedor | tabla | borrado, insercion | factory/Mapper.php:14287; factory/Mapper.php:14299 | CRITICA | Proveedores, tipos e impuestos asociados. | encinitas | Etapa 2 - Operacion comercial |
| Tesorería | acreditar_debitar_cheque_c | tabla | insercion, lectura | factory/Mapper.php:4086; factory/Mapper.php:4108 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | acreditar_debitar_cheque_d | tabla | insercion, lectura | factory/Mapper.php:4051; factory/Mapper.php:4067 | CRITICA | acreditar_debitar_cheque_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | aporte_socio | tabla | insercion, lectura | factory/Mapper.php:4285; factory/Mapper.php:4329 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | banco_propio | tabla | insercion, lectura | factory/Mapper.php:4793; factory/Mapper.php:4852 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | cajas_posibles_transferencia_interna | tabla | borrado, insercion | factory/Mapper.php:4937; factory/Mapper.php:4946 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | chequera_c | tabla | insercion, lectura | factory/Mapper.php:5206; factory/Mapper.php:5231 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | chequera_d | tabla | borrado, insercion, lectura | factory/Mapper.php:5248; factory/Mapper.php:5262; factory/Mapper.php:5265 | CRITICA | chequera_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | cobro_cheque_ventanilla_c | tabla | insercion, lectura | factory/Mapper.php:5520; factory/Mapper.php:5549 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | cobro_cheque_ventanilla_d | tabla | insercion, lectura | factory/Mapper.php:5485; factory/Mapper.php:5501 | CRITICA | cobro_cheque_ventanilla_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | cobro_cheque_ventanilla_temporal | tabla | insercion, lectura | factory/Mapper.php:5567; factory/Mapper.php:5603 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | cuenta_bancaria | tabla | insercion, lectura | factory/Mapper.php:6114; factory/Mapper.php:6152 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | deposito_bancario_c | tabla | insercion, lectura | factory/Mapper.php:6407; factory/Mapper.php:6438 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | deposito_bancario_d | tabla | insercion, lectura | factory/Mapper.php:6372; factory/Mapper.php:6388 | CRITICA | deposito_bancario_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | deposito_bancario_temporal | tabla | insercion, lectura | factory/Mapper.php:6456; factory/Mapper.php:6502 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | importe_por_operacion_c | tabla | insercion, lectura | factory/Mapper.php:9251; factory/Mapper.php:9267 | CRITICA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | importe_por_operacion_d | tabla | insercion | factory/Mapper.php:9286 | CRITICA | importe_por_operacion_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | ingreso_cheque_propio | tabla | insercion, lectura | factory/Mapper.php:9555; factory/Mapper.php:9573 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | orden_de_pago | tabla | insercion, lectura | factory/Mapper.php:10936; factory/Mapper.php:10994 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | permisos_por_caja | tabla | borrado, insercion | factory/Mapper.php:11403; factory/Mapper.php:11411 | CRITICA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | permisos_por_usuarios_por_caja | tabla | borrado, insercion | factory/Mapper.php:11431; factory/Mapper.php:11442 | CRITICA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | rechazo_de_cheque_c | tabla | insercion, lectura | factory/Mapper.php:12364; factory/Mapper.php:12392 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | rechazo_de_cheque_d | tabla | insercion, lectura | factory/Mapper.php:12328; factory/Mapper.php:12344 | CRITICA | rechazo_de_cheque_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | reingreso_cheque_cartera | tabla | insercion, lectura | factory/Mapper.php:12493; factory/Mapper.php:12521 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | rendicion_de_gastos | tabla | insercion, lectura | factory/Mapper.php:12792; factory/Mapper.php:12833 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | retiro_socio | tabla | insercion, lectura | factory/Mapper.php:13074; factory/Mapper.php:13118 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | solicitud_de_fondos_c | tabla | insercion, lectura | factory/Mapper.php:13553; factory/Mapper.php:13572 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | solicitud_de_fondos_d | tabla | insercion, lectura | factory/Mapper.php:13589; factory/Mapper.php:13605 | CRITICA | solicitud_de_fondos_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | transferencia_bancaria_importe | tabla | insercion, lectura | factory/Mapper.php:14467; factory/Mapper.php:14479 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | transferencia_bancaria_operacion | tabla | insercion, lectura | factory/Mapper.php:14414; factory/Mapper.php:14450 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | transferencia_interna_c | tabla | insercion, lectura | factory/Mapper.php:14534; factory/Mapper.php:14559 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | transferencia_interna_d | tabla | insercion, lectura | factory/Mapper.php:14498; factory/Mapper.php:14514 | CRITICA | transferencia_interna_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | usuarios_por_caja | tabla | borrado, insercion | factory/Mapper.php:14768; factory/Mapper.php:14776 | CRITICA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | venta_cheques_c | tabla | insercion, lectura | factory/Mapper.php:14897; factory/Mapper.php:14924 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | venta_cheques_d | tabla | insercion, lectura | factory/Mapper.php:14862; factory/Mapper.php:14878 | CRITICA | venta_cheques_c | encinitas | Etapa 3 - Finanzas y cierres |
| Tesorería | venta_cheques_temporal | tabla | insercion, lectura | factory/Mapper.php:14942; factory/Mapper.php:14980 | CRITICA | Maestros de caja/banco/cliente/proveedor segun el circuito. | encinitas | Etapa 3 - Finanzas y cierres |
| E-commerce | ecommerce_coupons | tabla | insercion, lectura | factory/Mapper.php:7463; factory/Mapper.php:7504 | ALTA | Catalogo comercial, clientes y estados/medios de pago del canal. | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_customers | tabla | insercion, lectura | factory/Mapper.php:7521; factory/Mapper.php:7570 | ALTA | Catalogo comercial, clientes y estados/medios de pago del canal. | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_deliverys | tabla | insercion, lectura | factory/Mapper.php:7587; factory/Mapper.php:7626 | ALTA | ecommerce_orders | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_order_details | tabla | insercion, lectura | factory/Mapper.php:7714; factory/Mapper.php:7747 | ALTA | ecommerce_orders, ecommerce_customers, ecommerce_order_status | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_order_status | tabla | insercion, lectura | factory/Mapper.php:7764; factory/Mapper.php:7802 | ALTA | Catalogo comercial, clientes y estados/medios de pago del canal. | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_orders | tabla | insercion, lectura | factory/Mapper.php:7643; factory/Mapper.php:7697 | ALTA | Catalogo comercial, clientes y estados/medios de pago del canal. | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_payment_methods | tabla | insercion, lectura | factory/Mapper.php:7907; factory/Mapper.php:7933 | ALTA | Catalogo comercial, clientes y estados/medios de pago del canal. | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_payments | tabla | insercion, lectura | factory/Mapper.php:7852; factory/Mapper.php:7890 | ALTA | ecommerce_orders | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_servicios_andreani | tabla | insercion, lectura | factory/Mapper.php:7950; factory/Mapper.php:7979 | ALTA | Catalogo comercial, clientes y estados/medios de pago del canal. | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ecommerce_usergroups | tabla | insercion, lectura | factory/Mapper.php:7996; factory/Mapper.php:8025 | ALTA | Catalogo comercial, clientes y estados/medios de pago del canal. | encinitas | Etapa 2 - Operacion comercial |
| E-commerce | ruta_imagenes | view | lectura | clases/ManejadorDeImagenes.php:11 | MEDIA | Catalogo comercial, clientes y estados/medios de pago del canal. | encinitas | Etapa 2 - Operacion comercial |
| Administración | areas_empresa | tabla | insercion, lectura | factory/Mapper.php:4347; factory/Mapper.php:4378 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | condiciones_iva | tabla | insercion | factory/Mapper.php:5845 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | Forecast_detalle | tabla | borrado, insercion | factory/Mapper.php:8680; factory/Mapper.php:8713 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | Forecast_encabezado | tabla | insercion | factory/Mapper.php:8662 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | Formas_pago | tabla | insercion | factory/Mapper.php:8750 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | grupo_empresa | tabla | insercion | factory/Mapper.php:9074 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | horarios_por_secciones | tabla | insercion, lectura | factory/Mapper.php:8357; factory/Mapper.php:8382 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Administración | motivos_ausentismo | tabla | insercion, lectura | factory/Mapper.php:9843; factory/Mapper.php:9869 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | persona_gasto | tabla | insercion, lectura | factory/Mapper.php:11460; factory/Mapper.php:11486 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | registro_entradas_salidas | tabla | borrado, insercion, lectura | factory/Mapper.php:8563; factory/Mapper.php:8595; factory/Mapper.php:8598 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Administración | rubros_iva | tabla | insercion | factory/Mapper.php:13266 | ALTA | Maestros administrativos y parametrizacion general. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | autorizaciones_personas | tabla | borrado, insercion | factory/Mapper.php:4688; factory/Mapper.php:4699 | ALTA | Usuarios, roles y catalogos transversales del sistema. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | autorizaciones_tipos | tabla | insercion | factory/Mapper.php:4719 | ALTA | Usuarios, roles y catalogos transversales del sistema. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | categorias_calzado_usuarios | tabla | insercion | factory/Mapper.php:4999 | ALTA | Usuarios, roles y catalogos transversales del sistema. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | funcionalidades_por_rol | tabla | borrado, insercion | factory/Mapper.php:8839; factory/Mapper.php:8848; factory/Mapper.php:13149 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | indicadores_por_rol | tabla | borrado, insercion | factory/Mapper.php:9491; factory/Mapper.php:9529 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | koi_sessions | tabla | actualizacion, insercion, lectura | clases/UsuarioLogin.php:55 | ALTA | Usuarios, roles y catalogos transversales del sistema. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | notificaciones_por_usuario | tabla | insercion | factory/Mapper.php:10485 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | roles_por_tipo_notificacion | tabla | borrado, insercion | factory/Mapper.php:13178; factory/Mapper.php:13188; factory/Mapper.php:14181 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | roles_por_usuario | tabla | borrado, insercion | factory/Mapper.php:13209; factory/Mapper.php:13217; factory/Mapper.php:14606; factory/Mapper.php:14688 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | tipos_notificacion | tabla | insercion, lectura | factory/Mapper.php:14142; factory/Mapper.php:14189 | ALTA | Usuarios, roles y catalogos transversales del sistema. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | usuarios_por_area_empresa | tabla | borrado, insercion | factory/Mapper.php:4363; factory/Mapper.php:14740; factory/Mapper.php:14748 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | usuarios_por_tipo_notificacion | tabla | borrado, insercion | factory/Mapper.php:14179; factory/Mapper.php:14822; factory/Mapper.php:14832 | ALTA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | roles_por_usuario_v | view | lectura | factory/Mapper.php:13205 | MEDIA | roles_por_usuario y catalogo de roles del sistema | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | usuarios_por_almacen_v | view | lectura | factory/Mapper.php:14709 | MEDIA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Seguridad, usuarios y notificaciones | usuarios_por_area_empresa_v | view | lectura | factory/Mapper.php:14736 | MEDIA | Tablas maestras relacionadas por clave foranea o codigo funcional. | encinitas | Etapa 1 - Base transversal |
| Contabilidad | asientos_contables | tabla | insercion, lectura | factory/Mapper.php:4503; factory/Mapper.php:4542 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | asientos_modelo_c | tabla | insercion, lectura | factory/Mapper.php:4559; factory/Mapper.php:4587 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | asientos_modelo_d | tabla | borrado, insercion | factory/Mapper.php:4573; factory/Mapper.php:4605 | ALTA | asientos_modelo_c | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | ejercicios_contables | tabla | insercion, lectura | factory/Mapper.php:8084; factory/Mapper.php:8116 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | filas_asientos_contables | tabla | borrado, insercion | factory/Mapper.php:4525; factory/Mapper.php:8616 | ALTA | asientos_contables, plan_cuentas | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | parametros_contabilidad | tabla | insercion | factory/Mapper.php:11079 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | periodos_fiscales_cierres | tabla | insercion, lectura | factory/Mapper.php:5069; factory/Mapper.php:5101 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | periodos_fiscales_tipos | tabla | insercion, lectura | factory/Mapper.php:14206; factory/Mapper.php:14232 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | plan_cuentas | tabla | insercion | factory/Mapper.php:9320 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | retencion_efectuada | tabla | insercion, lectura | factory/Mapper.php:12851; factory/Mapper.php:12893 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | retencion_ganancias_tabla | tabla | insercion, lectura | factory/Mapper.php:13020; factory/Mapper.php:13056 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | retencion_sufrida | tabla | insercion, lectura | factory/Mapper.php:12962; factory/Mapper.php:13001 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | retenciones_ganancias_honorarios | tabla | insercion, lectura | factory/Mapper.php:12912; factory/Mapper.php:12945 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Contabilidad | tipo_retencion | tabla | borrado, insercion, lectura | factory/Mapper.php:14317; factory/Mapper.php:14332; factory/Mapper.php:14335 | ALTA | Maestros contables y parametros fiscales previos. | encinitas | Etapa 3 - Finanzas y cierres |
| Soporte / revisar manualmente | REVISAR_MANUALMENTE | procedure | llamada | factory/Datos.php:124; factory/Datos.php:137; factory/Datos.php:138; factory/Datos.php:143; factory/Datos.php:144; factory/Factory.php:123; +1 refs | ALTA | Resolver el nombre real del stored procedure en llamadas dinamicas antes de migrar. | encinitas | Etapa 4 - Ajustes y revision manual |
| Soporte / revisar manualmente | koi_ticket | tabla | insercion, lectura | factory/Mapper.php:9126; factory/Mapper.php:9171 | MEDIA | Revisar dependencias exactas en esquema encinitas. | encinitas | Etapa 4 - Ajustes y revision manual |
