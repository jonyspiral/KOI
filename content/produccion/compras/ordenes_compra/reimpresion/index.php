<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reimpresión de órdenes de compra';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', {
				idProveedor: $('#inputBuscarProveedor_selectedValue').val(),
				desde: $('#inputBuscarFechaDesde').val(),
				hasta: $('#inputBuscarFechaHasta').val(),
				numero: $('#inputBuscarNumero').val()
			}),
			msgError = 'No hay órdenes de compra con ese filtro',
			cbSuccess = function(json){
				llenarPantalla(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text('ORDEN DE COMPRA Nº ' + o.id + ' - PROVEEDOR: ' + o.proveedor)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fecha + ' - Cant. detalles: ' + o.cantItems),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importe))
				)
			)
		);
		return table;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('botonera aCenter');
		var btn1, btn2;
		btn1 = $('<a>').addClass('boton').attr('href', '#')
						.attr('title', 'Imprimir')
						.click(function(){pdfClick(o.id);})
						.append($('<img>').attr('src', '/img/botones/40/pdf.gif'));
		btn2 = $('<a>').addClass('boton').attr('href', '#')
						.attr('title', 'Borrar')
						.click(function(){borrar(o.id);})
						.append($('<img>').attr('src', '/img/botones/40/borrar.gif'));
		div.append(btn1, btn2);
		return div;
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.id).append(
			$('<td>').addClass('w90p').append(divDatos(o)),
			$('<td>').addClass('w10p').append(divBotones(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divReimpresionOrdenesCompra');
        var table = $('<table>').attr('id', 'tablaOC').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function refrescarRow(id) {
		var before = $('#tr_' + id).prev();
		if (before.length < 1){
			$('#tr_' + id).remove();
		} else{
			before.next().remove();
		}
	}

	function pdfClick(id) {
		funciones.pdfClick(funciones.controllerUrl('getPdf', {id: id}));
	}

	function borrar(id){
		var msg = '¿Está seguro que desea borrar la órden de compra Nº ' + id + '?',
			url = funciones.controllerUrl('borrar');

		funciones.borrar(msg, url, {id: id}, function(){
					refrescarRow(id);
				});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divReimpresionOrdenesCompra').html('');
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
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
	<div id='divReimpresionOrdenesCompra' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox autoSuggestBox filtroBuscar w220' name='Proveedor' />
		</div>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Rango fecha:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar w80' to='inputBuscarFechaHasta' validate='Fecha' />
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar w80' from='inputBuscarFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarNumero' class='filtroBuscar'>Numero:</label>
			<input id='inputBuscarNumero' class='textbox filtroBuscar w220' validate='Entero' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
