<?php

?>

<style>
#divGestionCobranzaWrapper {
	height: 490px;
}
#divGestionCobranza {
	padding-bottom: 10px;
}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Gestión cobranza';
		cambiarModo('inicio');
		$('#inputBuscarVendedor').blur(function(){
			if ($('#inputBuscarCliente_selectedValue').val()!= ''){
				$('#inputBuscarCliente').attr('alt' , 'idVendedor=' + $('#inputBuscarVendedor_selectedValue').val());
			}
		});
		$('#radioGroupEmpresa').enableRadioGroup();
	});

	function limpiarScreen(){
		$('#divGestionCobranza').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/cobranzas/gestion_cobranza/buscar.php?' +
				  ($('#inputBuscarVendedor_selectedValue').val() != 0 ? '&idVendedor=' + $('#inputBuscarVendedor_selectedValue').val() : '') +
				  ($('#inputBuscarCliente_selectedValue').val() != 0 ? '&idCliente=' + $('#inputBuscarCliente_selectedValue').val() : '') +
				  ($('#checkbox1').isChecked() ? '&situacion1=S' : '') +
				  ($('#checkbox2').isChecked() ? '&situacion2=S' : '') +
				  ($('#checkbox3').isChecked() ? '&situacion3=S' : '') +
				  ($('#checkbox4').isChecked() ? '&situacion4=S' : '') +
				  ($('#checkbox5').isChecked() ? '&situacion5=S' : '') +
				  ($('#checkbox6').isChecked() ? '&situacion6=S' : '') +
				  ($('#checkbox7').isChecked() ? '&situacion7=S' : '') +
				  ($('#inputSaldoDesde').val() != 0 ? '&saldoDesde=' + $('#inputSaldoDesde').val() : '') +
				  ($('#inputSaldoHasta').val() != 0 ? '&saldoHasta=' + $('#inputSaldoHasta').val() : '') +
				  ($('#inputSaldoFechaHasta').val() != 0 ? '&saldoFechaHasta=' + $('#inputSaldoFechaHasta').val() : '') +
			      ($('#rdEmpresa_1').isChecked() ? '&empresa=1' : '') +
			      ($('#rdEmpresa_2').isChecked() ? '&empresa=2' : '') +
				  '&orden=' + $('#inputOrden').val();

		funciones.load($('#divGestionCobranza'), url, bindearEventos);
		$('#radioGroupEmpresa').enableRadioGroup();
	}

	function bindearEventos() {
		$('.seguimiento, .cliente, .calificacion, .saldo, .aplicador').hover(function() {
			$(this).stop(true, true).css('font-weight', 'bold');
		}, function() {
			$(this).stop(true, true).css('font-weight', 'normal');
		});
		$('.seguimiento').click(function(e) {
			funciones.newWindow('/administracion/cobranzas/seguimiento_clientes/?idCliente=' + $(e.target).parents('tr').attr('id'));
		});
		$('.cliente').click(function(e) {
			funciones.newWindow('/abm/clientes/?idCliente=' + $(e.target).parents('tr').attr('id'));
		});
		$('.calificacion, .observaciones').click(function(e) {
			var tr = $(e.target).parents('tr');
			var idCliente = tr.attr('id'),
				calificacion = tr.find('.calificacion').text(),
				observaciones = tr.find('.observaciones .obs_cli').data('observaciones'),
				observacionesVendedor = tr.find('.observaciones .obs_ven').data('observaciones');
			editarCliente(idCliente, calificacion, observaciones, observacionesVendedor);
		});
		$('.saldo').click(function(e) {
			var fecha = new Date();
			fecha.setMonth(fecha.getMonth() - 18);
			funciones.newWindow('/comercial/cuenta_corriente/?idCliente=' + $(e.target).parents('tr').attr('id') + '&desde=' + fecha.toLocaleDateString());
		});
		$('.aplicador').click(function(e) {
			funciones.newWindow('/administracion/cobranzas/aplicacion/?idCliente=' + $(e.target).parents('tr').attr('id'));
		});
		$('.observaciones').each(function() {
			var cli = $(this).find('.obs_cli'),
				ven = $(this).find('.obs_ven');
			cli.data('observaciones', cli.text()).text(cli.text());
			ven.data('observaciones', ven.text()).text(ven.text());
		});
		$('#divGestionCobranza').fixedHeader({target: 'table'});
	}

	function editarCliente(idCliente, calificacion, observaciones, observacionesVendedor) {
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append($('<table>').append(
				$('<tbody>').append(
					<? if (Usuario::logueado()->esPersonal()) { ?>
					$('<tr><td><label for="inputEditarObservaciones">Observaciones: </label></td><td><textarea id="inputEditarObservaciones" class="textbox w200 h100" /></td></tr>'),
					$('<tr><td style="width: 30px;"><label for="inputEditarCalificacion">Calificacion: </label></td><td>' +
					  '<select id="inputEditarCalificacion" class="textbox w200">' +
					  '<option value="01">1</option>' +
					  '<option value="02">2</option>' +
					  '<option value="03">3</option>' +
					  '<option value="04">4</option>' +
					  '<option value="05">5</option>' +
					  '<option value="06">6</option>' +
					  '<option value="07">7</option></select>'),
					<? } ?>
					$('<tr><td><label for="inputEditarObservacionesVendedor">Observaciones vendedor: </label></td><td><textarea id="inputEditarObservacionesVendedor" class="textbox w200 h100" /></td></tr>'),
					$('<tr><td><label for="inputEditarIdCliente"></label></td><td><input id="inputEditarIdCliente" class="hidden" type="text" /></td></tr>')
				)
			)),
			botones = [{value: 'Guardar', action: function() {goEditar();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputEditarIdCliente').val(idCliente);
		<? if (Usuario::logueado()->esPersonal()) { ?>
		$('#inputEditarObservaciones').val(observaciones);
		$('#inputEditarCalificacion').val(calificacion);
		funciones.delay('$("#inputEditarObservaciones").focus();');
		<? } else { ?>
		funciones.delay('$("#inputEditarObservacionesVendedor").focus();');
		<? } ?>
		$('#inputEditarObservacionesVendedor').val(observacionesVendedor);
	}

	function goEditar(){
		var objeto = {
				observacionesVendedor: $('#inputEditarObservacionesVendedor').val(),
				observaciones: $('#inputEditarObservaciones').val(),
				calificacion: $('#inputEditarCalificacion').val(),
				idCliente: $('#inputEditarIdCliente').val()
			},
			url = '/content/administracion/cobranzas/gestion_cobranza/editar.php';
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
					<? if (Usuario::logueado()->esPersonal()) { ?>
					tr.find('.calificacion')
						.text(json.data.calificacion)
						.removeClass('c_01')
						.removeClass('c_02')
						.removeClass('c_03')
						.removeClass('c_04')
						.removeClass('c_05')
						.removeClass('c_06')
						.removeClass('c_07')
						.addClass('c_' + json.data.calificacion);
					var cli = tr.find('.observaciones').find('.obs_cli');
					var strCli = json.data.observacionesGestionCobranza ? json.data.observacionesGestionCobranza : '';
					cli.data('observaciones', strCli).text(strCli);
					<? } ?>
					var ven = tr.find('.observaciones').find('.obs_ven');
					var strVen = json.data.observacionesVendedor ? json.data.observacionesVendedor : '';
					ven.data('observaciones', strVen).text(strVen);
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
		var url = '/content/administracion/cobranzas/gestion_cobranza/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?' +
				  ($('#inputBuscarVendedor_selectedValue').val() != 0 ? '&idVendedor=' + $('#inputBuscarVendedor_selectedValue').val() : '') +
				  ($('#inputBuscarCliente_selectedValue').val() != 0 ? '&idCliente=' + $('#inputBuscarCliente_selectedValue').val() : '') +
				  ($('#checkbox1').isChecked() ? '&situacion1=S' : '') +
				  ($('#checkbox2').isChecked() ? '&situacion2=S' : '') +
				  ($('#checkbox3').isChecked() ? '&situacion3=S' : '') +
				  ($('#checkbox4').isChecked() ? '&situacion4=S' : '') +
				  ($('#checkbox5').isChecked() ? '&situacion5=S' : '') +
				  ($('#checkbox6').isChecked() ? '&situacion6=S' : '') +
				  ($('#checkbox7').isChecked() ? '&situacion7=S' : '') +
				  ($('#inputSaldoDesde').val() != 0 ? '&saldoDesde=' + $('#inputSaldoDesde').val() : '') +
				  ($('#inputSaldoHasta').val() != 0 ? '&saldoHasta=' + $('#inputSaldoHasta').val() : '') +
				  ($('#inputSaldoFechaHasta').val() != 0 ? '&saldoFechaHasta=' + $('#inputSaldoFechaHasta').val() : '') +
				  '&orden=' + $('#inputOrden').val();
		return url;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#radioGroupEmpresa').enableRadioGroup();
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divGestionCobranzaWrapper'>
		<div id='divGestionCobranza' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarVendedor' class='filtroBuscar'>Vendedor:</label>
			<input id='inputBuscarVendedor' class='textbox autoSuggestBox filtroBuscar w200' name='Vendedor' alt='' />
		</div>
		<div>
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w200' name='Cliente' alt='' />
		</div>
		<div>
			<label for='divCalificacion' class='filtroBuscar'>Calificación:</label>
			<div id='divCalificacion' class="filtroBuscar inline-block w215 aLeft">
				<label for='checkbox1' class='filtroBuscar'>1 </label>
				<input id='checkbox1' type='checkbox' class='textbox koiCheckbox' />

				<label for='checkbox2' class='filtroBuscar'>2 </label>
				<input id='checkbox2' type='checkbox' class='textbox koiCheckbox' />

				<label for='checkbox3' class='filtroBuscar'>3 </label>
				<input id='checkbox3' type='checkbox' class='textbox koiCheckbox' />

				<label for='checkbox4' class='filtroBuscar'>4 </label>
				<input id='checkbox4' type='checkbox' class='textbox koiCheckbox' />

				<br>

				<label for='checkbox5' class='filtroBuscar'>5 </label>
				<input id='checkbox5' type='checkbox' class='textbox koiCheckbox' />

				<label for='checkbox6' class='filtroBuscar'>6 </label>
				<input id='checkbox6' type='checkbox' class='textbox koiCheckbox' />

				<label for='checkbox7' class='filtroBuscar'>7 </label>
				<input id='checkbox7' type='checkbox' class='textbox koiCheckbox' />
			</div>
		</div>
		<div>
			<label for='inputSaldoDesde' class='filtroBuscar'>Saldo desde ($):</label>
			<input id='inputSaldoDesde' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputSaldoHasta' class='filtroBuscar'>Saldo hasta ($):</label>
			<input id='inputSaldoHasta' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputSaldoFechaHasta' class='filtroBuscar'>Saldo a la fecha:</label>
			<input id='inputSaldoFechaHasta' class='textbox filtroBuscar w180' validate="Fecha" />
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w200'>
				<option value='0'>Razón social</option>
				<option value='1'>Calificación ascendente</option>
				<option value='2'>Calificación descendente</option>
				<option value='3'>Saldo ascendente</option>
				<option value='4'>Saldo descendente</option>
				<option value='5'>Saldo + cheques ascendente</option>
				<option value='6'>Saldo + cheques descendente</option>
				<option value='7'>Días promedio pago ascendente</option>
				<option value='8'>Días promedio pago descendente</option>
			</select>
		</div>
		<div>
			<label class='filtroBuscar'>Empresa:</label>
			<div id='radioGroupEmpresa' class='customRadio w180 inline-block'>
				<input id='rdEmpresa_0' type='radio' name='radioGroupEmpresa' value='0' /><label for='rdEmpresa_0'>Ambas</label>
				<input id='rdEmpresa_1' type='radio' name='radioGroupEmpresa' value='1' /><label for='rdEmpresa_1'>1</label>
				<input id='rdEmpresa_2' type='radio' name='radioGroupEmpresa' value='2' /><label for='rdEmpresa_2'>2</label>
			</div>
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
