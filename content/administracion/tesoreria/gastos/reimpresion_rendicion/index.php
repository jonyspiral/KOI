<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reimpresión rendición de gastos';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/tesoreria/gastos/reimpresion_rendicion/buscar.php?';
			url += '&desde=' + $('#inputBuscarDesde').val();
			url += '&hasta=' + $('#inputBuscarHasta').val();
			url += '&numero=' + $('#inputBuscarNumero').val();
		var msgError = 'No hay ordenes de pago con ese filtro',
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
					$('<label>').text('RENDICIÓN DE GASTOS Nº ' + o.numero + (o.observaciones == null ? '' : ' - ' + o.observaciones))
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fecha),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importeTotal) + ' - Pendiente: ' + funciones.formatearMoneda(o.importePendiente))
				)
			)
		);
		return table;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('botonera aCenter');
		var botonPdf, botonEditar, botonBorrar;

		botonEditar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
						.append($('<img>').attr('src', '/img/botones/40/editar.gif')
						.data('reimpresion', o)
						.click(function(e) {editarOrdenDePago(e.target);}));
		botonPdf = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir')
						.click(function(){pdfClickReimpresionGastos(o.numero)})
						.append($('<img>').attr('src', '/img/botones/40/pdf.gif'));
		botonBorrar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Borrar')
						.append($('<img>').attr('src', '/img/botones/40/borrar' + (o.importeTotal != o.importePendiente ? '_off' : '') + '.gif'))
						.click(function(){if(!(o.importeTotal != o.importePendiente))borrarReimpresionGastos(o.numero)});
		div.append(botonEditar, botonBorrar, botonPdf);
		return div;
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.numero).append(
			$('<td>').addClass('w85p').append(divDatos(o)),
			$('<td>').addClass('w15p').append(divBotones(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divReimpresionOrdenesDePago');
        var table = $('<table>').attr('id', 'tablaReimpresionesGastos').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function refrescarReimpresionesGastos(numero, deleted) {
		var before = $('#tr_' + numero).prev();
		if (before.length < 1)
			$('#tr_' + numero).remove();
		else
			before.next().remove();
		if (!deleted) {
			var url = '/content/administracion/tesoreria/gastos/reimpresion_rendicion/buscar.php?';
				url += 'numero=' + numero;
			$.postJSON(url, function(json){
				switch (funciones.getJSONType(json)) {
					case funciones.jsonObject:
						if (before.length < 1) {
							$('#tablaReimpresionesGastos tbody').first().prepend(returnTr(json.data[0]));
							$('#tablaReimpresionesGastos tr').first().shine();
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

	function refrescarListaReimpresionesGastos(id) {
		$('#tr_' + id).remove();
	}

	function borrarReimpresionGastos(numero){
		var msg = '¿Está seguro que desea anular la rendición de gastos Nº "' + numero + '"?',
			url = '/content/administracion/tesoreria/gastos/reimpresion_rendicion/borrar.php';
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
							$.success('La rendición de gastos fue anulada correctamente', function(){
								refrescarListaReimpresionesGastos(numero);
							});
							break;
					}
				});
			}
		});
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

	function pdfClickReimpresionGastos(nro) {
		var url = '/content/administracion/tesoreria/gastos/reimpresion_rendicion/getPdf.php';
			url += '?numero=' + nro;
		funciones.pdfClick(url);
	}

	function editarOrdenDePago(btn) {
		var rendicionDeGastos = $(btn).data('reimpresion'),
			body = $('<tbody>').append($(
				'<tr><td><label for="inputObservaciones">Observaciones: </label></td><td><textarea id="inputObservaciones" class="textbox w190" rel="descripcion" /></td></tr>'
		)),
			div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append($('<table>').append(body)),
			botones = [{value: 'Guardar', action: function() {goEditarCheque(btn);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		llenarCamposEditarOrdenDePago(rendicionDeGastos);
	}

	function llenarCamposEditarOrdenDePago(OrdenDePago){
		$('#inputObservaciones').val(OrdenDePago.observaciones);
		funciones.delay('$("#inputObservaciones").focus();');
	}

	function goEditarCheque(btn){
		var objeto = {
				numero: $(btn).data('reimpresion').numero,
				observaciones: $('#inputObservaciones').val()
			},
			url = '/content/administracion/tesoreria/gastos/reimpresion_rendicion/editar.php';

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
						$(btn).data('reimpresion').observaciones = objeto.observaciones;

						var obj = jQuery.extend(true, {}, $(btn).data('reimpresion'));
						$('#tr_' + objeto.numero).html('');
						$('#tr_' + objeto.numero).append(
							$('<td>').addClass('w85p').append(divDatos(obj)),
							$('<td>').addClass('w15p').append(divBotones(obj))
						);
						$.jPopUp.close();
						break;
				}
			});
		}
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.customRadio').enableRadioGroup();
				$('#divReimpresionOrdenesDePago').html('');
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
	<div id='divReimpresionOrdenesDePago' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarNumero' class='filtroBuscar'>Numero:</label>
			<input id='inputBuscarNumero' class='textbox filtroBuscar w190 aRight' validate='Entero' />
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
