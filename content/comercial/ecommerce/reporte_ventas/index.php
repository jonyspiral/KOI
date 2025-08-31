<?php
?>

<style>
#divVentasWrapper {
	height: 490px;
}
</style>

<script type='text/javascript'>
	var cliente = null;

	$(document).ready(function(){
		tituloPrograma = 'Ventas de Ecommerce';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divVentas').html('');
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if ($('#inputBuscarFecha').val() == '')
			return $('#inputBuscarFecha').val('');
		var url = funciones.controllerUrl('buscar', {
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val(),
			customer: $('#inputBuscarCustomer_selectedValue').val(),
			usergroup: $('#inputBuscarUsergroup_selectedValue').val(),
			modo: $('#inputBuscarModo').val()
		});
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
		var url = funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), {
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val(),
			customer: $('#inputBuscarCustomer_selectedValue').val(),
			usergroup: $('#inputBuscarUsergroup_selectedValue').val(),
			modo: $('#inputBuscarModo').val()
		});
		return url;
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
	<div id='divVentasWrapper'>
		<div id="divVentas" class='w100p customScroll'></div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' alt='' validate="Fecha" />
		</div>
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarHasta' alt='' validate="Fecha"/>
		</div>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCustomer' class='textbox autoSuggestBox filtroBuscar w190' name='Ecommerce_Customer' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Grupo de clientes:</label>
			<input id='inputBuscarUsergroup' class='textbox autoSuggestBox filtroBuscar w190' name='Ecommerce_Usergroup' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Modo:</label>
			<select id='inputBuscarModo' class='textbox filtroBuscar w190'>
				<option value='0'>Detallado</option>
				<option value='1'>Totalizado</option>
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