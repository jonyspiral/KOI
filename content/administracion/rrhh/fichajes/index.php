<?php
?>
<style>
#divFichajeWrapper {
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
		$('#divFichajes').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		funciones.load($('#divFichajes'), funciones.controllerUrl('buscar', getParams()));
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
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val(),
			modo: $('#inputBuscarModo').val(),
			personal: $('#inputBuscarEmpleado_selectedValue').val(),
			seccion: $('#inputBuscarSeccion_selectedValue').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarDesde').val() + ' - ' + $('#inputBuscarHasta').val());
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divFichajeWrapper'>
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
		<div>
			<label for='inputBuscarModo' class='filtroBuscar'>Modo:</label>
			<select id='inputBuscarModo' class='textbox filtroBuscar w190'>
				<option value='E'>Por empleado</option>
				<option value='F'>Por fecha</option>
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