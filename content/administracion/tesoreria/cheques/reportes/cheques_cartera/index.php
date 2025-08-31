<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte cheques en cartera';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divChequesEnCartera').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', {
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			empresa: $('#inputEmpresa').val(),
			idCliente: $('#inputCliente_selectedValue').val()
		});
		$.showLoading();
		$.get(url, function(result) {
			try {
				var json = $.parseJSON(result);
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
					case funciones.jsonError:
						$.error('Ocurrió un error al intentar realizar la consulta');
						break;
					case funciones.jsonInfo:
						$.info(funciones.getJSONMsg(json));
						break;
				}
			} catch (ex) {
				$('#divChequesEnCartera').html(result);
				cambiarModo('buscar');
			}
			$.hideLoading();
		});
	}

	function xlsClick(){
		funciones.xlsClick(urlToExport('xls'));
	}

	function pdfClick(){
		funciones.xlsClick(urlToExport('pdf'));
	}

	function urlToExport(tipo){
		return funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), {
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			empresa: $('#inputEmpresa').val(),
			idCliente: $('#inputCliente_selectedValue').val()
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divChequesEnCartera' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputCliente' class='textbox autoSuggestBox filtroBuscar w200' name='ClienteTodos' />
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
			<label for='inputEmpresa' class='filtroBuscar'>Empresa:</label>
			<select id='inputEmpresa' class='textbox filtroBuscar w200'>
				<option value='0'>Ambas</option>
				<option value='1'>1</option>
				<option value='2'>2</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>