<?php


?>

<style>
#divResumenCajaWrapper {
	height: 490px;
}
#divResumenCaja {
	padding-bottom: 10px;
}
</style>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Resumen de caja';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divResumenCaja').html('');
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/administracion/tesoreria/resumen_caja/buscar.php?',
			empresa = ($('#radioGroupEmpresa').radioVal() != 0 ? '&empresa=' + $('#radioGroupEmpresa').radioVal() : ''),
			desde = ($('#fechaDesde').val() != '' ? '&desde=' + funciones.escape($('#inputBuscarDesde').val()) : ''),
			hasta = ($('#fechaHasta').val() != '' ? '&hasta=' + funciones.escape($('#inputBuscarHasta').val()) : '');
		$.showLoading();
		$.post(url + empresa + desde + hasta, function(result) {
			$.hideLoading();
			try {
				var json = $.parseJSON(result);
				$.error(funciones.getJSONMsg(json));
			} catch (ex) {
				$('#divResumenCaja').html(result);
				cambiarModo('buscar');
			}
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
		var url = '/content/administracion/tesoreria/resumen_caja/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?',
			empresa = $('#radioGroupEmpresa').radioVal(),
			desde = ($('#fechaDesde').val() != '' ? '&desde=' + funciones.escape($('#inputBuscarDesde').val()) : ''),
			hasta = ($('#fechaHasta').val() != '' ? '&hasta=' + funciones.escape($('#inputBuscarHasta').val()) : '');
		empresa = (empresa == '1' || empresa == '2' ? '&empresa=' + empresa : '');
		return url + empresa + desde + hasta;
	}

	function hayErrorGuardar(){
		if ($('#inputCliente_selectedValue').val() == '')
			return 'Debe seleccionar un cliente';
		if ($('#inputSucursal_selectedValue').val() == '')
			return 'Debe seleccionar una sucursal';
		if (funciones.objectLength(notaDePedido) == 0)
			return 'Debe elegir algún artículo';
		return false;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#radioGroupEmpresa').enableRadioGroup();
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarDesde').val() + ' al ' + $('#inputBuscarHasta').val());
				break;
			
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divResumenCajaWrapper'>
		<div id='divResumenCaja' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w180' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w180' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src='/img/botones/25/buscar.gif' /></a>
		</div>
	</div>
	<div class='botonera'>
				
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
