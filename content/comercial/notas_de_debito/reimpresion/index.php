<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		$('#radioGroupCae input[name="radioGroupCae"]').change(function(){
			manejaRadioMail($('#radioGroupCae').radioVal());
		});
		tituloPrograma = 'Reimpresión de notas de débito';
		cambiarModo('inicio');
	});

	function manejaRadioMail(val) {
		$('#radioGroupCae').radioVal(val);
		if (val == 'N') {
			$('#rdMail_N').radioClick();
			$('#radioGroupMail').disableRadioGroup();
		} else
			$('#radioGroupMail').enableRadioGroup();
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', {
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val(),
			caeObtenido: $('#radioGroupCae').radioVal(),
			mailEnviado: $('#radioGroupMail').radioVal(),
			letra: $('#radioGroupLetra').radioVal(),
			numeroComprobante: $('#inputBuscarNumeroComprobante').val()
		});
		var msgError = 'No hay notas de débito con ese filtro',
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
					$('<label>').text(o.idCliente + ' - ' + o.razonSocialCliente + ' - NOTA DE DÉBITO "' + o.letra + '" Nº ' + o.numeroComprobante)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fecha),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importeTotal))
				)
			)
		);
		return table;
	}

	function divEstado(o) {
		var div = $('<div>').addClass('aLeft');
		if (o.caeObtenido == 'S')
			div.append(
				$('<img>').addClass('pLeft10').attr('src', '/img/varias/cae_obtenido.png')
			);
		if (o.mailEnviado == 'S')
			div.append(
				$('<img>').addClass('pLeft10').attr('src', '/img/varias/mail_sent.png')
			);
		return div;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('botonera aCenter');
		var btn1, btn2, btn3, btn4;
		btn1 = $('<a>').addClass('boton').attr('href', '#').attr('title', (o.empresa == '2' || o.caeObtenido == 'S' ? 'CAE ya obtenido' : 'Obtener CAE'))
						.attr('onclick', (o.empresa == '2' || o.caeObtenido == 'S' ? '' : 'caeClickNotaDeDebito(' + o.puntoDeVenta + ', ' + o.numero + ', "' + o.letra + '")'))
						.append($('<img>').attr('src', '/img/botones/40/cae' + (o.empresa == '2' || o.caeObtenido == 'S' ? '_off' : '') + '.gif'));
		btn2 = $('<a>').addClass('boton').attr('href', '#').attr('title', (o.caeObtenido == 'S' ? (o.mailEnviado == 'S' ? 'Re-' : '') + 'Enviar' : 'Debe obtener el CAE'))
						.attr('onclick', (o.empresa == '2' || o.caeObtenido == 'S' ? 'mailClickNotaDeDebito(' + o.puntoDeVenta + ', ' + o.numero + ', "' + o.letra + '")' : ''))
						.append($('<img>').attr('src', '/img/botones/40/mail' + (o.empresa == '2' || o.caeObtenido == 'S' ? '' : '_off') + '.gif'));
		btn3 = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir')
						.attr('onclick', 'pdfClickNotaDeDebito(' + o.puntoDeVenta + ', ' + o.numero + ', "' + o.letra + '")')
						.append($('<img>').attr('src', '/img/botones/40/pdf.gif'));
		btn4 = $('<a>').addClass('boton').attr('href', '#').attr('title', (o.caeObtenido == 'S' ? 'Ya tiene CAE' : 'Borrar'))
						.attr('onclick', (o.caeObtenido == 'S' ? '' : 'borrar(' + o.puntoDeVenta + ', ' + o.numero + ', "' + o.letra + '")'))
						.append($('<img>').attr('src', '/img/botones/40/borrar' + (o.caeObtenido == 'S' ? '_off' : '') + '.gif'));
		div.append(btn1, btn2, btn3, btn4);
		return div;
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.puntoDeVenta + '_' + o.numero + '_' + o.letra).append(
			$('<td>').addClass('w65p').append(divDatos(o)),
			$('<td>').addClass('w15p').append(divEstado(o)),
			$('<td>').addClass('w20p').append(divBotones(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divReimpresionNotasDeDebito');
        var table = $('<table>').attr('id', 'tablaNotasDeDebito').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function refrescarNotaDeDebito(puntoDeVenta, letra, numero, deleted) {
		var before = $('#tr_' + puntoDeVenta + '_' + numero + '_' + letra).prev();
		if (before.length < 1)
			$('#tr_' + puntoDeVenta + '_' + numero + '_' + letra).remove();
		else
			before.next().remove();
		if (!deleted) {
			var url = '/content/comercial/notas_de_debito/reimpresion/buscar.php?';
				url += 'puntoDeVenta=' + puntoDeVenta;
				url += '&letra=' + letra;
				url += '&numero=' + numero;
			$.postJSON(url, function(json){
				switch (funciones.getJSONType(json)) {
					case funciones.jsonObject:
						if (before.length < 1) {
							$('#tablaNotasDeDebito tbody').first().prepend(returnTr(json.data[0]));
							$('#tablaNotasDeDebito tr').first().shine();
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

	function ajax(url, cbSuccess, cbAlert) {
		$.showLoading();
		$.postJSON(url, function(json){
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
					cbSuccess(json);
					break;
				case funciones.jsonAlert:
					cbAlert(json);
					break;
			}
		});
	}

	function caeClickNotaDeDebito(puntoDeVenta, nro, letra) {
		var url = '/content/comercial/notas_de_debito/reimpresion/getCae.php';
			url += '?puntoDeVenta=' + puntoDeVenta + '&letra=' + letra + '&numero=' + nro;
		ajax(url, function(json) {
			refrescarNotaDeDebito(json.data.puntoDeVenta, json.data.letra, json.data.nro);
			$.success('El CAE se ha obtenido correctamente');
		}, function(json) {
			refrescarNotaDeDebito(json.data.puntoDeVenta, json.data.letra, json.data.nro);
			$.alert(funciones.getJSONMsg(json));
		});
	}

	function mailClickNotaDeDebito(puntoDeVenta, nro, letra) {
		var url = '/content/comercial/notas_de_debito/reimpresion/sendMail.php';
			url += '?puntoDeVenta=' + puntoDeVenta + '&letra=' + letra + '&numero=' + nro;
		ajax(url, function(json) {
			refrescarNotaDeDebito(json.data.puntoDeVenta, json.data.letra, json.data.nro);
			$.success('La nota de débito se ha enviado correctamente');
		});
	}

	function pdfClickNotaDeDebito(puntoDeVenta, nro, letra) {
		var url = '/content/comercial/notas_de_debito/reimpresion/getPdf.php';
			url += '?puntoDeVenta=' + puntoDeVenta + '&letra=' + letra + '&numero=' + nro;
		funciones.pdfClick(url);
	}

	function borrar(puntoDeVenta, nro, letra){
		var msg = '¿Está seguro que desea borrar la nota de débito "' + letra + '" Nº ' + nro + '?',
			url = '/content/comercial/notas_de_debito/reimpresion/borrar.php';
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, armoObjetoBorrar(puntoDeVenta, nro, letra), function(json){
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
							$.success('La nota de débito se ha borrado correctamente', function(){
								refrescarNotaDeDebito(json.data.puntoDeVenta, json.data.letra, json.data.nro, true);
							});
							break;
					}
				});
			}
		});
	}

	function armoObjetoBorrar(puntoDeVenta, nro, letra){
		return {
			puntoDeVenta: puntoDeVenta,
			numero: nro,
			letra: letra
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.customRadio').enableRadioGroup();
				manejaRadioMail('A');
				$('#divReimpresionNotasDeDebito').html('');
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
	<div id='divReimpresionNotasDeDebito' class='w100p customScroll acordeon h480'>
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
			<label class='filtroBuscar'>CAE obtenido:</label>
			<div id='radioGroupCae' class='customRadio w180 inline-block' default='rdCae_A'>
				<input id='rdCae_A' type='radio' name='radioGroupCae' value='A' /><label for='rdCae_A'>Ambas</label>
				<input id='rdCae_S' type='radio' name='radioGroupCae' value='S' /><label for='rdCae_S'>S</label>
				<input id='rdCae_N' type='radio' name='radioGroupCae' value='N' /><label for='rdCae_N'>N</label>
			</div>
		</div>
		<div>
			<label class='filtroBuscar'>Mail enviado:</label>
			<div id='radioGroupMail' class='customRadio w180 inline-block' default='rdMail_A'>
				<input id='rdMail_A' type='radio' name='radioGroupMail' value='A' /><label for='rdMail_A'>Ambas</label>
				<input id='rdMail_S' type='radio' name='radioGroupMail' value='S' /><label for='rdMail_S'>S</label>
				<input id='rdMail_N' type='radio' name='radioGroupMail' value='N' /><label for='rdMail_N'>N</label>
			</div>
		</div>
		<div>
			<label class='filtroBuscar'>Letra:</label>
			<div id='radioGroupLetra' class='customRadio w180 inline-block' default='rdLetra_T'>
				<input id='rdLetra_T' type='radio' name='radioGroupLetra' value='T' /><label for='rdLetra_T'>Todas</label>
				<input id='rdLetra_A' type='radio' name='radioGroupLetra' value='A' /><label for='rdLetra_A'>A</label>
				<input id='rdLetra_B' type='radio' name='radioGroupLetra' value='B' /><label for='rdLetra_B'>B</label>
				<input id='rdLetra_E' type='radio' name='radioGroupLetra' value='E' /><label for='rdLetra_E'>E</label>
			</div>
		</div>
		<div>
			<label class='filtroBuscar'>Numero:</label>
			<input id='inputBuscarNumeroComprobante' class='textbox filtroBuscar w190' validate='Entero' />
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
