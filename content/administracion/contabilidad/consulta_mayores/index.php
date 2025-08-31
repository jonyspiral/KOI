<?php

?>

<style>
#divConsultaMayoresWrapper {
	height: 490px;
}
#divConsultaMayores {
	padding-bottom: 10px;
}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte consulta mayores';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divConsultaMayores').html('');
	}

	function buscar() {
		if($('#inputBuscarCuenta_selectedValue').val() == ''){
			$.error('Debe seleccionar una cuenta para realizar la búsqueda');
		}else {
			funciones.limpiarScreen();
			var url = '/content' + window.location.pathname + 'buscar.php?';
			url += 'idImputacion=' + $('#inputBuscarCuenta_selectedValue').val();
			url += '&fechaDesde=' + $('#inputBuscarDesde').val();
			url += '&fechaHasta=' + $('#inputBuscarHasta').val();
			url += '&fechaVtoDesde=' + $('#inputBuscarVtoDesde').val();
			url += '&fechaVtoHasta=' + $('#inputBuscarVtoHasta').val();
			url += '&consolidado=' + ($('#inputConsolidado').isChecked() ? 'S' : 'N');
			funciones.load($('#divConsultaMayores'), url, function() {
				funciones.cambiarTitulo(tituloPrograma + ' - [' + $('#inputBuscarCuenta_selectedValue').val() + '] ' + $('#inputBuscarCuenta_selectedName').val());
				$('#divConsultaMayores').fixedHeader({target: 'table'});
			});
		}
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
		var url = '/content' + window.location.pathname + 'get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?';
		url += 'idImputacion=' + $('#inputBuscarCuenta_selectedValue').val();
		url += '&fechaDesde=' + $('#inputBuscarDesde').val();
		url += '&fechaHasta=' + $('#inputBuscarHasta').val();
		url += '&fechaVtoDesde=' + $('#inputBuscarVtoDesde').val();
		url += '&fechaVtoHasta=' + $('#inputBuscarVtoHasta').val();
		url += '&consolidado=' + ($('#inputConsolidado').isChecked() ? 'S' : 'N');

		return url;
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
<div id='programaContenido'>
	<div id='divConsultaMayoresWrapper'>
		<div id='divConsultaMayores' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarCuenta' class='filtroBuscar'>Cuenta:</label>
			<input id='inputBuscarCuenta' class='textbox obligatorio autoSuggestBox filtroBuscar w180' name='Imputacion' />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Fecha asiento desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w160' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Fecha asiento hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w160' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarVtoDesde' class='filtroBuscar'>Fecha vto. desde:</label>
			<input id='inputBuscarVtoDesde' class='textbox filtroBuscar w160' to='inputBuscarVtoHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarVtoHasta' class='filtroBuscar'>Fecha vto. hasta:</label>
			<input id='inputBuscarVtoHasta' class='textbox filtroBuscar w160' from='inputBuscarVtoDesde' validate='Fecha' />
		</div>
		<div class='fLeft'>
			<label class='filtroBuscar fLeft pRight3 w116'>Consolidado:</label>
			<input id='inputConsolidado' type='checkbox' class='filtroBuscar' />
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
