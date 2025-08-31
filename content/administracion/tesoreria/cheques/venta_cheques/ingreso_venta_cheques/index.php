<?php

?>

<script type='text/javascript'>
	var importeCaja = 0;
	$(document).ready(function(){
		tituloPrograma = 'Venta cheques';
		$('#btnDownload').attr('title', 'Confirmar venta');

		$('#inputBuscarCuentaBancaria').blur(function(){funciones.delay('blurBusqueda();');});
		$('#inputBuscarFechaDesde').blur(function(){funciones.delay('blurBusqueda();');});
		$('#inputBuscarFechaHasta').blur(function(){funciones.delay('blurBusqueda();');});

		$('#btnGoesHere').append($('<a class="boton" href="#" title="Actualizar" ><img src="/img/botones/25/actualizar.gif" class="custom-disable"></a>').click(refreshCheques));

		cambiarModo('inicio');
		buscarListaVentasCheques();
	});

	function blurBusqueda(){
		var alt = '&',
			cuentaBancaria = $('#inputBuscarCuentaBancaria_selectedValue').val(),
			fechaDesde = $('#inputBuscarFechaDesde').val(),
			fechaHasta = $('#inputBuscarFechaHasta').val();

		alt += (cuentaBancaria ? 'idCuentaBancaria=' + cuentaBancaria : '');
		alt += (fechaDesde && fechaDesde != '__/__/____' ? '&fechaDesde=' + fechaDesde : '');
		alt += (fechaHasta && fechaHasta != '__/__/____' ? '&fechaHasta=' + fechaHasta : '');

		$('#inputBuscarNroVenta').attr('alt', alt);
	}

	function refreshCheques() {
		var caja = $('#inputCajaOrigen_selectedValue').val();
		if (caja == '') {
			$.error('Debe seleccionar la caja de los cheques');
		} else {
			var checked = {};
			$('#importes-popup-cheque-body tr input:checked').each(function() {
				checked[$(this).data('obj').id] = 1;
			});
			var obj = {
				idCaja: caja,
				fechaDesde: $('#importes-popup-content-filtros-fechadesde').val(),
				fechaHasta: $('#importes-popup-content-filtros-fechahasta').val(),
				diasDesde: $('#importes-popup-content-filtros-diasdesde').val(),
				diasHasta: $('#importes-popup-content-filtros-diashasta').val(),
				importeDesde: $('#importes-popup-content-filtros-importedesde').val(),
				importeHasta: $('#importes-popup-content-filtros-importehasta').val(),
				order: $('#importes-popup-cheque-orden').val()
			};
			$.showLoading();
			$.getJSON('/content' + window.location.pathname + 'getCheques.php?idVenta=' + $('#inputBuscarNroVenta_selectedValue').val() + '&filtros=' + encodeURIComponent(JSON.stringify(obj)), function(json) {
				var cheque;
				$('#importes-popup-cheque-body').html('');
				for (var i in json.data) {
					cheque = json.data[i];
					var td = $('<input type="checkbox" id="chk_' + cheque.id + '">').data('id', cheque.id).data('obj', cheque).click(sumChecks);
					if (checked[cheque.id]) {
						td.check();
					}
					$('#importes-popup-cheque-body').append(
						$('<tr>')
							.append($('<td>').addClass('aCenter').text(cheque.fechaVencimiento ? cheque.fechaVencimiento : ' '))
							.append($('<td>').addClass('aRight').text(cheque.numero ? cheque.numero : ' '))
							.append($('<td>').append((cheque.libradorNombre ? cheque.libradorNombre : ' ') + (cheque.libradorNombre && cheque.libradorCuit ? '<br>' : '') + (cheque.libradorCuit ? cheque.libradorCuit : ' ')))
							.append($('<td>').text(cheque.banco.nombre ? cheque.banco.nombre : ' '))
							.append($('<td>').addClass('aCenter').text(cheque.diasVencimiento ? cheque.diasVencimiento : ' '))
							.append($('<td>').addClass('aRight').text(funciones.formatearMoneda(cheque.importe ? cheque.importe : ' ')))
							.append($('<td>').append(td).addClass('aCenter'))
					);
				}
				sumChecks();
				$.hideLoading();
			});
		}
	}

	function sumChecks() {
		var t = 0;
		$('#importes-popup-cheque-body tr input:checked').each(function() {
			t += funciones.toFloat($(this).data('obj').importe);
		});
		$('#importes-popup-cheque-sumaimporte').text(funciones.formatearMoneda(t));
	}

	function getCheques() {
		var checked = [];
		$('#importes-popup-cheque-body tr input:checked').each(function(key, val) {
			checked.push($(val).data('obj'));
		});
		return checked;
	}

	function limpiarScreen(){
		$('#divDepositoCheque').html('');
		$('#importes-popup-cheque-sumaimporte').text(funciones.formatearMoneda(0));
	}

	function buscarListaVentasCheques() {
		var url = '/content/administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/getListaVentas.php';
		var msgError = 'Ocurrió un error al intentar buscar las ventas de cheques';

		$.showLoading();
		$.postJSON(url, function(json){
			switch (funciones.getJSONType(json)) {
				case funciones.jsonError:
					$('#inputBuscar').limpiarAutoSuggestBox();
					$.error(funciones.getJSONMsg(json));
					break;
				case funciones.jsonObject:
					if(json.length == 0){
						funciones.cancelarBuscarClick();
					}else{
						llenarPantalla(json.data);
					}
					break;
				default:
					$('#inputBuscar').limpiarAutoSuggestBox();
					$.error(msgError, function(){
						$('#inputBuscar').focus();
					});
					break;
			}
			$.hideLoading();
		});
	}

	function buscar() {
		if($('#inputBuscarNroVenta_selectedValue').val()){
			$('#importes-popup-cheque-body').html('');
			var url = '/content/administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/buscar.php?idVentaChequesTemporal=' + $('#inputBuscarNroVenta_selectedValue').val(),
				msgError = 'El depósito número "' + $('#inputBuscarNroVenta_selectedValue').val() + '" no existe o no tiene permiso para visualizarlo.',
				cbSuccess = function(json){
					setTimeout(function(){
						$('#inputCajaOrigen').val(json.caja.id).autoComplete();
						$('#inputCuentaBancaria').val(json.cuentaBancaria.id).autoComplete();
						$('#inputFecha').val(json.fecha);
						$.each(json.cheques, function(key, value){
							$('#importes-popup-cheque-body').append(
								$('<tr>').append($('<td>').addClass('aCenter').text(value.fechaVencimiento ? value.fechaVencimiento : ' '),
												 $('<td>').addClass('aRight').append(value.numero ? value.numero : ' '),
												 $('<td>').append((value.libradorNombre ? value.libradorNombre : ' ') + (value.libradorNombre && value.libradorCuit ? '<br>' : '') + (value.libradorCuit ? value.libradorCuit : ' ')),
												 $('<td>').text(value.banco.nombre ? value.banco.nombre : ' '),
												 $('<td>').addClass('aCenter').text(value.diasVencimiento ? value.diasVencimiento : ' '),
												 $('<td>').addClass('aRight').text(funciones.formatearMoneda(value.importe ? value.importe : ' ')),
												 $('<td>').addClass('aCenter').append($('<input>')
																	  .attr('type', 'checkbox')
																	  .attr('id', 'chk_' + value.id)
																	  .addClass('custom-disable')
																	  .data('id', value.id)
																	  .data('obj', value)
																	  .click(sumChecks)
																	  .check())
								)
							);
						});
						sumChecks();
						funciones.cambiarTitulo(tituloPrograma + ' - ' + json.id);
						$('.custom-disable').disable();
					}, 50);
				};
			funciones.buscar(url, cbSuccess, msgError);
		}else{
			$.error('Debe seleccionar un número de depósito.');
		}
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text('VENTA DE CHEQUES Nº ' + o.id + ' - CAJA: ' + o.nombreCaja + ' - CUENTA: ' + o.nombreCuenta)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Cant. cheques: ' + o.cantCheques),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importeTotal))
				)
			)
		);
		return table;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('aCenter');
		var btn1;
		btn1 = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Buscar')
			.attr('onclick', 'buscarDeposito(' + o.id + ')')
			.append($('<img>').attr('src', '/img/botones/40/buscar.gif'));
		div.append(btn1);
		return div;
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.id).append(
			$('<td>').addClass('w75p').append(divDatos(o)),
			$('<td>').addClass('w5p').append(divBotones(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divListaDepositosConfirmar');
		var table = $('<table>').attr('id', 'tablaFacturas').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
		}
		div.append(table);
	}

	function buscarDeposito(id) {
		$('#inputBuscarNroVenta_selectedValue').val(id);
		buscar();
	}

	function guardar(){
		var aux = ($('#inputBuscarNroVenta_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/' + aux + '.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar());
		} catch (ex) {
			$.error(ex);
		}
	}

	function hayErrorGuardar(){
		var cheques = getCheques();

		if($('#inputCajaOrigen_selectedValue').val() == '')
			return 'Debe seleccionar una caja.';
		if($('#inputCuentaBancaria_selectedValue').val() == '')
			return 'Debe seleccionar una cuenta bancaria.';
		if(cheques.length == 0)
			return 'Debe ingresar al menos un cheque para la venta.';

		return false;
	}

	function armoObjetoGuardar(){
		var cheques = {},
			i = 0;
		$('#importes-popup-cheque-body tr input:checked').each(function() {
			cheques[i++] = $(this).data('obj').id;
		});
		return {
			idVentaChequesTemporal: $('#inputBuscarNroVenta_selectedValue').val(),
			idCajaOrigen: $('#inputCajaOrigen_selectedValue').val(),
			idCuentaBancaria: $('#inputCuentaBancaria_selectedValue').val(),
			fecha: $('#inputFecha').val(),
			cheques: getCheques()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar venta de cheques nro. "' + $('#inputBuscarNroVenta_selectedValue').val() + '"?',
			url = '/content/administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/borrar.php?';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idVentaCheques: $('#inputBuscarNroVenta_selectedValue').val()};
	}

	function confirmarClick(){
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td><label for="inputObservaciones" class="filtroBuscar">Observaciones:</label></td><td><textarea id="inputObservaciones" class="textbox w190" /></td></tr>' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {doConfirmar();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
	}

	function confirmar(objeto){
		if(typeof objeto === 'undefined')
			objeto = {};
		var url = '/content/administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/confirmar.php?';
		try {
			funciones.guardar(url, objeto);
		} catch (ex) {
			$.error(ex);
		}
	}

	function doConfirmar(){
		var idVentaChequesTemporal = $('#inputBuscarNroVenta_selectedValue').val(),
			observaciones = $('#inputObservaciones').val();

			confirmar({idVentaChequesTemporal: idVentaChequesTemporal, observaciones: observaciones});
	}

	function pdfClick(){
		var url = '/content/administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/getPdf.php';
		url += '?idVentaChequesTemporal=' + $('#inputBuscarNroVenta_selectedValue').val();
		funciones.pdfClick(url);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#btnDownload').hide();
				break;
			case 'buscar':
				$('.custom-disable').disable();
				$('#btnDownload').show();
				$('#divListaDepositosConfirmar').hide();
				break;
			case 'editar':
				$('#inputCajaOrigen').disable();
				$('.custom-disable').enable();
				$('#inputCuentaBancaria').focus();
				$('#btnDownload').hide();
				break;
			case 'agregar':
				$('#importes-popup-cheque-body').html('');
				$('#inputCajaOrigen').focus();
				$('#inputFecha').val(funciones.hoy());
				$('#divListaDepositosConfirmar').hide();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divListaDepositosConfirmar' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
	<div class='fLeft pantalla'>
		<?php
		$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
		$tabla->getRowCellArray($rows, $cells);

		$cells[0][0]->style->width = '140px';
		$cells[0][0]->content = '<label>Caja origen:</label>';
		$cells[0][1]->style->width = '170px';
		$cells[0][1]->content = '<input id="inputCajaOrigen" class="textbox obligatorio autoSuggestBox inputForm w150" name="CajaPorUsuario" rel="caja" />';

		$cells[1][0]->content = '<label>Cuenta bancaria:</label>';
		$cells[1][1]->content = '<input id="inputCuentaBancaria" class="textbox obligatorio autoSuggestBox inputForm w150" name="CuentaBancaria" rel="cuentaBancaria" />';

		$cells[2][0]->content = '<label>Fecha:</label>';
		$cells[2][1]->content = '<input id="inputFecha" class="textbox obligatorio inputForm aRight w130" validate="Fecha" rel="fecha" />';

		$tabla->create();
		?>
	</div>
	<div class='fRight pantalla w64p'>
		<div class="bold">Elegir cheque</div>
		<div class="w94p well h440 mTop5">
			<div>
				<table>
					<tbody>
						<tr><td style="width: 150px;"><label>Rango días vto: </label></td><td><input type="text" class="textbox w40p importes-input-numeric inputForm" id="importes-popup-content-filtros-diasdesde" placeholder="Desde..." validate="Entero"> - <input type="text" class="textbox w40p importes-input-numeric inputForm" id="importes-popup-content-filtros-diashasta" placeholder="Hasta..." validate="Entero"></td></tr>
						<tr><td><label>Rango importe: </label></td><td><input type="text"class="textbox w40p importes-input-numeric inputForm" id="importes-popup-content-filtros-importedesde" placeholder="Desde..." validate="Decimal"> - <input type="text" class="textbox w40p importes-input-numeric inputForm" id="importes-popup-content-filtros-importehasta" placeholder="Hasta..." validate="Decimal"></td></tr>
						<tr><td><label>Rango fecha vto: </label></td><td><input type="text" class="textbox w142 importes-input-numeric inputForm" id="importes-popup-content-filtros-fechadesde" to="importes-popup-content-filtros-fechahasta" placeholder="Desde..." validate="Fecha"> - <input type="text" class="textbox w142 importes-input-numeric inputForm" id="importes-popup-content-filtros-fechahasta" from="importes-popup-content-filtros-fechadesde" placeholder="Hasta..." validate="Fecha"></td></tr>
						<tr><td><label>Orden:</label></td>
							<td>
								<select id="importes-popup-cheque-orden" class="textbox w90p inputForm">
									<option value="1">Fecha vencimiento ascendente</option>
									<option value="2">Fecha vencimiento descentente</option>
									<option value="3">Días al vencimiento ascendente</option>
									<option value="4">Días al vencimiento descendente</option>
									<option value="5">Importe ascendente</option>
									<option value="6">Importe descendente</option>
								</select>
							</td>
						</tr>
						<tr><td id="btnGoesHere" class="aRight" colspan="2"></td></tr>
					</tbody>
				</table>
			</div>
			<div class="importes-popup-lista bAllOrange corner5">
				<table class="importes-striped">
					<thead class="tableHeader">
						<tr>
							<th style="width: 70px;" title="Fecha de vencimiento">F. venc.</th>
							<th style="width: 60px;" title="Número de cheque">Número</th>
							<th>Librador</th>
							<th>Banco</th>
							<th style="width: 30px;" title="Días para el vencimiento">Días</th>
							<th style="width: 80px;">Importe</th>
							<th style="width: 25px;"></th>
						</tr>
					</thead>
					<tbody id="importes-popup-cheque-body">
					</tbody>
				</table>
			</div>
			<div class="importes-popup-lista-totales">Total cheques: <span id="importes-popup-cheque-sumaimporte">$ 0.00</span></div>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarCuentaBancaria' class='filtroBuscar'>Cuenta bancaria:</label>
			<input id='inputBuscarCuentaBancaria' class='textbox autoSuggestBox filtroBuscar w190' name='CuentaBancaria' />
		</div>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar aRight w170' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar aRight w170' from='inputFechaHasta'' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarNroVenta' class='filtroBuscar'>Nro. venta:</label>
			<input id='inputBuscarNroVenta' class='textbox autoSuggestBox obligatorio filtroBuscar w190' name='VentaChequesTemporal' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'download', 'accion' => 'confirmarClick();', 'id' => 'btnDownload')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick()', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>