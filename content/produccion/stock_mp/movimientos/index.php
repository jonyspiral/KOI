<?php

?>

<style>
#divMovimientosStockWrapper {
	height: 490px;
}
#divMovimientosStock {
	padding-bottom: 10px;
}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Movimientos de stock MP';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divMovimientosStock').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', {
			fechaDesde: $('#inputBuscarFechaDesde').val(),
			fechaHasta: $('#inputBuscarFechaHasta').val(),
			tipoMovimiento: $('#inputBuscarTipoMovimiento').val(),
			tipoOperacion: $('#inputBuscarTipoOperacion').val(),
			idAlmacen: $('#inputBuscarAlmacen_selectedValue').val(),
			idMaterial: $('#inputBuscarMaterial_selectedValue').val(),
			idColorMateriaPrima: $('#inputBuscarColorMateriaPrima_selectedValue').val(),
			orden: $('#inputOrden').val()
		});
		funciones.load($('#divMovimientosStock'), url, function() {
			$('#divMovimientosStock').fixedHeader({target: 'table'});
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
		return funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), {
			fechaDesde: $('#inputBuscarFechaDesde').val(),
			fechaHasta: $('#inputBuscarFechaHasta').val(),
			tipoMovimiento: $('#inputBuscarTipoMovimiento').val(),
			tipoOperacion: $('#inputBuscarTipoOperacion').val(),
			idAlmacen: $('#inputBuscarAlmacen_selectedValue').val(),
			idMaterial: $('#inputBuscarMaterial_selectedValue').val(),
			idColorMateriaPrima: $('#inputBuscarColorMateriaPrima_selectedValue').val(),
			orden: $('#inputOrden').val()
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divMovimientosStockWrapper'>
		<div id='divMovimientosStock' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Rango fecha mov.:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar w80' to='inputBuscarFechaHasta' validate='Fecha' />
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar w80' from='inputBuscarFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarTipoMovimiento' class='filtroBuscar'>Tipo movimiento:</label>
			<select id='inputBuscarTipoMovimiento' class='textbox filtroBuscar w220'>
				<option value='0'>Todos</option>
				<option value='POS'>Positivo</option>
				<option value='NEG'>Negativo</option>
				<option value='INI'>Inicial</option>
			</select>
		</div>
		<div>
			<label for='inputBuscarTipoOperacion' class='filtroBuscar'>Tipo operación:</label>
			<select id='inputBuscarTipoOperacion' class='textbox filtroBuscar w220'>
				<option value='0'>Todos</option>
				<option value='1'>Ajuste</option>
				<option value='2'>Consumo</option>
				<option value='3'>Remito</option>
			</select>
		</div>
		<div>
			<label for='inputBuscarAlmacen' class='filtroBuscar'>Almacén:</label>
			<input id='inputBuscarAlmacen' class='textbox autoSuggestBox filtroBuscar w220' name='Almacen' alt='' />
		</div>
		<div>
			<label for='inputBuscarMaterial' class='filtroBuscar'>Material:</label>
			<input id='inputBuscarMaterial' class='textbox autoSuggestBox filtroBuscar w220' name='Material' alt='' />
		</div>
		<div>
			<label for='inputBuscarColorMateriaPrima' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColorMateriaPrima' class='textbox autoSuggestBox filtroBuscar w220' name='ColorMateriaPrima' linkedTo='inputBuscarMaterial,Material' alt='' />
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w220'>
				<option value='0'>Fecha movimiento descendente</option>
				<option value='1'>Fecha movimiento ascendente</option>
				<option value='2'>Almacén-material ascendente</option>
				<option value='3'>Almacén-material descendente</option>
				<option value='4'>Tipo de operación</option>
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
