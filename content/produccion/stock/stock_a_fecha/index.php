<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Stock a fecha PT';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divStock').html('');
	}

	function buscar() {
		if ($('#inputBuscarAlmacen_selectedValue').val() == '' && $('#inputBuscarArticulo_selectedValue').val() == '') {
			$.error('Debe ingresar al menos un almacén o un artículo para realizar la búsqueda');
		} else {
			funciones.limpiarScreen();
			var url = funciones.controllerUrl('buscar', {
				idAlmacen: $('#inputBuscarAlmacen_selectedValue').val(),
				idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
				idColor: $('#inputBuscarColor_selectedValue').val(),
				idTipo: $('#inputBuscarTipo_selectedValue').val(),
				fecha: $('#inputBuscarFecha').val()
			});
			funciones.load($('#divStock'), url);
		}
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
			idAlmacen: $('#inputBuscarAlmacen_selectedValue').val(),
			idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
			idColor: $('#inputBuscarColor_selectedValue').val(),
			idTipo: $('#inputBuscarTipo_selectedValue').val(),
			nameAlmacen: $('#inputBuscarAlmacen_selectedName').val(),
			nameArticulo: $('#inputBuscarArticulo_selectedName').val(),
			nameColor: $('#inputBuscarColor_selectedName').val(),
			nameTipo: $('#inputBuscarTipo_selectedName').val(),
			fecha: $('#inputBuscarFecha').val()
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'buscar':
				funciones.cambiarTitulo('Stock a fecha' + ($('#inputBuscarFecha').val() ? ' (' + $('#inputBuscarFecha').val() + ')' : ''));
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divStock' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Almacén:</label>
			<input type='text' id='inputBuscarAlmacen' class='textbox obligatorio autoSuggestBox filtroBuscar w200' name='Almacen' />
		</div>
		<div>
			<label class='filtroBuscar'>Artículo:</label>
			<input type='text' id='inputBuscarArticulo' class='textbox obligatorio autoSuggestBox filtroBuscar w200' name='Articulo' />
		</div>
		<div>
			<label class='filtroBuscar'>Color:</label>
			<input type='text' id='inputBuscarColor' class='textbox autoSuggestBox filtroBuscar w200' name='ColorPorArticulo' linkedTo='inputBuscarArticulo,Articulo' />
		</div>
		<div>
			<label class='filtroBuscar'>Tipo:</label>
			<input type='text' id='inputBuscarTipo' class='textbox autoSuggestBox filtroBuscar w200' name='TipoProductoStock' />
		</div>
		<div>
			<label for='inputBuscarFecha' class='filtroBuscar'>Fecha:</label>
			<input id='inputBuscarFecha' class='textbox filtroBuscar w180' validate='Fecha' />
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