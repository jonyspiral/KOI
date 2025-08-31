<?php
$iva21 = Impuestos::iva21;
?>
<script type='text/javascript'>
	$(document).ready(function() {
		detalle = [];
		tituloPrograma = 'Actualización de precios en nota de pedido';

		$('#inputTieneIva').change(
			function() {
				$('.precioUnitario').blur();
			}
		);

		cambiarModo('inicio');
	});

	function limpiarScreen() {
	}

	function buscar() {
		var url = funciones.controllerUrl('buscar', {
				idCliente: $('#inputBuscarCliente_selectedValue').val()
			}),
			msgError = 'No hay pedidos pendientes con los filtros especificados',
			cbSuccess = function(json){
				var cliente = ' - [' + $('#inputBuscarCliente_selectedValue').val() + '] ' +
								$('#inputBuscarCliente_selectedName').val();
				llenarPantalla(json);
				setTimeout(function(){
					cambiarModo('agregar');
					funciones.cambiarTitulo(tituloPrograma + cliente);
				}, 100);
			};
		$('#tablaPedidos > tbody').html('');
		funciones.buscar(url, cbSuccess, msgError);
	}

	function llenarPantalla(json) {
		var div = $('#divActualizacionPrecios'),
			tbody = $('<tbody>'),
			table = $('<table>').attr('id', 'tablaPedidos').addClass('registrosAlternados w100p').append(
				$('<thead>').addClass('tableHeader').append(
					$('<tr>').append(
						$('<th>').addClass('w10p').text('Nro Pedido'),
						$('<th>').addClass('w10p').text('Fecha'),
						$('<th>').addClass('w57p').text('Cliente'),
						$('<th>').addClass('w20p').text('Pares Pendientes'),
						$('<th>').addClass('w3p').append($('<input>')
															 .attr('type', 'checkbox')
															 .attr('id', 'checkUncheckAll')
															 .addClass('textbox koiCheckbox')
															 .click(function() {
																		$('#checkUncheckAll').isChecked() ? $('#tablaPedidos > tbody').find('[type="checkbox"]').check() : $('#tablaPedidos > tbody').find('[type="checkbox"]').uncheck();
																	})
						)
					)
				)
			);
		for (var i = 0; i < json.length; i++) {
			tbody.append(returnTr(json[i]));
		}

		div.append(table.append(tbody));
	}

	function returnTr(o) {
		return $('<tr>').addClass('s11').attr('id', 'tr_' + o.idPedido).append(
			$('<td>').addClass('aCenter').append($('<label>').text(o.idPedido)),
			$('<td>').addClass('aCenter').append($('<label>').text(o.fecha)),
			$('<td>').append($('<label>').text(o.cliente)),
			$('<td>').addClass('aCenter').append($('<label>').text(o.paresPendientes)),
			$('<td>').append(divCheckBox(o))
		);
	}

	function divCheckBox(o) {
		var div = $('<div>').addClass('aCenter');

		div.append($('<input>')
					   .attr('type', 'checkbox')
					   .attr('id', o.idPedido)
					   .data('data', {idPedido: o.idPedido})
					   .addClass('textbox koiCheckbox')
					   .click(function() {
								  var todos = true;
								  $('#tablaPedidos > tbody').find('[type="checkbox"]').each(function(i, item) {
									  if (!$(item).isChecked()) {
										  todos = false;
									  }
								  });
								  todos ? $('#checkUncheckAll').check() : $('#checkUncheckAll').uncheck();
							  })
		);

		return div;
	}

	function hayErrorGuardar(){
		detalle = [];

		$('#tablaPedidos > tbody').find('[type="checkbox"]').each(function(i, item) {
			if ($(item).isChecked()) {
				detalle.push($(item).data('data').idPedido);
			}
		});

		if(detalle.length == 0){
			return 'Debe seleccionar al menos un pedido para poder realizar la actualización de precios';
		}

		return false;
	}

	function guardar(){
		funciones.guardar(funciones.controllerUrl('agregar'),{detalle: detalle}, function() {
			funciones.reload();
		});
	}

	function cancelarBuscarClick(){
		$('#divActualizacionPrecios').html('');
		funciones.cancelarBuscarClick()
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
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
	<div id='divActualizacionPrecios' class='w100p customScroll'>
		<?php //TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w220' name='ClienteTodos' />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar' title='Corresponde a la fecha de creación de la órden de compra'>Rango fecha:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w80' to='inputFechaHasta' validate='Fecha' />
			<input id='inputFechaHasta' class='textbox filtroBuscar w80' from='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();', 'permiso' => 'comercial/pedidos/actualizacion_precios/buscar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();', 'permiso' => 'comercial/pedidos/actualizacion_precios/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>