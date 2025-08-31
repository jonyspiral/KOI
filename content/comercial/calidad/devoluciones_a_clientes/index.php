<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Devoluciones a clientes';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divDevoluciones').html('');
		funciones.cambiarTitulo();
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', {
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val()
		});
		var msgError = 'No hay devoluciones con ese filtro',
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
					$('<label>').text(o.idCliente + ' - ' + o.cliente.razonSocial + ' - DEVOLUCIÓN Nº ' + o.id + ' - Pares: ' + o.cantidadPares)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fechaAlta + (o.observaciones ? ' - Observaciones: ' + o.observaciones : ''))
				)
			)
		);
		return table;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('aCenter');
		var btn1, btn2;
		btn1 = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir')
						.append($('<img>').attr('src', '/img/botones/40/pdf.gif'))
						.click($.proxy(pdfClickDevolucion, o));
		/* Se agrega si se quiere. Hay que agregar el controller también
		btn2 = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Borrar')
						.append($('<img>').attr('src', '/img/botones/40/borrar' + (o.caeObtenido == 'S' ? '_off' : '') + '.gif'))
						.click($.proxy(borrar, o));
		*/
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
		var div = $('#divDevoluciones');
        var table = $('<table>').attr('id', 'tablaDevoluciones').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function pdfClickDevolucion() {
		var url = funciones.controllerUrl('getPdf', {idDevolucion: this.id});
		funciones.pdfClick(url);
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la devolución Nº ' + this.id + '?',
			url = funciones.controllerUrl('borrar');
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, {idDevolucion: this.id}, function(json){
					$.hideLoading();
					switch (funciones.getJSONType(json)){
						case funciones.jsonNull:
						case funciones.jsonEmpty:
							$.error('Ocurrió un error');
							break;
						case funciones.jsonError:
							$.error(funciones.getJSONMsg(json));
							break;
						case funciones.jsonSuccess:
							$.success('La devolución se ha borrado correctamente', function(){
								var tr = $('#tr_' + json.data.id);
								(tr.prev().length < 1) ? tr.remove() : tr.next().remove();
							});
							break;
					}
				});
			}
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
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
	<div id='divDevoluciones' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w190' name='Cliente' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarDesde' validate='Fecha' />
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
