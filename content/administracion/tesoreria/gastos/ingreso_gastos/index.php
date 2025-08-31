<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Gastos';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divGastos').html('');
	}

	function buscar() {
		if (!$('#inputBuscarCaja_selectedValue').val()) {
			$('#inputBuscarCaja').val('');
			$.error('Debe seleccionar al menos la caja con la que quiere operar');
		} else {
			var url = '/content/administracion/tesoreria/gastos/ingreso_gastos/buscar.php?';
				url += 'idCaja=' + $('#inputBuscarCaja_selectedValue').val();
				url += '&comprobante=' + $('#inputComprobante_selectedValue').val();
			var msgError = 'Ocurrió un error al intentar buscar los gastos a rendir',
				cbSuccess = function(json){
					$('#divGastos').html('');
					llenarPantalla(json);
				};
			funciones.buscar(url, cbSuccess, msgError);
		}
	}

	function llenarPantalla(json) {
		var div = $('#divGastos');
		var table = $('<table>').attr('id', 'tablaGastitos').attr('class', 'registrosAlternados w100p').append(
			$('<thead>').addClass('tableHeader').append(
				$('<tr>').append(
					$('<th>').addClass('w75p').text('Detalle'),
					$('<th>').addClass('w10p'),
					$('<th>').addClass('w10p'),
					$('<th>').addClass('w5p').append($('<input>')
						 .attr('type', 'checkbox')
						 .attr('id', 'checkUncheckAll')
						 .addClass('textbox koiCheckbox')
						 .click(function() {
							$('#checkUncheckAll').isChecked() ? $('#tablaGastitos > tbody').find('[type="checkbox"]').check() : $('#tablaGastitos > tbody').find('[type="checkbox"]').uncheck();
						})
					)
				)
			)
		).append(
			$('<tbody>')
		);
		var body = table.find('tbody').eq(0);
		for (var i = 0; i < json.length; i++) {
			body.append(returnTr(json[i]));
		}
		div.append(table);
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.id).append(
			$('<td>').append(divDatos(o)),
			$('<td>').append(divEstado(o)),
			$('<td>').append(divBotones(o)),
			$('<td>').append(divCheckBox(o))
		);
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text('PERSONA: ' + o.personaGasto.nombre + (o.observaciones ? ' - OBSERVACIONES: ' + o.observaciones : ''))
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fecha),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importe))
				)
			)
		);
		return table;
	}

	function divEstado(o) {
		var div = $('<div>').addClass('aLeft');
		if (o.comprobante == 'S')
			div.append(
				$('<img>').addClass('pLeft10').attr('src', '/img/varias/facturado.png')
			);
		return div;
	}

	function divBotones(o) {
		var btn;
		var div = $('<div>').addClass('aCenter');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
			.click($.proxy(function() {editarGastito(this);}, o))
			.append($('<img>').attr('src', '/img/botones/40/editar.gif'));
		div.append(btn);
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Eliminar')
			.click($.proxy(function() {borrarGastito(this);}, o))
			.append($('<img>').attr('src', '/img/botones/40/borrar.gif'));
		div.append(btn);
		return div;
	}

	function divCheckBox(o) {
		var div = $('<div>').addClass('aCenter');
		div.append($('<input>')
		   .attr('type', 'checkbox')
		   .attr('id', o.id)
		   .data('id', o.id)
		   .addClass('textbox koiCheckbox')
		   .click(function() {
				var todos = true;
				$('#tablaGastitos > tbody').find('[type="checkbox"]').each(function(i, item) {
					if (!$(item).isChecked()) {
						todos = false;
					}
				});
				todos ? $('#checkUncheckAll').check() : $('#checkUncheckAll').uncheck();
			})
		);
		return div;
	}

	function removeTr(id) {
		$('#tr_' + id).remove();
	}

	function borrarGastito(gastito) {
		var msg = '¿Está seguro que desea borrar el gasto de "' + gastito.personaGasto.nombre + '" por ' + funciones.formatearMoneda(gastito.importe) + '?',
			url = '/content/administracion/tesoreria/gastos/ingreso_gastos/borrar.php',
			objeto = {id: gastito.id};
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, objeto, function(json){
					$.hideLoading();
					switch (funciones.getJSONType(json)){
						case funciones.jsonNull:
						case funciones.jsonEmpty:
							$.error('Ocurrió un error.');
							break;
						case funciones.jsonError:
							$.error(funciones.getJSONMsg(json));
							break;
						case funciones.jsonSuccess:
							removeTr(gastito.id);
							$.success('El gasto fue eliminado correctamente');
							break;
					}
				});
			}
		});
	}

	function rendirGastitos() {
		var gastitosARendir = [];
		$('#tablaGastitos > tbody').find('[type="checkbox"]').each(function(i, item) {
			if ($(item).isChecked()) {
				gastitosARendir.push($(item).data('id'));
			}
		});
		if (gastitosARendir.length){
			var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
					  '<table><tbody>' +
					  '<tr><td><label for="inputFecha">Fecha:</label></td><td><input id="inputFecha" class="textbox obligatorio w200" validate="Fecha" value="' + funciones.hoy() + '" /></td></tr>' +
					  '<tr><td><label for="inputObservaciones" class="filtroBuscar">Observaciones:</label></td><td><textarea id="inputObservaciones" class="textbox w190" /></td></tr>' +
					  '</tbody></table>' +
					  '</div>';
			var botones = [{value: 'Guardar', action: function() {
				var url = '/content/administracion/tesoreria/gastos/ingreso_gastos/agregar.php';
				var objeto = {idCaja: $('#inputBuscarCaja_selectedValue').val(), observaciones: $('#inputObservaciones').val(), fecha: $('#inputFecha').val(), gastitos: gastitosARendir};
				if(objeto.fecha == ''){
					$.error('Debe ingresar una fecha');
				}else {
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
								for (var i = 0; i < gastitosARendir.length; i++){
									removeTr(gastitosARendir[i]);
								}
								gastitosARendir = [];
								$.jPopUp.close();
								$.success('Los gastos seleccionados fueron rendidos correctamente');
								break;
						}
					});
				}
			}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
				$.jPopUp.show(div, botones, function() {}, function() {$('#inputObservaciones').focus();});
		} else {
			$.error('Debe seleccionar al menos un gasto para rendir');
		}
	}

	function agregarEditarGastito(msj) {
		var objeto = {
			id: $('#inputGastitoId').val(),
			fecha: $('#inputGastitoFecha').val(),
			idPersonaGasto: $('#inputGastitoPersona_selectedValue').val(),
			personaGasto: {id: $('#inputGastitoPersona_selectedValue').val(), nombre: $('#inputGastitoPersona_selectedName').val()},
			importe: $('#inputGastitoImporte').val(),
			observaciones: $('#inputGastitoObservaciones').val(),
			comprobante: $('#inputGastitoComprobante').isChecked() ? 'S' : 'N',
			idCaja: $('#inputBuscarCaja_selectedValue').val()
		};
		var url = '/content/administracion/tesoreria/gastos/ingreso_gastos/editar.php';
		msj = msj || 'El gasto se agregó correctamente';
		if (objeto.fecha == '' || objeto.importe == '' || objeto.idPersonaGasto == '') {
			$.error('Deberá completar al menos la fecha, persona e importe del gasto');
		} else {
			$.showLoading();
			$.jPopUp.close();
			$.postJSON(url, objeto, function(json){
				$.hideLoading();
				switch (funciones.getJSONType(json)){
					case funciones.jsonNull:
					case funciones.jsonEmpty:
						$.error('Ocurrió un error.');
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonSuccess:
						if (!objeto.id){
							objeto.id = json.data.id;
							$('#tablaGastitos > tbody').prepend(returnTr(objeto));
						} else {
							$('#tr_' + objeto.id).html('').append(
								$('<td>').append(divDatos(objeto)),
								$('<td>').append(divEstado(objeto)),
								$('<td>').append(divBotones(objeto)),
								$('<td>').append(divCheckBox(objeto))
							);
						}
						$.success(msj);
						break;
				}
				$.hideLoading();
			});
		}
	}

	function popUpAgregarEditar(msj, callback){
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td class="w100"><label for="inputGastitoPersona">Persona:</label></td><td><input id="inputGastitoPersona" class="textbox obligatorio autoSuggestBox w190" name="PersonaGasto" /></td></tr>' +
				  '<tr><td><label for="inputGastitoFecha">Fecha:</label></td><td><input id="inputGastitoFecha" class="textbox obligatorio w170" validate="Fecha" /></td></tr>' +
				  '<tr><td><label for="inputGastitoImporte">Importe:</label></td><td><input id="inputGastitoImporte" class="textbox obligatorio w190 aRight" validate="DecimalPositivo" /></td></tr>' +
				  '<tr><td><label for="inputGastitoVuelto">Vuelto:</label></td><td><input id="inputGastitoVuelto" class="textbox w190 aRight" validate="DecimalPositivo" /></td></tr>' +
				  '<tr><td><label for="inputGastitoObservaciones">Observaciones:</label></td><td><textarea id="inputGastitoObservaciones" class="textbox w190"></textarea></td></tr>' +
				  '<tr><td><label for="inputGastitoComprobante">Comprobante:</label></td><td><input id="inputGastitoComprobante" type="checkbox" class="textbox koiCheckbox" /></td></tr>' +
				  '<input id="inputGastitoId" class="hidden" rel="id" />' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {agregarEditarGastito(msj);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones, null, callback);
		$('#inputGastitoPersona').focus();
		$('#inputGastitoFecha').val(funciones.hoy());
		$('#inputGastitoVuelto').blur(function() {
			$('#inputGastitoImporte').val(funciones.toFloat($('#inputGastitoImporte').val()) - funciones.toFloat($('#inputGastitoVuelto').val()));
			$('#inputGastitoVuelto').val('');
		});
	}

	function editarGastito(o){
		popUpAgregarEditar('El gasto se editó correctamente', function() {
			$('#inputGastitoPersona_selectedValue').val(o.personaGasto.id);
			$('#inputGastitoPersona_selectedName').val(o.personaGasto.nombre);
			$('#inputGastitoPersona').val(o.personaGasto.id + ' - ' + o.personaGasto.nombre);
			$('#inputGastitoFecha').val(o.fecha);
			$('#inputGastitoImporte').val(o.importe);
			$('#inputGastitoObservaciones').val(o.observaciones);
			(o.comprobante == 'S') ? $('#inputGastitoComprobante').check() : $('#inputGastitoComprobante').uncheck();
			$('#inputGastitoId').val(o.id);
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#btnAgregar').hide();
				$('#btnRendir').hide();
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarCaja_selectedName').val());
				$('#btnAgregar').show();
				$('#btnRendir').show();
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
	<div id='divGastos' class='w100p customScroll h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarCaja' class='filtroBuscar'>Caja:</label>
			<input id='inputBuscarCaja' class='textbox obligatorio autoSuggestBox filtroBuscar w190' name='CajaPorUsuario' />
		</div>
		<div>
			<label class='filtroBuscar'>Comprobante:</label>
			<select id='inputComprobante' class='textbox filtroBuscar w190'>
				<option value='1'>Ambos</option>
				<option value='2'>Sí</option>
				<option value='3'>No</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'rendir', 'accion' => 'rendirGastitos();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'popUpAgregarEditar();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
