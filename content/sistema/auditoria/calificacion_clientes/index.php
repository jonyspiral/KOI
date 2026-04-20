<?php

?>

<style>
#divSumasSaldosWrapper {
	height: 490px;
}
#divSumasSaldos {
	padding-bottom: 10px;
}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte cambios calificación clientes';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divCambiosCalificacion').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getParams());
		funciones.load($('#divCambiosCalificacion'), url);
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
			fechaHasta: $('#inputBuscarHasta').val()
		};
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
	<div id='divCambiosCalificacion' class='w100p customScroll'></div>
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
