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
		tituloPrograma = 'Reporte comisiones';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divComisiones').html('');
	}

	function buscar() {
		var parametros = getParams();

		if(parametros.idVendedor == '' || parametros.fechaDesde == '' || parametros.fechaHasta == '') {
			$.error('Todos los filtros son obligatorios');
		} else {
			funciones.limpiarScreen();
			var url = funciones.controllerUrl('buscar', getParams());
			funciones.load($('#divComisiones'), url, function() {
				$('#divComisiones').fixedHeader({target: 'table'});
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
			idVendedor: $('#inputBuscarVendedor_selectedValue').val(),
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
	<div id='divComisiones' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarVendedor' class='filtroBuscar'>Vendedor:</label>
			<input id='inputBuscarVendedor' class='textbox obligatorio autoSuggestBox filtroBuscar w220' name='Vendedor' />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar' title='Corresponde a la fecha de creación de la órden de compra'>Rango fecha:</label>
			<input id='inputBuscarDesde' class='textbox obligatorio filtroBuscar w80' to='inputFechaHasta' validate='Fecha' />
			<input id='inputBuscarHasta' class='textbox obligatorio filtroBuscar w80' from='inputFechaDesde' validate='Fecha' />
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
