<?php
?>

<style>
#divVentasWrapper {
	height: 490px;
}
#divFichaje {
	padding-bottom: 10px;
}
.bRed {
	background-color: #FF7F50;
}
</style>

<script type='text/javascript'>
	var cliente = null;

	$(document).ready(function(){
		tituloPrograma = 'Fichajes';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divVentas').html('');
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if ($('#inputBuscarFecha').val() == '')
			return $('#inputBuscarFecha').val('');
		var url = '/content/administracion/rrhh/fichajes/buscar.php?';
			url += 'desde=' + $('#inputBuscarDesde').val();
			url += '&hasta=' + $('#inputBuscarHasta').val();
			url += '&modo=' + $(':radio:checked').val();
			url += '&personal=' + $('#inputBuscarEmpleado_selectedValue').val();
			url += '&seccion=' + $('#inputBuscarSeccion_selectedValue').val();

		funciones.load($('#divVentas'), url);
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
		var url = '/content/administracion/rrhh/fichajes/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?';
			url += 'desde=' + $('#inputBuscarDesde').val();
			url += '&hasta=' + $('#inputBuscarHasta').val();
			url += '&modo=' + $(':radio:checked').val();
			url += '&personal=' + $('#inputBuscarEmpleado_selectedValue').val();
		return url;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.customRadio').enableRadioGroup();
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarDesde').val() + ' - ' + $('#inputBuscarHasta').val());
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divVentasWrapper'>
		<div id='divFichajes' class='w100p customScroll'></div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='filtroBuscar'>Personal:</label>
			<input id='inputBuscarEmpleado' class='textbox autoSuggestBox filtroBuscar w190' name='Personal' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Sección:</label>
			<input id='inputBuscarSeccion' class='textbox autoSuggestBox filtroBuscar w190' name='SeccionProduccion' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' alt='' validate="Fecha" />
		</div>
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarHasta' alt='' validate="Fecha"/>
		</div>
			<label class='filtroBuscar'>Modo:</label>
		<div id='radioGroupModo' class='customRadio w200 inline-block' default="rdModo_E">
			<input id='rdModo_E' type='radio' name='radioGroupModo' value='E' /><label for='rdModo_E'>Por empleado</label>
			<input id='rdModo_F' type='radio' name='radioGroupModo' value='F' /><label for='rdModo_F'>Por fecha</label>
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