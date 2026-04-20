<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reimpresión de predespachos';

		cambiarModo('inicio');
	});

	function limpiarScreen() {
		$('#divReimpresionPredespachos').html('');
	}

	function getParams() {
		return {
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			idPedido: $('#inputBuscarPedido_selectedValue').val(),
			almacen: $('#inputAlmacen').val()
		};
	}

	function buscar() {
		funciones.limpiarScreen();
		var msgError = 'No hay predespachos con ese filtro',
			cbSuccess = function(json){
				llenarPantalla(json);
			};
		funciones.buscar(funciones.controllerUrl('buscar', getParams()), cbSuccess, msgError);
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text(o.idCliente + ' - ' + o.razonSocial)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Sucursal: ' + o.nombreSucursal + (o.esCasaCentral == 'S' ? ' (CASA CENTRAL)' : '')),
					$('<label>').addClass('fRight').text('Cant. a predespachar: ' + o.pendientePredespacho)
				)
			)
		);
		return table;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('botonera aCenter');
		var btn1;
		btn1 = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir')
			.click(function(){pdfClickRemito(
				{
					tipo: 'C',
					idCliente: o.idCliente,
					idSucursal: o.idSucursal
				}
			);})
			.append($('<img>').attr('src', '/img/botones/40/pdf.gif'));
		div.append(btn1);

		return div;
	}

	function pdfClickRemito(obj) {
		funciones.pdfClick(funciones.controllerUrl('getPdf', obj));
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.numeroDespacho + '-' + o.numeroItem).append(
			$('<td>').addClass('w87p').append(divDatos(o)),
			$('<td>').addClass('w6p').append(divBotones(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divReimpresionPredespachos');
		var table = $('<table>').attr('id', 'tablaDespachos').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
		}
		div.append(table);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divReimpresionPredespachos' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div id='divBuscarCliente'>
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w200' name='Cliente' />
		</div>
		<div id='divBuscarPedido'>
			<label for='inputBuscarPedido' class='filtroBuscar'>Pedido:</label>
			<input id='inputBuscarPedido' class='textbox autoSuggestBox filtroBuscar w200' name='Pedido' />
		</div>
		<div>
			<label for='inputAlmacen' class='filtroBuscar'>Almacén:</label>
			<select id='inputAlmacen' class='textbox filtroBuscar w200'>
				<option value='0'>Ambos</option>
				<option value='01'>01</option>
				<option value='02'>02</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php //Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php //Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>