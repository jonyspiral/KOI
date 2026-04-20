<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reimpresión de remitos';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/comercial/remitos/reimpresion/buscar.php?';
			url += 'idCliente=' + $('#inputBuscarCliente_selectedValue').val();
			url += '&desde=' + $('#inputBuscarDesde').val();
			url += '&hasta=' + $('#inputBuscarHasta').val();
			url += '&facturado=' + $('#radioGroupFacturado').radioVal();
			url += '&numero=' + $('#inputBuscarNumero').val();
		var msgError = 'No hay remitos con ese filtro',
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
					$('<label>').text(o.idCliente + ' - ' + o.razonSocialCliente + ' - REMITO Nº ' + o.numero)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fecha + (o.usuarioBaja ? ' - Anulado por: ' + o.usuarioBaja : '')),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importe))
				)
			)
		);
		return table;
	}

	function divEstado(o) {
		var div = $('<div>').addClass('aLeft');
		if (o.facturado == 'S')
			div.append(
				$('<img>').addClass('pLeft10').attr('src', '/img/varias/facturado.png')
			);
		return div;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('botonera aCenter');
		var btn1, btn2;
		btn1 = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir')
						.click(function(){pdfClickRemito(o.numero);})
						.append($('<img>').attr('src', '/img/botones/40/pdf.gif'));
		btn2 = $('<a>').addClass('boton').attr('href', '#').attr('title', (o.facturado == 'S' ? 'Ya está facturado' : 'Borrar'))
						.click(function(){if(o.facturado == 'N' && o.anulado == 'N'){borrar(o.numero)}})
						.append($('<img>').attr('src', '/img/botones/40/borrar' + (o.facturado == 'N' && o.anulado == 'N' ? '' : '_off') + '.gif'));
		div.append(btn1, btn2);
		return div;
	}

	function returnTr(o) {
		var tr = $('<tr>').attr('id', 'tr_' + o.numero).append(
			$('<td>').addClass('w83p').append(divDatos(o)),
			$('<td>').addClass('w7p').append(divEstado(o)),
			$('<td>').addClass('w10p').append(divBotones(o))
		);

		if(o.anulado == 'S'){
			tr.addClass('indicador-rojo');
		}

		return tr;
	}

	function llenarPantalla(json) {
		var div = $('#divReimpresionRemitos');
        var table = $('<table>').attr('id', 'tablaRemitos').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function refrescarRemito(numero, deleted) {
		var before = $('#tr_' + numero).prev();
		if (before.length < 1)
			$('#tr_' + numero).remove();
		else
			before.next().remove();
		if (!deleted) {
			var url = '/content/comercial/remitos/reimpresion/buscar.php?';
				url += '&numero=' + numero;
			$.postJSON(url, function(json){
				switch (funciones.getJSONType(json)) {
					case funciones.jsonObject:
						if (before.length < 1) {
							$('#tablaRemitos tbody').first().prepend(returnTr(json.data[0]));
							$('#tablaRemitos tr').first().shine();
						} else {
							before.after(returnTr(json.data[0]));
							before.next().shine();
						}
						break;
					default:
						$.error(funciones.getJSONMsg(json));
						break;
				}
			});
		}
	}

	function pdfClickRemito(nro) {
		var url = '/content/comercial/remitos/reimpresion/getPdf.php';
			url += '?numero=' + nro;
		funciones.pdfClick(url);
	}

	function borrar(nro){
		var msg = '¿Está seguro que desea borrar el remito Nº ' + nro + '?',
			url = '/content/comercial/remitos/reimpresion/borrar.php';
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, armoObjetoBorrar(nro), function(json){
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
							$.success('El remito se ha borrado correctamente');
								//refrescarRemito(json.data.nro, true);
							var tr = $('#tr_' + nro);
							tr.html('');
							tr.append(
								$('<td>').addClass('w83p').append(divDatos(json.data)),
								$('<td>').addClass('w7p').append(divEstado(json.data)),
								$('<td>').addClass('w10p').append(divBotones(json.data))
							);
							if(json.data.anulado == 'S'){
								tr.addClass('indicador-rojo');
							}
							break;
					}
				});
			}
		});
	}

	function armoObjetoBorrar(nro){
		return {
				numero: nro
			};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.customRadio').enableRadioGroup();
				$('#divReimpresionRemitos').html('');
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
	<div id='divReimpresionRemitos' class='w100p customScroll acordeon h480'>
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
			<label class='filtroBuscar'>Facturado:</label>
			<div id='radioGroupFacturado' class='customRadio w180 inline-block' default='rdFacturado_A'>
				<input id='rdFacturado_A' type='radio' name='radioGroupFacturado' value='A' /><label for='rdFacturado_A'>Ambas</label>
				<input id='rdFacturado_S' type='radio' name='radioGroupFacturado' value='S' /><label for='rdFacturado_S'>S</label>
				<input id='rdFacturado_N' type='radio' name='radioGroupFacturado' value='N' /><label for='rdFacturado_N'>N</label>
			</div>
		</div>
		<div>
			<label class='filtroBuscar'>Numero:</label>
			<input id='inputBuscarNumero' class='textbox filtroBuscar w190' validate='Entero' />
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
