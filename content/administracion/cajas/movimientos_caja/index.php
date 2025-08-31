<?php

?>

<style>
#divMovimientosCajaWrapper {
	height: 490px;
}
#divMovimientosCaja {
	padding-bottom: 10px;
}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Movimientos de caja';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divMovimientosCaja').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/cajas/movimientos_caja/buscar.php?',
			caja = '&caja=' + $('#inputBuscarCaja_selectedValue').val(),
			desde = '&desde=' + $('#inputBuscarDesde').val(),
			hasta = '&hasta=' + $('#inputBuscarHasta').val(),
			efectivo = '&soloEfectivo=' + ($('#inputSoloEfectivo').isChecked() ? 'S' : 'N');
		funciones.load($('#divMovimientosCaja'), url + caja + desde + hasta + efectivo, function() {
			$('#divMovimientosCaja').fixedHeader({target: 'table'});
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
		var url = '/content/administracion/cajas/movimientos_caja/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?',
			caja = '&caja=' + $('#inputBuscarCaja_selectedValue').val(),
			desde = '&desde=' + $('#inputBuscarDesde').val(),
			hasta = '&hasta=' + $('#inputBuscarHasta').val(),
			efectivo = '&soloEfectivo=' + ($('#inputSoloEfectivo').isChecked() ? 'S' : 'N');
		return url + caja + desde + hasta + efectivo;
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
	<div id='divMovimientosCajaWrapper'>
		<div id='divMovimientosCaja' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarCaja' class='filtroBuscar'>Caja:</label>
			<input id='inputBuscarCaja' class='textbox autoSuggestBox obligatorio filtroBuscar w180' name="CajaPorUsuario" />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputBuscarDesde' class='textbox obligatorio filtroBuscar w160' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w160' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div class='fLeft'>
			<label class='filtroBuscar fLeft pRight3 w74'>Sólo efectivo:</label>
			<input id='inputSoloEfectivo' type='checkbox' class='filtroBuscar' />
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
