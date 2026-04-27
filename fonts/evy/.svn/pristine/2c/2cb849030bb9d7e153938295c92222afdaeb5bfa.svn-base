<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Panel de control de cheques';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divRechazoCheque').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/tesoreria/cheques/panel_de_control/buscar.php?' + 'idCaja=' + $('#inputBuscarCaja_selectedValue').val() + '&fechaDesde=' + $('#inputFechaDesde').val() +
				  '&fechaHasta=' + $('#inputFechaHasta').val() + '&idCuentaBancaria=' + $('#inputCuentaBancaria_selectedValue').val() + '&numeroCheque=' + $('#inputNumeroCheque').val() +
				  '&tipoCheque=' + $('#inputTipoCheque').val() + '&idCliente=' + $('#inputCliente_selectedValue').val() + '&importeDesde=' + $('#inputImporteDesde').val() + '&importeHasta=' + $('#inputImporteHasta').val();
		var msgError = 'Ocurrió un error al intentar buscar cheques',
			cbSuccess = function(json){
				llenarPantalla(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function llenarPantalla(json) {
		var div = $('#divRechazoCheque');
		var table = $('<table>').attr('id', 'tablaCheques').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
		}
		div.append(table);
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.idCheque).append(
			$('<td>').addClass('w60p').append(divDatos(o)),
			$('<td>').addClass('w15p').append(divTipoCheque(o)),
			$('<td>').addClass('w15p').append(divBotones(o))
		);
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text((o.cliente != '' ? 'CLIENTE: ' + o.cliente + ' - ' : '') + 'Nº ' + funciones.formatearNumeroCheque(o.numero) + ' - BANCO: ' + o.nombreBanco)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha Vencimiento: ' + o.fechaVencimiento),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importe))
				)
			)
		);
		return table;
	}

	function divTipoCheque(o) {
		var label = $('<label>').text('Cheque ' + (o.propio == '1' ? 'propio' : 'de tercero')).addClass('bold');
		return $('<div>').append(label).addClass('aCenter');
	}

	function divBotones(o) {
		var div = $('<div>').addClass('aCenter'),

			btnEditar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
				.append($('<img>').attr('src', '/img/botones/40/editar.gif')
							.data('cheque', o)
							.click(function(e) {
									   editarCheque(e.target);
								   })
				),
			btnBorrar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Anular')
					.append($('<img>').attr('src', '/img/botones/40/borrar.gif')
						.data('cheque', o)
						.click(function(e) {
							borrarCheque(e.target);
						})
				),
			btnRechazar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Rechazar')
				.append($('<img>').attr('src', '/img/botones/40/rechazar.gif')
							.data('cheque', o)
							.click(function(e) {
									   rechazarCheque(e.target);
								   })
				),
			btnReingresar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Reingresar')
				.append($('<img>').attr('src', '/img/botones/40/download' + (o.reingresable == '1' ? '' : '_off') + '.gif')
							.data('cheque', o)
							.click(function(e) {
										if (o.reingresable == '1') {
											reingresarCheque(e.target);
										}
								   })
				);

		div.append(btnEditar, btnBorrar, btnRechazar, btnReingresar);
		return div;
	}

	function refrescarListaCheques(id) {
		$('#tr_' + id).remove();
	}

	function rechazarCheque(btn) {
		var cheque = $(btn).data('cheque');
		var body = $('<tbody>');
		body.append($(
			'<tr><td><label for="inputMotivoRechazo">Motivo: </label></td><td><input id="inputMotivoRechazo" type="text" class="textbox obligatorio autoSuggestBox w230" name="Motivo" alt="tipo=<?php echo Motivos::rechazoCheque; ?>" /></td></tr>' +
			'<tr><td><label for="inputObservaciones">Observaciones del rechazo: </label></td><td><textarea id="inputObservaciones" class="textbox inputForm w230"></textarea></td></tr>'
		)).find('input, textarea').blur(function() {
			$('#inputObservacionesNdbP, #inputObservacionesNdbC').val(
				($('#inputMotivoRechazo_selectedName').val() != '' ? 'Por rechazo de cheque. Motivo: ' + $('#inputMotivoRechazo_selectedName').val() + '. ' : '') +
				'Cheque Nº ' + cheque.numero + '.' +
				($('#inputObservaciones').val() != '' ? ' Observaciones: ' + $('#inputObservaciones').val() : '')
			)
		});
		if (cheque.entregado == '1') {
			body.append($(
				'<tr><td><label class="bold">Datos de la NDB del proveedor</label></td><td></td></tr>' +
				'<tr><td><label for="inputNroNdbP">Número: </label></td><td><input id="inputNroNdbP" type="text" class="textbox obligatorio aRight w230" validate="Factura" /></td></tr>' +
				'<tr><td><label for="inputFechaNdbP">Fecha: </label></td><td><input id="inputFechaNdbP" type="text" class="textbox obligatorio aRight w210" validate="Fecha" /></td></tr>' +
				'<tr><td><label for="inputComisionNdbP">Comisión: </label></td><td class="aRight"><input id="inputComisionNdbP" type="text" class="textbox obligatorio w100 aRight" validate="DecimalPositivo" /></td></tr>' +
				'<tr><td><label for="inputTipoIvaNdbP">Tipo IVA: </label></td><td class="aRight"><input id="inputTipoIvaNdbP" type="text" class="textbox obligatorio autoSuggestBox w230" name="Impuesto" alt="tipo=1" /></td></tr>' +
				'<tr><td><label for="inputObservacionesNdbP">Observaciones: </label></td><td><textarea id="inputObservacionesNdbP" class="textbox inputForm w230"></textarea></td></tr>'
			))
		}
		if (cheque.cliente != '') {
			body.append($(
				'<tr><td><label class="bold">Datos de la NDB del cliente</label></td><td></td></tr>' +
				'<tr><td><label for="inputComisionNdbC">Comisión: </label></td><td class="aRight"><input id="inputComisionNdbC" type="text" class="textbox obligatorio w100 aRight" validate="DecimalPositivo" /></td></tr>' +
				'<tr><td><label for="inputObservacionesNdbC">Observaciones: </label></td><td><textarea id="inputObservacionesNdbC" class="textbox inputForm w230"></textarea></td></tr>'
			))
		} else {
			body.append($(
				'<tr><td colspan="2" class="aCenter bold">Este cheque fue ingresado por "Otros ingresos".<br>Si debe crearse una nota de débito para un cliente,<br>deberá hacerse desde la generación de NDB</td></tr>'
			))
		}
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append($('<table>').append(body));
		var botones = [{value: 'Guardar', action: function() {goRechazarCheque(cheque);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones, null, function() {
			$('#inputTipoIvaNdbP').val('1').autoComplete();
		});
	}

	function reingresarCheque(btn) {
		var cheque = $(btn).data('cheque');
		var body = $('<tbody>');
		body.append($(
				'<tr><td><label for="inputObservaciones">Observaciones del reingreso: </label></td><td><textarea id="inputObservaciones" class="textbox inputForm w230"></textarea></td></tr>'
			)).find('input, textarea').blur(function() {
												$('#inputObservacionesNdbP').val(
													($('#inputMotivoRechazo_selectedName').val() != '' ? 'Por rechazo de cheque. ' : '') +
													'Cheque Nº ' + cheque.numero + '.' +
													($('#inputObservaciones').val() != '' ? ' Observaciones: ' + $('#inputObservaciones').val() : '')
												)
											});
		if (cheque.entregado == '1') {
			body.append($(
				'<tr><td><label class="bold">Datos de la NDB del proveedor</label></td><td></td></tr>' +
				'<tr><td><label for="inputNroNdbP">Número: </label></td><td><input id="inputNroNdbP" type="text" class="textbox obligatorio aRight w230" validate="Factura" /></td></tr>' +
				'<tr><td><label for="inputFechaNdbP">Fecha: </label></td><td><input id="inputFechaNdbP" type="text" class="textbox obligatorio aRight w210" validate="Fecha" /></td></tr>' +
				'<tr><td><label for="inputComisionNdbP">Comisión: </label></td><td class="aRight"><input id="inputComisionNdbP" type="text" class="textbox obligatorio w100 aRight" validate="DecimalPositivo" /></td></tr>' +
				'<tr><td><label for="inputTipoIvaNdbP">Tipo IVA: </label></td><td class="aRight"><input id="inputTipoIvaNdbP" type="text" class="textbox obligatorio autoSuggestBox w230" name="Impuesto" alt="tipo=1" /></td></tr>' +
				'<tr><td><label for="inputObservacionesNdbP">Observaciones: </label></td><td><textarea id="inputObservacionesNdbP" class="textbox inputForm w230"></textarea></td></tr>'
			))
		}
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append($('<table>').append(body));
		var botones = [{value: 'Guardar', action: function() {goReingresarCheque(cheque);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones, null, function() {
			$('#inputTipoIvaNdbP').val('1').autoComplete();
			$('#inputObservaciones').focus();
		});
	}

	function borrarCheque(btn){
		var cheque = $(btn).data('cheque'),
			msg = '¿Está seguro que desea anular el cheque "' + cheque.numero + '"?',
			url = '/content/administracion/tesoreria/cheques/panel_de_control/borrar.php';
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, {idCheque: cheque.idCheque}, function(json){
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
							$.success('El cheque fue anulado correctamente', function(){
								refrescarListaCheques(cheque.idCheque);
							});
							break;
					}
				});
			}
		});
	}

	function editarCheque(btn) {
		var cheque = $(btn).data('cheque');
		var body = $('<tbody>');
		if (cheque.propio != '1') {
			body.append($(
				'<tr><td><label for="inputNumero">Número: </label></td><td class="aRight"><input id="inputNumero" type="text" class="textbox obligatorio w190 aRight" validate="Cheque" /></td></tr>' +
				'<tr><td><label for="inputNombreLibrador">Librador: </label></td><td><input id="inputNombreLibrador" type="text" class="textbox obligatorio w190" /></td></tr>' +
				'<tr><td><label for="inputCuitLibrador">CUIT librador: </label></td><td><input id="inputCuitLibrador" type="text" class="textbox obligatorio w190 aRight" validate="Cuit" /></td></tr>'
			))
		}
		body.append($(
				'<tr><td><label for="inputFechaEmision">Fecha emisión: </label></td><td><input id="inputFechaEmision" type="text" class="textbox obligatorio w170" validate="Fecha" /></td></tr>' +
				'<tr><td><label for="inputFechaVencimiento">Fecha vto.: </label></td><td><input id="inputFechaVencimiento" type="text" class="textbox obligatorio w170" validate="Fecha" /></td></tr>' +
				'<tr><td><label for="inputNoALaOrden">No a la orden: </label></td><td><input type="checkbox" id="inputNoALaOrden" class="textbox koiCheckbox inputForm"></td></tr>' +
				'<tr><td><label for="inputCruzado">Cruzado: </label></td><td><input type="checkbox" id="inputCruzado" class="textbox koiCheckbox inputForm"></td></tr>'
			));
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append($('<table>').append(body));
		var botones = [{value: 'Guardar', action: function() {goEditarCheque(btn);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		llenarCamposEditarCheque(cheque);
	}

	function llenarCamposEditarCheque(cheque){
		if (cheque.propio != '1') {
			$('#inputNumero').val(funciones.padLeft(cheque.numero, 8, 0));
			$('#inputNombreLibrador').val(cheque.nombreLibrador);
			$('#inputCuitLibrador').val(cheque.cuitLibrador);
		}
		$('#inputFechaEmision').val(cheque.fechaEmision);
		$('#inputFechaVencimiento').val(cheque.fechaVencimiento);
		cheque.noALaOrden == 'S' ? $('#inputNoALaOrden').check() : '';
		cheque.cruzado == 'S' ? $('#inputCruzado').check() : '';
	}

	function goEditarCheque(btn){
		var objeto = {
				idCheque: $(btn).data('cheque').idCheque,
				numero: $('#inputNumero').val(),
				nombreLibrador: $('#inputNombreLibrador').val(),
				cuitLibrador: $('#inputCuitLibrador').val(),
				fechaEmision: $('#inputFechaEmision').val(),
				fechaVencimiento: $('#inputFechaVencimiento').val(),
				noALaOrden: ($('#inputNoALaOrden').isChecked() ? 'S' : 'N'),
				cruzado: ($('#inputCruzado').isChecked() ? 'S' : 'N')
			},
			url = '/content/administracion/tesoreria/cheques/panel_de_control/editar.php',
			condicion;

		condicion = objeto.fechaEmision == '' || objeto.fechaVencimiento == '' || objeto.noALaOrden == '' || objeto.cruzado == '';
		if($(btn).data('cheque').propio != '1')
			condicion = condicion || objeto.numero == '' || objeto.nombreLibrador == '' || objeto.cuitLibrador == '';

		if(condicion) {
			$.error('Todos los campos son obligatorios.');
		} else if(funciones.esFechaMayor(objeto.fechaEmision, objeto.fechaVencimiento)) {
			$.error('La fecha de vencimiento no puede ser menor a la fecha de emisión.');
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
						$('#tr_' + json.data.idCheque).html('');
						$('#tr_' + json.data.idCheque).append(
							$('<td>').addClass('w60p').append(divDatos(json.data)),
							$('<td>').addClass('w15p').append(divTipoCheque(json.data)),
							$('<td>').addClass('w15p').append(divBotones(json.data))
						);
						$('#tr_' + json.data.idCheque).shine();
						$.jPopUp.close();
						break;
				}
			});
		}
	}

	function hayErrorGuardar(cheque) {
		if ($('#inputMotivoRechazo_selectedValue').val() == '')
			return 'Debe elegir un motivo para el rechazo del cheque';
		if (cheque.entregado) {
			if ($('#inputNroNdbP').val() == '')
				return 'Debe ingresar el número de nota de débito del proveedor';
			if ($('#inputFechaNdbP').val() == '')
				return 'Debe ingresar la fecha de la nota de débito del proveedor';
			if ($('#inputComisionNdbP').val() == '' || funciones.toFloat($('#inputComisionNdbP').val()) < 0)
				return 'Debe ingresar el importe de comisión de la nota de débito del proveedor (mínimo cero)';
			if ($('#inputTipoIvaNdbP_selectedValue').val() == '')
				return 'Debe elegir el tipo de IVA de la comisión de la nota de débito del proveedor';
		}
		if (cheque.cliente) {
			if ($('#inputComisionNdbC').val() == '' || funciones.toFloat($('#inputComisionNdbC').val()) < 0)
				return 'Debe ingresar el importe de comisión de la nota de débito del cliente (mínimo cero)';
		}
		return false;
	}

	function goRechazarCheque(cheque) {
		var error = hayErrorGuardar(cheque);
		if (error) {
			$.alert(error);
			return;
		}
		var url = '/content/administracion/tesoreria/cheques/panel_de_control/agregar.php';
		var objeto = {
			idCheque: cheque.idCheque,
			idMotivoRechazo: $('#inputMotivoRechazo_selectedValue').val(),
			observaciones: $('#inputObservaciones').val(),
			nroNdbP: $('#inputNroNdbP').val(),
			fechaNdbP: $('#inputFechaNdbP').val(),
			comisionNdbP: $('#inputComisionNdbP').val(),
			tipoIvaNdbP: $('#inputTipoIvaNdbP_selectedValue').val(),
			observacionesNdbP: $('#inputObservacionesNdbP').val(),
			comisionNdbC: $('#inputComisionNdbC').val(),
			observacionesNdbC: $('#inputObservacionesNdbC').val()
		};
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
					refrescarListaCheques(cheque.idCheque);
					$.success('El cheque fue rechazado correctamente');
					$.jPopUp.close();
					break;
			}
		});
	}

	function goReingresarCheque(cheque) {
		var error = hayErrorGuardar(cheque);
		if (error) {
			$.alert(error);
			return;
		}
		var url = '/content/administracion/tesoreria/cheques/panel_de_control/reingreso.php';
		var objeto = {
			idCheque: cheque.idCheque,
			observaciones: $('#inputObservaciones').val(),
			nroNdbP: $('#inputNroNdbP').val(),
			fechaNdbP: $('#inputFechaNdbP').val(),
			comisionNdbP: $('#inputComisionNdbP').val(),
			tipoIvaNdbP: $('#inputTipoIvaNdbP_selectedValue').val(),
			observacionesNdbP: $('#inputObservacionesNdbP').val()
		};
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
					refrescarListaCheques(cheque.idCheque);
					$.success('El cheque fue reingresado correctamente');
					$.jPopUp.close();
					break;
			}
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divRechazoCheque').html('');
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
	<div id='divRechazoCheque' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputCliente' class='textbox autoSuggestBox filtroBuscar w200' name='Cliente' />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Fecha vto. desde:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w180' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha vto. hasta:</label>
			<input id='inputFechaHasta' class='textbox filtroBuscar w180' from='inputFechaHasta'' validate='Fecha' />
		</div>
		<div>
			<label for='inputImporteDesde' class='filtroBuscar'>Importe desde:</label>
			<input id='inputImporteDesde' class='textbox filtroBuscar w200' name='importeDesde' validate='DecimalPositivo' />
		</div>
		<div>
			<label for='inputImporteHasta' class='filtroBuscar'>Importe hasta:</label>
			<input id='inputImporteHasta' class='textbox filtroBuscar w200' name='importeHasta' validate='DecimalPositivo' />
		</div>
		<div>
			<label for='inputCuentaBancaria' class='filtroBuscar'>Cuenta bancaria:</label>
			<input id='inputCuentaBancaria' class='textbox autoSuggestBox filtroBuscar w200' name='CuentaBancaria' />
		</div>
		<div>
			<label for='inputBuscarCaja' class='filtroBuscar'>Caja:</label>
			<input id='inputBuscarCaja' class='textbox autoSuggestBox filtroBuscar w200' name='Caja' />
		</div>
		<div>
			<label for='inputNumeroCheque' class='filtroBuscar'>Número cheque:</label>
			<input id='inputNumeroCheque' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for="inputTipoCheque" class='filtroBuscar'>Tipo:</label>
			<select id='inputTipoCheque' class='textbox filtroBuscar w200'>
				<option value='TOD'>Todos</option>
				<option value='PRO'>Propios</option>
				<option value='TER'>Terceros</option>
			</select>
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
