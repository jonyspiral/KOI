<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		$('#radioGroupCae input[name="radioGroupCae"]').change(function(){
			manejaRadioMail($('#radioGroupCae').radioVal());
		});
		tituloPrograma = 'Reimpresión de recibos';
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

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/cobranzas/reimpresion_recibos/buscar.php?';
			url += 'idCliente=' + $('#inputBuscarCliente_selectedValue').val();
			url += '&desde=' + $('#inputBuscarDesde').val();
			url += '&hasta=' + $('#inputBuscarHasta').val();
			url += '&mailEnviado=' + $('#radioGroupMail').radioVal();
			url += '&numero=' + $('#inputBuscarNumero').val();
		var msgError = 'No hay recibos con ese filtro',
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
					$('<label>').text((o.idCliente == null ? o.recibidoDe : o.idCliente + ' - ' + o.razonSocialCliente) + ' - RECIBO Nº ' + o.numero)
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
		var div = $('<div>').addClass('aCenter');
		if (o.mailEnviado == 'S')
			div.append(
				$('<img>').addClass('pLeft10').attr('src', '/img/varias/mail_sent.png')
			);
		return div;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('botonera aCenter');
		var botonMail, botonPdf, botonEditar, botonBorrar;

		botonEditar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
						.append($('<img>').attr('src', '/img/botones/40/editar.gif')
						.data('recibo', o)
						.click(function(e) {editarRecibo(e.target);}));
		botonMail = $('<a>').addClass('boton').attr('href', '#').attr('title', (o.mailEnviado == 'S' ? 'Re-' : '') + 'Enviar')
						.click(function(){mailClickRecibo(o.numero)})
						.append($('<img>').attr('src', '/img/botones/40/mail.gif'));
		botonPdf = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir')
						.click(function(){pdfClickRecibo(o.numero)})
						.append($('<img>').attr('src', '/img/botones/40/pdf.gif'));
		botonBorrar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Borrar')
						.append($('<img>').attr('src', '/img/botones/40/borrar' + (o.importeTotal != o.importePendiente ? '_off' : '') + '.gif'))
						.click(function(){if(!(o.importeTotal != o.importePendiente))borrarRec(o.numero)});

		div.append(botonEditar, botonMail, botonPdf, botonBorrar);
		return div;
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.numero).append(
			$('<td>').addClass('w75p').append(divDatos(o)),
			$('<td>').addClass('w5p').append(divEstado(o)),
			$('<td>').addClass('w20p').append(divBotones(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divReimpresionRecibos');
        var table = $('<table>').attr('id', 'tablaRecibos').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function shine(o) {
		o.animate({opacity: '0.05'}, 300, function(){$(this).animate({opacity: '1'}, 300);});
	}

	function refrescarRecibo(numero, deleted) {
		var before = $('#tr_' + numero).prev();
		if (before.length < 1)
			$('#tr_' + numero).remove();
		else
			before.next().remove();
		if (!deleted) {
			var url = '/content/administracion/cobranzas/reimpresion_recibos/buscar.php?';
				url += 'numero=' + numero;
			$.postJSON(url, function(json){
				switch (funciones.getJSONType(json)) {
					case funciones.jsonObject:
						if (before.length < 1) {
							$('#tablaRecibos tbody').first().prepend(returnTr(json.data[0]));
							$('#tablaRecibos tr').first().shine();
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

	function mailClickRecibo(nro) {
		var url = '/content/administracion/cobranzas/reimpresion_recibos/sendMail.php';
			url += '?numero=' + nro;
		ajax(url, function(json) {
			refrescarRecibo(json.data.numero);
			$.success('El recibo se ha enviado correctamente');
		});
	}

	function pdfClickRecibo(nro) {
		var url = '/content/administracion/cobranzas/reimpresion_recibos/getPdf.php';
			url += '?numero=' + nro;
		funciones.pdfClick(url);
	}

	function editarRecibo(btn) {
		var recibo = $(btn).data('recibo'),
			body = $('<tbody>').append($(
				'<tr><td><label for="inputImputacion">Imputación: </label></td><td><input id="inputImputacion" type="text" class="textbox autoSuggestBox obligatorio w190" name="Imputacion" /></td></tr>' +
				'<tr><td><label for="inputRecibidoDe">Recibido de: </label></td><td><input id="inputRecibidoDe" type="text" class="textbox w190" /></td></tr>' +
				'<tr><td><label for="inputObservaciones">Observaciones: </label></td><td><textarea id="inputObservaciones" class="textbox w190" rel="descripcion" /></td></tr>'
		)),
			div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append($('<table>').append(body)),
			botones = [{value: 'Guardar', action: function() {goEditarRecibo(btn);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		llenarCamposEditarRecibo(recibo);
	}

	function llenarCamposEditarRecibo(recibo){
		setTimeout(function(){
			$('#inputImputacion').val(recibo.idImputacion).autoComplete();
			$('#inputRecibidoDe').val(recibo.recibidoDe);
			$('#inputObservaciones').val(recibo.observaciones);
		},500);
	}

	function goEditarRecibo(btn){
		var objeto = {
				numero: $(btn).data('recibo').numero,
				idImputacion: $('#inputImputacion_selectedValue').val(),
				recibidoDe: $('#inputRecibidoDe').val(),
				observaciones: $('#inputObservaciones').val()
			},
			url = '/content/administracion/cobranzas/reimpresion_recibos/editar.php';

		if(objeto.idImputacion == '') {
			$.error('El campo imputación es obligatorio.');
		} else {
			$.showLoading();
			$.postJSON(url, objeto, function(json){
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
						$(btn).data('recibo').idImputacion = objeto.idImputacion;
						$(btn).data('recibo').recibidoDe = objeto.recibidoDe;
						$(btn).data('recibo').observaciones = objeto.observaciones;

						var obj = jQuery.extend(true, {}, $(btn).data('recibo'));
						$('#tr_' + objeto.numero).html('');
						$('#tr_' + objeto.numero).append(
							$('<td>').addClass('w75p').append(divDatos(obj)),
							$('<td>').addClass('w5p').append(divEstado(obj)),
							$('<td>').addClass('w20p').append(divBotones(obj))
						);
						$.jPopUp.close();
						break;
				}
			});
		}
	}

	function refrescarListaRecibos(id) {
		$('#tr_' + id).remove();
	}

	function borrarRec(numero){
		var msg = '¿Está seguro que desea anular el recibo Nº "' + numero + '"?',
			url = '/content/administracion/cobranzas/reimpresion_recibos/borrar.php';
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, {numero: numero}, function(json){
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
							$.success('El recibo fue anulada correctamente', function(){
								refrescarListaRecibos(numero);
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
				$('.customRadio').enableRadioGroup();
				manejaRadioMail('A');
				$('#divReimpresionRecibos').html('');
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
	<div id='divReimpresionRecibos' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w190' name='Cliente' alt='' />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarDesde' validate='Fecha' />
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
			<label for='inputBuscarNumero' class='filtroBuscar'>Numero:</label>
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
