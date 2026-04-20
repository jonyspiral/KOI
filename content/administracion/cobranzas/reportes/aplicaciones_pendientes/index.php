<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte aplicaciones pendientes de clientes';
		cambiarModo('inicio');

		$('#inputBuscarDesde').val(funciones.hoy());
		$('#inputBuscarHasta').val(funciones.hoy());
	});

	function limpiarScreen(){
		$('#divAplicacionesPendientes').html('');
	}

	function buscar() {
		var parametros = getParams();

		if(parametros.checkboxFAC == 'N' && parametros.checkboxNDB == 'N' && parametros.checkboxNCR == 'N' && parametros.checkboxREC == 'N') {
			$.error('Debe seleccionar al menos un tipo de documento');
		} else {
			funciones.limpiarScreen();
			var url = funciones.controllerUrl('buscar', getParams());
			funciones.load($('#divAplicacionesPendientes'), url, function() {
				$('#divAplicacionesPendientes').fixedHeader({target: 'table'});
			});
		}
	}

	function xlsClick(){
		funciones.xlsClick(urlToExport('xls'));
	}

	function pdfClick(){
		funciones.xlsClick(urlToExport('pdf'));
	}

	function urlToExport(tipo){
		return funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), getParams());
	}

	function getParams() {
		return {
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			fechaDesde: $('#inputBuscarDesde').val(),
			fechaHasta: $('#inputBuscarHasta').val(),
			empresa: $('#inputEmpresa').val(),
			checkboxFAC: ($('#checkboxFAC').isChecked() ? 'S' : 'N'),
			checkboxNDB: ($('#checkboxNDB').isChecked() ? 'S' : 'N'),
			checkboxNCR: ($('#checkboxNCR').isChecked() ? 'S' : 'N'),
			checkboxREC: ($('#checkboxREC').isChecked() ? 'S' : 'N'),
			ordenadoPor: $('#inputOrdenadoPor').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#checkboxFAC').check();
				$('#checkboxNDB').check();
				$('#checkboxNCR').check();
				$('#checkboxREC').check();
				break;
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divAplicacionesPendientes' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w220' name='ClienteTodos' />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar' title='Corresponde a la fecha de creación de la órden de compra'>Rango fecha:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w80' to='inputFechaHasta' validate='Fecha' />
			<input id='inputBuscarHasta' class='textbox filtroBuscar w80' from='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputEmpresa' class='filtroBuscarModo'>Empresa:</label>
			<select id='inputEmpresa' class='textbox filtroBuscar w220'>
				<option value='0'>Ambas</option>
				<option value='1'>1</option>
				<option value='2'>2</option>
			</select>
		</div>
		<div>
			<label for='divCalificacion' class='filtroBuscar'>Documentos:</label>
			<div id='divCalificacion' class="filtroBuscar inline-block w235 aLeft">
				<label for='checkboxFAC' class='filtroBuscar'>FAC </label>
				<input id='checkboxFAC' type='checkbox' class='textbox koiCheckbox' />

				<label for='checkboxNDB' class='filtroBuscar'>NDB </label>
				<input id='checkboxNDB' type='checkbox' class='textbox koiCheckbox' />

				<label for='checkboxNCR' class='filtroBuscar'>NCR </label>
				<input id='checkboxNCR' type='checkbox' class='textbox koiCheckbox' />

				<label for='checkboxREC' class='filtroBuscar'>REC </label>
				<input id='checkboxREC' type='checkbox' class='textbox koiCheckbox' />
			</div>
		</div>
		<div>
			<label for='inputOrdenadoPor' class='filtroBuscarModo'>Ordenado por:</label>
			<select id='inputOrdenadoPor' class='textbox filtroBuscar w220'>
				<option value='0'>Fecha ascendente</option>
				<option value='1'>Fecha descendente</option>
				<option value='2'>Cliente</option>
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
