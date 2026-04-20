<?php

?>

<style>
#divGestionProveedoresWrapper {
	height: 490px;
}
#divGestionProveedores {
	padding-bottom: 10px;
}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Gestión de proveedores';
		cambiarModo('inicio');
	});

	function limpiarScreen() {
		$('#divGestionProveedores').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/proveedores/gestion_proveedores/buscar.php?' +
				  ($('#inputBuscarProveedor_selectedValue').val() != 0 ? '&idProveedor=' + $('#inputBuscarProveedor_selectedValue').val() : '') +
				  ($('#inputSaldoDesde').val() != 0 ? '&saldoDesde=' + $('#inputSaldoDesde').val() : '') +
				  ($('#inputSaldoHasta').val() != 0 ? '&saldoHasta=' + $('#inputSaldoHasta').val() : '') +
				  ($('#inputSaldoFechaHasta').val() != 0 ? '&saldoFechaHasta=' + $('#inputSaldoFechaHasta').val() : '') +
				  '&mostrarSaldoCero=' + ($('#inputMostrarSaldoCero').isChecked() ? 'S' : 'N') +
				  '&empresa=' + $('#inputEmpresa').val() +
				  '&orden=' + $('#inputOrden').val();

		funciones.load($('#divGestionProveedores'), url, bindearEventos);
	}

	function bindearEventos() {
		$('.observaciones').click(function(e) {
			var tr = $(e.target).parents('tr');
			var idProveedor = tr.attr('id'),
				observaciones = tr.find('.observaciones').data('observaciones');
			editarProveedor(idProveedor, observaciones);
		});
		$('.nombre, .saldo').hover(function() {
			$(this).stop(true, true).css('font-weight', 'bold');
		}, function() {
			$(this).stop(true, true).css('font-weight', 'normal');
		});
		$('.observaciones').each(function() {
			$(this).data('observaciones', $(this).text()).text(funciones.acortarString($(this).text(), 50));
		});
		$('.nombre').click(function(e) {
			funciones.newWindow('/administracion/proveedores/cuenta_corriente_proveedor/?idProveedor=' + $(e.target).parents('tr').attr('id'));
		});
		$('.saldo').click(function(e) {
			funciones.newWindow('/administracion/proveedores/aplicacion/?idProveedor=' + $(e.target).parents('tr').attr('id'));
		});
		$('#divGestionProveedores').fixedHeader({target: 'table'});
	}

	function editarProveedor(idProveedor, observaciones) {
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append($('<table>').append(
				$('<tbody>').append(
					$('<tr><td><label for="inputEditarObservaciones">Observaciones: </label></td><td><textarea id="inputEditarObservaciones" class="textbox w200 h100" /></td></tr>'),
					$('<tr><td><label for="inputEditarIdProveedor"></label></td><td><input id="inputEditarIdProveedor" class="hidden" type="text" /></td></tr>')
				)
			)),
			botones = [{value: 'Guardar', action: function() {goEditar();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputEditarObservaciones').val(observaciones);
		$('#inputEditarIdProveedor').val(idProveedor);
		funciones.delay('$("#inputEditarObservaciones").focus();');
	}

	function goEditar(){
		var objeto = {
				observaciones: $('#inputEditarObservaciones').val(),
				idProveedor: $('#inputEditarIdProveedor').val()
			},
			url = '/content/administracion/proveedores/gestion_proveedores/editar.php';
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
					var tr = $('#' + json.data.id);
					tr.find('.observaciones').data('observaciones', json.data.observacionesGestion).text(funciones.acortarString(json.data.observacionesGestion, 50));
					$.jPopUp.close();
					$.success(funciones.getJSONMsg(json));
					break;
			}
		});
	}

	function pdfClick(){
		var finalUrl = urlToExport('pdf');
		if (finalUrl)
			funciones.pdfClick(finalUrl);
	}

	function xlsClick(){
		var finalUrl = urlToExport('xls');
		if (finalUrl)
			funciones.xlsClick(finalUrl);
	}

	function urlToExport(tipo){
		var url = '/content/administracion/proveedores/gestion_proveedores/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?' +
				  ($('#inputBuscarProveedor_selectedValue').val() != 0 ? '&idProveedor=' + $('#inputBuscarProveedor_selectedValue').val() : '') +
				  ($('#inputSaldoDesde').val() != 0 ? '&saldoDesde=' + $('#inputSaldoDesde').val() : '') +
				  ($('#inputSaldoHasta').val() != 0 ? '&saldoHasta=' + $('#inputSaldoHasta').val() : '') +
				  ($('#inputSaldoFechaHasta').val() != 0 ? '&saldoFechaHasta=' + $('#inputSaldoFechaHasta').val() : '') +
				  '&mostrarSaldoCero=' + ($('#inputMostrarSaldoCero').isChecked() ? 'S' : 'N') +
				  '&empresa=' + $('#inputEmpresa').val() +
				  '&orden=' + $('#inputOrden').val();
		return url;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divSaldoFechaHasta').hide();
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divGestionProveedoresWrapper'>
		<div id='divGestionProveedores' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox autoSuggestBox filtroBuscar w200' name='Proveedor' alt='' />
		</div>
		<div>
			<label for='inputSaldoDesde' class='filtroBuscar'>Saldo desde ($):</label>
			<input id='inputSaldoDesde' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputSaldoHasta' class='filtroBuscar'>Saldo hasta ($):</label>
			<input id='inputSaldoHasta' class='textbox filtroBuscar w200' />
		</div>
		<div id="divSaldoFechaHasta">
			<label for='inputSaldoFechaHasta' class='filtroBuscar'>Saldo a la fecha:</label>
			<input id='inputSaldoFechaHasta' class='textbox filtroBuscar w180' validate="Fecha" />
		</div>
		<div class='fLeft'>
			<label class='filtroBuscar fLeft'>Mostrar saldo cero:</label>
			<div id='divMostrarSaldoCero' class='fRight w217 aLeft'>
				<input id='inputMostrarSaldoCero' type='checkbox' class='filtroBuscar' />
			</div>
		</div>
		<div>
			<label for="inputEmpresa" class='filtroBuscar'>Empresa:</label>
			<select id='inputEmpresa' class='textbox filtroBuscar w200'>
				<option value='0'>Ambas</option>
				<option value='1'>1</option>
				<option value='2'>2</option>
			</select>
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w200'>
				<option value='0'>Razón social</option>
				<option value='1'>Saldo ascendente</option>
				<option value='2'>Saldo descendente</option>
				<option value='3'>Imputación ascendente</option>
				<option value='4'>Imputación descendente</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
