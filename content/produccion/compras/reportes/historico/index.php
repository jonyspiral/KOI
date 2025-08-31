<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte histórico por material';
		cambiarModo('inicio');
	});

	function limpiarScreen() {
		funciones.cambiarTitulo();
		$('#divReporteHistorico').html('');
	}

	function getParams() {
		return {
			idMaterial: $('#inputMaterial_selectedValue').val(),
			idColor: $('#inputColor_selectedValue').val(),
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val()
		}
	}

	function buscar() {
		if ($('#inputMaterial_selectedValue').val() == '') {
			return $.error('Debe seleccionar un material para realizar la búsqueda');
		}
		funciones.load($('#divReporteHistorico'), funciones.controllerUrl('buscar', getParams()));
	}

	function xlsClick(){
		funciones.xlsClick(urlToExport('xls'));
	}

	function urlToExport(tipo){
		return funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), getParams());
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
	<div id='divReporteHistorico' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputMaterial' class='filtroBuscar'>Material:</label>
			<input id='inputMaterial' class='textbox autoSuggestBox obligatorio filtroBuscar w200' name='Material' />
		</div>
		<div>
			<label for='inputColor' class='filtroBuscar'>Color:</label>
			<input id='inputColor' class='textbox autoSuggestBox filtroBuscar w200' name='ColorMateriaPrima' linkedTo="inputMaterial,Material" />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w180' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputFechaHasta' class='textbox filtroBuscar w180' from='inputFechaHasta'' validate='Fecha' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>