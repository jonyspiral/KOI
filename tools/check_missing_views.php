<?php
/**
 * PHP 5.6 compatible.
 * Usage:
 *   php check_missing_views.php
 *
 * Compara las vistas del CSV (ya embebidas aquí) contra MySQL (information_schema.VIEWS)
 * y lista cuáles faltan en MySQL (case-sensitive).
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ==== CONFIG MYSQL (KOI2) ====
define('MYSQL_HOST', '192.168.2.210');
define('MYSQL_PORT', 3306);
define('MYSQL_USER', 'koiuser');
define('MYSQL_PASS', 'Route667?');
define('MYSQL_DB',   'koi1_stage');   // cambiar a 'koi2_v1' si corresponde
define('MYSQL_CHARSET', 'utf8');

$viewList = array(
    'Marcas',
    'Ordenes_compra_detalle_v',
    'Pedidos_rentabilidad_v',
    'Stock_pt_detalle',
    'Stock_pt_real_valorizado_v',
    'almacenes_por_seccion_v',
    'articulos_imagenes_v',
    'asignados_por_tarea',
    'banco_propio_v',
    'caja_v',
    'cajas_posibles_transferencia_interna_v',
    'cajas_resumen',
    'cheque_v',
    'chequera_v',
    'cheques_propios_v',
    'cheques_rechazados_v',
    'clientes_v',
    'colores_por_articulo_v',
    'compras_pendientes',
    'compras_vw',
    'consumos_comprometidos_v',
    'costo_factura_total_v',
    'costo_mp_factura_24meses_v',
    'costo_mp_factura_v',
    'costo_mp_producto_V',
    'costo_mp_producto_detalle_v',
    'costo_mp_semielaborado_detalle_v',
    'costo_producto_total_V',
    'costos_fijos_periodo_v',
    'costos_fijos_periodo_vig_v',
    'cuenta_corriente_historica',
    'cuenta_corriente_historica_proveedor',
    'cuenta_corriente_historica_proveedor_v',
    'despachos_d_v',
    'despachos_v',
    'documento_proveedor',
    'documento_proveedor_aplicacion_debe_v',
    'documento_proveedor_aplicacion_haber_v',
    'documento_proveedor_aplicacion_v',
    'documento_proveedor_h_v',
    'documentos',
    'documentos_aplicacion_debe_v',
    'documentos_aplicacion_haber_v',
    'documentos_aplicacion_v',
    'documentos_cantidades',
    'documentos_d_v',
    'documentos_h_v',
    'documentos_rentabilidad',
    'documentos_vendedor',
    'ecomexperts_articulos_update_v',
    'ecommerce_colores_por_articulo_v',
    'ecommerce_orders_v',
    'egreso_de_fondos_v',
    'egresos_compras_gastos_vw',
    'egresos_ff_vw',
    'egresos_op_vw',
    'egresos_vw',
    'facturacion_cantidades_por_articulo_v',
    'facturas_con_saldo_pendientexxx',
    'facturas_detalle',
    'fasonier_v',
    'ficha_tecnica_patrones_d',
    'fichajes_v',
    'filas_asientos_contables_v',
    'gastos_vw',
    'gestion_proveedores',
    'gestion_proveedores_1',
    'gestion_proveedores_2',
    'importe_por_operacion_d_v',
    'importes_op_acumulado_mes',
    'indicadores_por_rol_v',
    'ingresos_v',
    'koi_ticket_v',
    'lineas_productos',
    'listado_clientes_v',
    'listado_proveedores_v',
    'materiales_preferentes_v',
    'materiales_v',
    'materias_primas_v',
    'movimientos_caja_v',
    'movimientos_caja_v_anul',
    'movimientos_caja_v_chq',
    'movimientos_caja_v_noanul',
    'movimientos_stock_mp_v',
    'movimientos_stock_v',
    'mp_consumos_facon_vw',
    'mp_consumos_vw',
    'mp_mov_extraor_vw',
    'mp_remitos_vw',
    'mp_stock_agrupado_vw',
    'mp_stock_detallado_vw',
    'mundial',
    'mundial_detalle',
    'notificaciones_por_usuario_v',
    'operadores_v',
    'ordenes_compra_cabecera_v',
    'patrones_se_vigentes_v',
    'patrones_v',
    'patrones_vigentes_v',
    'pedidos_clientes_articulo_vendedor',
    'pedidos_clientes_sin_mora_vw',
    'pedidos_d_v',
    'pendientes_aplicacion_clientes_v',
    'permisos_por_usuarios_por_caja_v',
    'predespachos_v',
    'presentismo',
    'prestashop_articulos_update_v',
    'prestashop_imagenes_update',
    'programacion_empaque_v',
    'promedio_tolerancia_pagados',
    'proveedor_v',
    'proveedores_materias_primas_v',
    'proveedores_plazo_pago',
    'proveedores_v',
    'remitos_c_v',
    'reporte_articulos_v',
    'reporte_facturacion_proveedores_v',
    'reporte_facturacion_v',
    'resumen_bancario_v',
    'retenciones_efectuadas_v',
    'roles_por_tipo_notificacion_v',
    'roles_por_usuario_v',
    'saldos_historicos_proveedor',
    'stock01_por_talle_v',
    'stock14_por_talle_v',
    'stock14y20_por_talle_v',
    'stock20_por_talle_v',
    'stock_01_14_20_por_talle_v',
    'stock_asignados',
    'stock_comprometido_mp_v',
    'stock_disponible_total_v',
    'stock_menos_asignado_vw',
    'stock_menos_pendiente_vw',
    'stock_menos_pendiente_vw_original',
    'stock_mp',
    'stock_mp_detalle',
    'stock_mp_rango',
    'stock_mp_sin_rango_ a_eliminar',
    'stock_mp_vw',
    'stock_pedidos_pendientes',
    'stock_pedidos_pendientes_cliente_v',
    'stock_pedidos_pendientes_con_estado_v',
    'stock_pedidos_predespachados',
    'stock_pedidos_sin_predespachar',
    'stock_produccion_incumplida_v',
    'stock_pt',
    'stock_pt_real_v',
    'stock_registros_aux_vw',
    'subdiario_de_ingresos_v',
    'sucursales_v',
    'sysconstraints',
    'syssegments',
    'tareas_cabecera_v',
    'tareas_detalle_v',
    'tareas_incumplidas_empaque_v',
    'tareas_incumplidas_v',
    'tareas_sin empaque_v',
    'tranferencias_materias_primas_v',
    'ultimos_precios_mp',
    'usuarios_por_almacen_v',
    'usuarios_por_area_empresa_v',
    'usuarios_por_caja_v',
    'usuarios_por_seccion_v',
    'usuarios_por_tipo_notificacion_v',
    'valores_en_cartera_v',
    'view_prueba_v'
);

// Conectar MySQL
$mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT);
if ($mysqli->connect_errno) { fwrite(STDERR, "MySQL connect error ({$mysqli->connect_errno}): {$mysqli->connect_error}\n"); exit(1); }
$mysqli->set_charset(MYSQL_CHARSET);

// Obtener vistas reales
$sql = "SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ? ORDER BY BINARY TABLE_NAME";
$stmt = $mysqli->prepare($sql);
if (!$stmt) { fwrite(STDERR, "Prepare error: {$mysqli->error}\n"); exit(1); }
$db = MYSQL_DB;
$stmt->bind_param('s', $db);
$stmt->execute();
$res = $stmt->get_result();

$mysqlViews = array();
while ($row = $res->fetch_assoc()) { $mysqlViews[] = $row['TABLE_NAME']; }
$stmt->close(); $mysqli->close();

// Índices para comparación case-sensitive
$setMy = array();
foreach ($mysqlViews as $m) { $setMy[$m] = true; }

$missing = array();
$present = array();
foreach ($viewList as $v) {
    if (!isset($setMy[$v])) $missing[] = $v; else $present[] = $v;
}

echo "Total vistas CSV(normalizado): ".count($viewList)."\n";
echo "Total vistas en MySQL (" . MYSQL_DB . "): ".count($mysqlViews)."\n";
echo "Coinciden: ".count($present)."\n\n";

if ($missing) {
    echo "-- FALTAN EN MySQL --\n";
    foreach ($missing as $v) echo $v."\n";
    echo "\n";
} else {
    echo "-- No faltan vistas en MySQL. ✅ --\n\n";
}

if (false) {
    // Debug opcional: listar sobrantes (vistas que están en MySQL pero no en CSV)
    $setCsv = array();
    foreach ($viewList as $v) $setCsv[$v] = true;
    $extra = array();
    foreach ($mysqlViews as $m) if (!isset($setCsv[$m])) $extra[] = $m;
    if ($extra) {
        echo "-- SOBRANTES EN MySQL --\n";
        foreach ($extra as $e) echo $e."\n";
        echo "\n";
    }
}
