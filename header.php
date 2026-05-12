<?php global $pagename; $pageactual = explode('/', $pagename); $pageactual = $pageactual[0]; ?>
<?php

$arr =	array(
	'abm' => array('nombre' => 'ABM', 'contenido' => array(
		'clientes' => array('nombre' => 'Clientes'),
		'sucursales' => array('nombre' => 'Sucursales'),
		'proveedores' => array('nombre' => 'Proveedores'),
		'personal' => array('nombre' => 'Personal'),
		'contactos' => array('nombre' => 'Contactos'),
		'transportes' => array('nombre' => 'Transportes'),
		'regiones' => array('nombre' => 'Regiones', 'contenido' => array(
			'paises' => array('nombre' => 'Paises'),
			'provincias' => array('nombre' => 'Provincias'),
			'localidades' => array('nombre' => 'Localidades'))),
		'formas_pago' => array('nombre' => 'Formas de pago'),
		'fajas_horarias' => array('nombre' => 'Fajas horarias'),
		'zonas' => array('nombre' => 'Zonas'),
		'zonas_transporte' => array('nombre' => 'Zonas de transporte'),
		'articulos' => array('nombre' => 'Artículos'),
		'colores_por_articulo' => array('nombre' => 'Colores por artículo'),
		'vendedores' => array('nombre' => 'Vendedores'),
		'curvas' => array('nombre' => 'Curvas'),
		'condiciones_iva' => array('nombre' => 'Condiciones de IVA'),
		'causas_notas_de_credito' => array('nombre' => 'Causas de NCR'),
		'almacenes' => array('nombre' => 'Almacenes'),
		'rubros_iva' => array('nombre' => 'Rubros IVA'),
		'rango_talle' => array('nombre' => 'Rango talle'),
		'impuestos' => array('nombre' => 'Impuestos'),
		'conceptos' => array('nombre' => 'Conceptos'),
		'persona_gasto' => array('nombre' => 'Personas gastos'),
		'tipo_factura' => array('nombre' => 'Tipo factura'),
		'seccion_produccion' => array('nombre' => 'Secciones producción'),
		'temporadas' => array('nombre' => 'Temporadas'),
		'alicuotas_retenciones' => array('nombre' => 'Alicuotas reten.'),
		'areas_empresa' => array('nombre' => 'Áreas empresa'),
		'bancos' => array('nombre' => 'Bancos'),
		'bancos_propios' => array('nombre' => 'Bancos propios'),
		'cuentas_bancarias' => array('nombre' => 'Cuentas bancarias'),
		'cajas' => array('nombre' => 'Cajas'),
		'ejercicios_contables' => array('nombre' => 'Ejercicios contables'))
	),
	'produccion' => array('nombre' => 'PRODUCCION', 'contenido' => array(
		'producto' => array('nombre' => 'Producto', 'contenido' => array(
			'ficha_tecnica'=> array('nombre' => 'Ficha técnica'),
			'reportes'=> array('nombre' => 'Reportes'),
			'patrones'=> array('nombre' => 'Patrones', 'contenido' => array(
				'generacion'=> array('nombre' => 'Generación'),
				'gestion'=> array('nombre' => 'Gestión')
			)))),
		'gestion_produccion' => array('nombre' => 'Gestión de producción', 'contenido' => array(
			'lotes_produccion' => array('nombre' => 'Lotes de producción'),
			'ordenes_produccion' => array('nombre' => 'Órdenes de producción'),
			'cumplido_tareas' => array('nombre' => 'Cumplido de tareas'),
			'confirmacion' => array('nombre' => 'Confirmación stock'),
			'seguimiento' => array('nombre' => 'Seguimiento'),
			'programacion' => array('nombre' => 'Programación'))),
		'guia_de_porte' => array('nombre' => 'Guía de porte'),
		'reportes' => array('nombre' => 'Reportes', 'contenido' => array(
			'programacion_empaque' => array('nombre' => 'Prog. empaque')
		)),
		'compras' => array('nombre' => 'Compras', 'contenido' => array(
			'presupuesto'=> array('nombre' => 'Pedido de cot.', 'contenido' => array(
				'manual' => array('nombre' => 'Ingreso manual'),
				'explosion' => array('nombre' => 'Por explosión'))),
			'ordenes_compra' => array('nombre' => 'Ordenes de compra', 'contenido' => array(
				'generacion' => array('nombre' => 'Generacion OC'),
				'reimpresion' => array('nombre' => 'Reimpresión'),
				'pendiente' => array('nombre' => 'OC pendientes'),
				'descontar_pendiente' => array('nombre' => 'Descontar pendiente'))),
			'reportes' => array('nombre' => 'Reportes', 'contenido' => array(
				'pendientes' => array('nombre' => 'Pendientes'),
				'historico' => array('nombre' => 'Histórico')
			)))),
		'stock' => array('nombre' => 'Stock prod. terminado', 'contenido' => array(
			'ajustes' => array('nombre' => 'Ajustes'),
			'movimientos' => array('nombre' => 'Movimientos'),
			'movimiento_almacen' => array('nombre' => 'Mov. almacén'),
			'confirmacion_movimiento_almacen' => array('nombre' => 'Confirm. mov. almacén'),
			'stock_a_fecha' => array('nombre' => 'Stock a fecha'))),
		'stock_mp' => array('nombre' => 'Stock mat. prima', 'contenido' => array(
			'ajustes' => array('nombre' => 'Ajustes'),
			'movimientos' => array('nombre' => 'Movimientos'),
			'movimiento_almacen' => array('nombre' => 'Mov. almacén'),
			'confirmacion_movimiento_almacen' => array('nombre' => 'Confirm. mov. almacén'),
			'stock_a_fecha' => array('nombre' => 'Stock a fecha'),
			'consumos' => array('nombre' => 'Consumos'))))
	),
	'comercial' => array('nombre' => 'COMERCIAL', 'contenido' => array(
		'pedidos' => array('nombre' => 'Pedidos', 'contenido' => array(
			'nota_de_pedido' => array('nombre' => 'Nota de pedido'),
			'nota_de_pedido_vip' => array('nombre' => 'Nota de pedido VIP'),
			'pendientes' => array('nombre' => 'Pendientes'),
			'historico' => array('nombre' => 'Histórico'),
			'estadisticas' => array('nombre' => 'Estadísticas'),
			'asignacion' => array('nombre' => 'Asignación'),
			'actualizacion_precios' => array('nombre' => 'Act. Precios'))),
		'cuenta_corriente' => array('nombre' => 'Cuenta corriente'),
		'reportes' => array('nombre' => 'Reportes', 'contenido' => array(
			'listado_clientes' => array('nombre' => 'Listado de clientes'),
			'predespachos' => array('nombre' => 'Predespachos'))),
		'predespachos' => array('nombre' => 'Predespachos', 'contenido' => array(
			'generacion' => array('nombre' => 'Generación PREDESP'),
			'reimpresion' => array('nombre' => 'Reimpresión'))),
		'despachos' => array('nombre' => 'Despachos', 'contenido' => array(
			'generacion' => array('nombre' => 'Generación DESP'),
			'reimpresion' => array('nombre' => 'Reimpresión'))),
		'remitos' => array('nombre' => 'Remitos', 'contenido' => array(
			'generacion' => array('nombre' => 'Generación REM'),
			'reimpresion' => array('nombre' => 'Reimpresión'))),
		'facturas' => array('nombre' => 'Facturas', 'contenido' => array(
			'generacion' => array('nombre' => 'Generación FAC'),
			'reimpresion' => array('nombre' => 'Reimpresión'))),
		'notas_de_credito' => array('nombre' => 'Notas de crédito', 'contenido' => array(
			'generacion' => array('nombre' => 'Generación NCR'),
			'reimpresion' => array('nombre' => 'Reimpresión'))),
		'notas_de_debito' => array('nombre' => 'Notas de débito', 'contenido' => array(
			'generacion' => array('nombre' => 'Generación NDB'),
			'reimpresion' => array('nombre' => 'Reimpresión'))),
		'stock' => array('nombre' => 'Stock'),
		'calidad' => array('nombre' => 'Calidad', 'contenido' => array(
			'garantias' => array('nombre' => 'Garantías'),
			'devoluciones_a_clientes' => array('nombre' => 'Dev. a clientes'))),
		'rotulos' => array('nombre' => 'Rótulos'),
		'ecommerce' => array('nombre' => 'Ecommerce', 'contenido' => array(
			'panel_de_control' => array('nombre' => 'Panel de control'),
			'reporte_ventas' => array('nombre' => 'Reporte de ventas'))),
		'vendedores' => array('nombre' => 'Vendedores', 'contenido' => array(
			'reimpresion_documentos' => array('nombre' => 'Reimpresión DOCS'))))
	),
	'administracion' => array('nombre' => 'ADMINISTRACION', 'contenido' => array(
		'rrhh' => array('nombre' => 'RRHH', 'contenido' => array(
			'fichajes' => array('nombre' => 'Fichajes'))),
		'tesoreria' => array('nombre' => 'Tesorería', 'contenido' => array(
			'egresos' => array('nombre' => 'Egresos'),
			'reimpresion_ordenes_de_pago' => array('nombre' => 'Reimpresión OP'),
			'cheques' => array('nombre' => 'Cheques', 'contenido' => array(
				'chequera' => array('nombre' => 'Chequera'),
				'ingreso_cheque_propio' => array('nombre' => 'Ingreso Cheque Propio'),
				'panel_de_control' => array('nombre' => 'Panel de control'),
				'acreditar_cheque' => array('nombre' => 'Acreditar Cheque'),
				'debitar_cheque' => array('nombre' => 'Debitar Cheque'),
				'reportes' => array('nombre' => 'Reportes', 'contenido' => array(
					'seguimiento_cheques' => array('nombre' => 'Seguim. de cheques'),
					'cheques_cartera' => array('nombre' => 'Cheques en cartera'),
					'cheques_propios' => array('nombre' => 'Cheques propios'),
					'cheques_rechazados' => array('nombre' => 'Cheques rechazados'))),
				'venta_cheques' => array('nombre' => 'Venta de cheques', 'contenido' => array(
					'ingreso_venta_cheques' => array('nombre' => 'Ingreso'),
					'reimpresion_venta_cheques' => array('nombre' => 'Reimpresión'))),
				'cobro_cheques_ventanilla' => array('nombre' => 'Cobro cheq. vent.', 'contenido' => array(
					'ingreso_cobro_cheques_ventanilla' => array('nombre' => 'Ingreso'),
					'reimpresion_cobro_cheques_ventanilla' => array('nombre' => 'Reimpresión'))))
			),
			'deposito_bancario' => array('nombre' => 'Deposito bancario', 'contenido' => array(
				'ingreso_deposito_bancario' => array('nombre' => 'Ingreso depósito'),
				'reimpresion_deposito_bancario' => array('nombre' => 'Reimpresión depósito'))),
			'gastos' => array('nombre' => 'Gastos', 'contenido' => array(
				'ingreso_gastos' => array('nombre' => 'Ingreso gastos'),
				'reimpresion_rendicion' => array('nombre' => 'Reimpresión REND'),
				'documento_gastos' => array('nombre' => 'Documento gastos'),
				'aplicacion' => array('nombre' => 'Aplicación'))),
			'reportes' => array('nombre' => 'Reportes', 'contenido' => array(
				'retenciones_efectuadas' => array('nombre' => 'Reten. efectuadas'),
				'egreso_de_fondos' => array('nombre' => 'Egreso de fondos'),
				'subdiario_ingresos' => array('nombre' => 'Subdiario de ingresos'),
				'retiro_aporte_socio' => array('nombre' => 'Retiro/aporte socios'))))
		),
		'proveedores' => array('nombre' => 'Proveedores', 'contenido' => array(
			'listado_proveedores' => array('nombre' => 'Listado proveedores'),
			'documentos_proveedor' => array('nombre' => 'Documentos proveedor'),
			'remitos_proveedor' => array('nombre' => 'Remitos proveedor'),
			'aplicacion' => array('nombre' => 'Aplicación'),
			'cuenta_corriente_proveedor' => array('nombre' => 'Cta. cte. proveedor'),
			'facturacion' => array('nombre' => 'IVA Compras'),
			'gestion_proveedores' => array('nombre' => 'Gestión proveedores'))),
		'cobranzas' => array('nombre' => 'Cobranzas', 'contenido' => array(
			'gestion_cobranza' => array('nombre' => 'Gestión cobranza'),
			'seguimiento_clientes' => array('nombre' => 'Seguimiento clientes'),
			'ingresos' => array('nombre' => 'Ingresos'),
			'reimpresion_recibos' => array('nombre' => 'Reimpresion REC'),
			'aplicacion' => array('nombre' => 'Aplicación'),
			'depositos_pendientes' => array('nombre' => 'Depósitos pend.'),
			'reportes' => array('nombre' => 'Reportes', 'contenido' => array(
				'aplicaciones_pendientes' => array('nombre' => 'Aplicaciones pend.'),
				'comisiones' => array('nombre' => 'Comisiones'))))),
		'finanzas' => array('nombre' => 'Finanzas', 'contenido' => array(
			'reportes' => array('nombre' => 'Reportes', 'contenido' => array(
				'facturacion' => array('nombre' => 'IVA Ventas'),
				'articulo' => array('nombre' => 'Facturación artículo'),
				'cliente' => array('nombre' => 'Facturación cliente'),
				'facturacion_por_jurisdiccion' => array('nombre' => 'Fact. jurisdicción'))))),
		'cajas' => array('nombre' => 'Cajas', 'contenido' => array(
			'movimientos_caja' => array('nombre' => 'Movimientos de caja'),
			'transferencia_interna' => array('nombre' => 'Transf. interna'),
			'resumen_bancario' => array('nombre' => 'Resumen bancario'),
			'saldo_cajas' => array('nombre' => 'Saldo cajas'))),
		'contabilidad' => array('nombre' => 'Contabilidad', 'contenido' => array(
			'asientos_contables' => array('nombre' => 'Asientos contables'),
			'libro_diario' => array('nombre' => 'Libro diario'),
			'consulta_mayores' => array('nombre' => 'Consulta mayores'),
			'sumas_saldos' => array('nombre' => 'Sumas y saldos'),
			'plan_cuentas' => array('nombre' => 'Plan de cuentas'),
			'periodos_fiscales' => array('nombre' => 'Períodos fiscales', 'contenido' => array(
				'cierres' => array('nombre' => 'Cierres'),
				'tipos' => array('nombre' => 'Tipos de período'))),
			'asientos_modelo' => array('nombre' => 'Asientos modelo'))),
		'reportes_gerenciales' => array('nombre' => 'Reportes gerenciales', 'contenido' => array(
			'ventas' => array('nombre' => 'Ventas')
		)))
	),
	'sistema' => array('nombre' => 'SISTEMA', 'contenido' => array(
		'usuarios' => array('nombre' => 'Usuarios', 'contenido' => array(
			'abm' => array('nombre' => 'ABM'),
			'cambiar_contrasena' => array('nombre' => 'Cambiar contraseña'),
			'por_almacen' => array('nombre' => 'Por almacén'),
			'por_seccion' => array('nombre' => 'Por sección'))),
		'roles' => array('nombre' => 'Roles'),
		'formularios' => array('nombre' => 'Formularios'),
		'autorizaciones' => array('nombre' => 'Autorizaciones'),
		'indicadores' => array('nombre' => 'Indicadores'),
		'notificaciones' => array('nombre' => 'Notificaciones', 'contenido' => array(
			'mis_notificaciones' => array('nombre' => 'Mis notificaciones'),
			'notificacion_manual' => array('nombre' => 'Notificación manual'),
			'usuarios_notificados' => array('nombre' => 'Usuarios notifcados'),
			'tipos_de_notificaciones' => array('nombre' => 'Tipos de notificaciones'))),
		'auditoria' => array('nombre' => 'Auditoría', 'contenido' => array(
			'calificacion_clientes' => array('nombre' => 'Calific. clientes'))),
		'avanzado' => array('nombre' => '¡¡PELIGRO!!'),
		'tickets' => array('nombre' => 'Tickets'))
	)
);

