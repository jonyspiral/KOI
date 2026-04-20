<?php

?>

<style>
#divAsientosContablesWrapper {
	height: 490px;
}
#divAsientosContables {
	padding-bottom: 10px;
}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Libro diario';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divAsientosContables').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content' + window.location.pathname + 'buscar.php?';
		url += 'fechaDesde=' + $('#inputBuscarDesde').val();
		url += '&fechaHasta=' + $('#inputBuscarHasta').val();
		url += '&fechaVtoDesde=' + $('#inputBuscarVtoDesde').val();
		url += '&fechaVtoHasta=' + $('#inputBuscarVtoHasta').val();
		url += '&numeroDesde=' + $('#inputBuscarAsientoDesde').val();
		url += '&numeroHasta=' + $('#inputBuscarAsientoHasta').val();
		url += '&consolidado=' + ($('#inputConsolidado').isChecked() ? 'S' : 'N');
		url += '&orden=' + $('#inputOrden').val();
		funciones.load($('#divAsientosContables'), url, function() {
			$('#divAsientosContables').fixedHeader({target: 'table'});
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
		var url = '/content' + window.location.pathname + 'get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?';
		url += 'fechaDesde=' + $('#inputBuscarDesde').val();
		url += '&fechaHasta=' + $('#inputBuscarHasta').val();
		url += '&fechaVtoDesde=' + $('#inputBuscarVtoDesde').val();
		url += '&fechaVtoHasta=' + $('#inputBuscarVtoHasta').val();
		url += '&numeroDesde=' + $('#inputBuscarAsientoDesde').val();
		url += '&numeroHasta=' + $('#inputBuscarAsientoHasta').val();
		url += '&consolidado=' + ($('#inputConsolidado').isChecked() ? 'S' : 'N');
		url += '&orden=' + $('#inputOrden').val();

		return url;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarDesde').val() + ' al ' + ($('#inputBuscarHasta').val() == '' || $('#inputBuscarHasta').val() == '__/__/____' ? funciones.hoy() : $('#inputBuscarHasta').val()));
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divAsientosContablesWrapper'>
		<div id='divAsientosContables' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
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
		<div>
			<label for='inputBuscarAsientoDesde' class='filtroBuscar'>Nº asiento desde:</label>
			<input id='inputBuscarAsientoDesde' class='textbox filtroBuscar w180' validate='EnteroPositivo' />
		</div>
		<div>
			<label for='inputBuscarAsientoHasta' class='filtroBuscar'>Nº asiento hasta:</label>
			<input id='inputBuscarAsientoHasta' class='textbox filtroBuscar w180' validate='EnteroPositivo' />
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w180'>
				<option value='0'>Fecha ascendente</option>
				<option value='1'>Fecha descendente</option>
				<option value='2'>Nº de asiento ascendente</option>
				<option value='3'>Nº de asiento descendente</option>
			</select>
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
