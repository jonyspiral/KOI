<?php
?>
<style type='text/css'>
.tituloSolapa {
	font-size: 12px;
}
</style>

<script type='text/javascript'>
	var statusActual = 2;
	$(document).ready(function(){
		$('#divPanelControlEcommerce').fadeIn();
		tituloPrograma = 'Panel de control de Ecommerce';
		cambiarModo('inicio');
		$('.solapas').solapas({fixedHeight: 430, heightSolapas: 45, precall: loadTab, selectedItem: 0});
		$('.btnPasoSiguiente').livequery(function(){
			$(this).click(guardar);
		});
		$('.btnPasoAnterior').livequery(function(){
			$(this).click(borrar);
		});
		$('.btnCambio').livequery(function(){
			$(this).click(clickCambiar);
		});
		$('.btnDevolucion').livequery(function(){
			$(this).click(clickDevolucion);
		});
		$('.inputCantidadCambiar').livequery(function(){
			$(this).change(calcularTotalCambiar);
		});
	});

	function buscar() {
		loadTab($('li[data-statusid].selected'));
	}

	function loadTab(obj) {
		if (!isNaN(obj)) {
			$('li[data-statusid="' + obj + '"]').click();
			return;
		}
		statusActual = obj.data('statusid');
		var div = $('div[data-statusid="' + statusActual + '"]');
		funciones.get(funciones.controllerUrl('buscar', getParams()), {}, function(json){
			$('.spanTotalPedidos').text(funciones.formatearMoneda(0));
			$('.spanCantidadPedidos').text(0);
			$(json.data.tabs).each(function(i, j) {
				$('#spanTotalPedidos_' + j.cod_status).text(funciones.formatearMoneda(j.total));
				$('#spanCantidadPedidos_' + j.cod_status).text(j.cantidad_pedidos);
			});
			if (json.data.msg) {
				$.info(json.data.msg);
			}
			div.html(json.data.html);
			$('#divPanelControlEcommerce').fixedHeader({target: 'table'});
		});
	}

	function hayErrorGuardar(obj){
		return false;
	}

	function guardar(e){
		var obj = armoObjetoGuardar((e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target));
		ajax('agregar', obj, '¿Está seguro que desea mover el pedido Nº ' + obj.orderIdEcommerce + ' de ' + obj.cantidadPares + ' pares al próximo estado?');
	}

	function armoObjetoGuardar(obj){
		return {
			orderId: obj.data('orderid'),
			orderIdEcommerce: obj.data('orderidecommerce'),
			cantidadPares: obj.data('cantidadpares')
		};
	}

	function borrar(e){
		var obj = armoObjetoBorrar((e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target));
		ajax('borrar', obj, '¿Está seguro que desea volver el pedido Nº ' + obj.orderIdEcommerce + ' de ' + obj.cantidadPares + ' pares al estado anterior?');
	}

	function armoObjetoBorrar(obj){
		return {
			orderId: obj.data('orderid'),
			orderIdEcommerce: obj.data('orderidecommerce'),
			cantidadPares: obj.data('cantidadpares')
		};
	}

	function clickCambiar(e){
		var obj = (e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target);
		funciones.get(funciones.controllerUrl('getDetalleOrder'), {orderId: obj.data('orderid')}, function(json) {
			if (json.data.msg) {
				$.info(json.data.msg);
			} else {
				var botones = [{value: 'Guardar', action: function(){goCambiar();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
				$.jPopUp.show(json.data.html, botones);
				calcularTotalCambiar();
			}
		});
	}

	function goCambiar() {
		var obj = {
			orderId: 0,
			detalles: {}
		};
		$('.inputCantidadCambiar').each(function(k, val) {
			if (k == 0) {
				obj.orderId = $(val).data('orderid');
			}
			obj.detalles[$(val).data('detailid')] = $(val).val();
		});
		funciones.guardar(funciones.controllerUrl('generarCupon'), obj, function() {
			$.jPopUp.close();
			funciones.delay('$.showLoading()', 200);
			loadTab(this.data.statusId)
		}, null, null, false);
	}

	function clickDevolucion(e){
		var obj = armoObjetoGuardar((e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target));
		ajax('devolucion', obj, '¿Está seguro que desea mover el pedido Nº ' + obj.orderIdEcommerce + ' de ' + obj.cantidadPares + ' pares al estado de devolución?');
	}

	function calcularTotalCambiar() {
		$('#totalCantidad').text(0);
		$('#totalSubtotal').text(funciones.formatearMoneda(0));
		$('.inputCantidadCambiar').each(function(i, input) {
			$('#totalCantidad').text(funciones.toInt($('#totalCantidad').text()) + funciones.toInt($(input).val()));
			$('#subtotal_' + $(input).data('detailid')).text(funciones.formatearMoneda($(input).val() * $(input).data('price')));
			$('#totalSubtotal').text(funciones.formatearMoneda(funciones.toFloat($('#totalSubtotal').text()) + funciones.toFloat($(input).val() * $(input).data('price'))));
		});
	}

	function ajax(controller, obj, msg) {
		var error = hayErrorGuardar(obj);
		if (!error) {
			$.confirm(msg, function(r) {
				if (r == funciones.si) {
					var url = funciones.controllerUrl(controller);
					funciones.guardar(url, obj, function() {
						funciones.delay('$.showLoading()', 200);
						loadTab(this.data.statusId)
					}, null, null, false);
				}
			});
		} else {
			$.error(error);
		}
	}

	function getParams(){
		return {
				idStatus: statusActual,
				fechaDesde: $('#inputBuscarFechaDesde').val(),
				fechaHasta: $('#inputBuscarFechaHasta').val(),
				one: '0',
				orden: $('#inputOrden').val()
		}
	}

	function pdfClick(){
		funciones.pdfClick(funciones.controllerUrl('getPdf', getParams()));
	}

	function xlsClick(){
		funciones.xlsClick(funciones.controllerUrl('getXls'));
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#btnXls').show();
				$('#btnPdf').show();
				break;
			case 'buscar':
				funciones.cambiarTitulo();
				$('#btnBuscar').show();
				break;
			case 'editar':
				break;
			case 'agregar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divPanelControlEcommerce' class='w100p customScroll h480' style="display: none;">
		<div class="solapas">
			<?php
			$lis = '';
			$divs = '';
			$where = 'anulado = ' . Datos::objectToDB('N') .  ' AND mostrar_en_panel = ' . Datos::objectToDB('S') . ' ORDER BY orden_para_mostrar ASC, cod_status ASC';
			$estados = Factory::getInstance()->getListObject('Ecommerce_OrderStatus', $where);
			foreach($estados as $estado){
				$lis .= '<li data-statusid="' . $estado->id . '">' . Funciones::toUpper($estado->nombre) . '<br><span id="spanCantidadPedidos_' . $estado->id . '" class="spanCantidadPedidos">0</span> pedidos - <span id="spanTotalPedidos_' . $estado->id . '" class="spanTotalPedidos">0,00</span></li>';
				$divs .= '<div data-statusid="' . $estado->id . '"></div>';
			}
			echo '<ul>' . $lis . '</ul><div>' . $divs . '</div>';
			?>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Rango fecha pedido:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar w80' to='inputBuscarFechaHasta' validate='Fecha' />
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar w80' from='inputBuscarFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w220'>
				<option value='0'>Fecha de pedido descendente</option>
				<option value='1'>Fecha de pedido ascendente</option>
				<option value='2'>Importe del pedido ascendente</option>
				<option value='3'>Importe del pedido descendente</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php //Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
	</div>
</div>