function generarUL($arr, $base, $nivel){
	$lis = '';
	foreach ($arr[$nivel]['contenido'] as $newNivel => $contenido) {
		if (Usuario::logueado(true)->puede($base . $nivel . '/' . $newNivel . '/')) {
			$link = isset($contenido['contenido']) ? '#' : Config::siteRoot . $base . $nivel . '/' . $newNivel . '/';
			$uls = isset($contenido['contenido']) ? generarUL($arr[$nivel]['contenido'], $base . $nivel . '/', $newNivel) : '';
			$lis .= '<li><a href="' . $link . '">' . $contenido['nombre'] . '</a>' . $uls . '</li>';
		}
 // ?? Agregar manualmente Cumplido de Tareas con target="_blank"
/* 	   if ($nivel == 'gestion_produccion') {
        $lis .= '<li><a href="http://koi2_v1.spiralshoes.com/cumplido_tareas" target="_blank">Cumplido de Tareas</a></li>';
    }*/

	}
	return '<ul>' . $lis . '</ul>';
} 
?>

<div id='divHeader'>
	<div id='divBarraTop'><a href="#" id="dummyLink"> </a><a href='/'>Inicio</a> - <a href='/logout/'>Cerrar sesión (<?php echo Usuario::logueado(true)->id ?>)</a></div>
	<?php if (!Usuario::logueado(true)->esCliente()) { ?>
	<div id='divMenu' class='classMenu'>
		<ul id='ulMenu'>
			<li>
				<div class='menu_koi'><a href='<? echo Config::siteRoot; ?>'></a></div>
			</li>
			<li>
				<div class='menu_1<?php echo ($pageactual == 'abm' ? ' actual' : '') ?>'><a href="#">ABM</a></div>
				<?php if (Usuario::logueado(true)->puede('abm/')) echo generarUL($arr, '', 'abm'); ?>
			</li>
			<li>
				<div class='menu_2<?php echo ($pageactual == 'produccion' ? ' actual' : '') ?>'><a href="#">PRODUCCIÓN</a></div>
				<?php if (Usuario::logueado(true)->puede('produccion/')) echo generarUL($arr, '', 'produccion'); ?>
			</li>
			<li>
				<div class='menu_3<?php echo ($pageactual == 'comercial' ? ' actual' : '') ?>'><a href="#">COMERCIAL</a></div>
				<?php if (Usuario::logueado(true)->puede('comercial/')) echo generarUL($arr, '', 'comercial'); ?>
			</li>
			<li>
				<div class='menu_4<?php echo ($pageactual == 'administracion' ? ' actual' : '') ?>'><a href="#">ADMINISTRACIÓN</a></div>
				<?php if (Usuario::logueado(true)->puede('administracion/')) echo generarUL($arr, '', 'administracion'); ?>
			</li>
			<li>
				<div class='menu_5<?php echo ($pageactual == 'sistema' ? ' actual' : '') ?>'><a href="#">SISTEMA</a></div>
				<?php if (Usuario::logueado(true)->puede('sistema/')) echo generarUL($arr, '', 'sistema'); ?>
			</li>
			<li>
				<div class='menu_empresa'><img src='/img/menu/menu_empresa_<?php echo Funciones::session('empresa'); ?>.gif'  /></div>
			</li>
		</ul>
	</div>
	<?php } ?>
</div>