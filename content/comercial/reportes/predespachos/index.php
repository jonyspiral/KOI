<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reimpresión de predespachos';

		$('#inputTipo').change(function() {
			if ($(this).val() == 'C') {
				$('#divBuscarCliente').show();
				$('#divBuscarPedido').hide();
			} else {
				$('#divBuscarCliente').hide();
				$('#divBuscarPedido').show();
			}
		});

		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divReimpresionPredespachos').html('');
	}

	function getParams() {
		return {
			tipo: $('#inputTipo').val(),
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			idPedido: $('#inputBuscarPedido_selectedValue').val(),
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val(),
			almacen: $('#inputAlmacen').val(),
			idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
			idColor: $('#inputBuscarColor_selectedValue').val()
		};
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getParams());
		funciones.load($('#divReimpresionPredespachos'), url);
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

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#inputTipo').change();
				break;
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divReimpresionPredespachos' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputTipo' class='filtroBuscar'>Tipo:</label>
			<select id='inputTipo' class='textbox obligatorio filtroBuscar w200'>
				<option value='C'>Por cliente</option>
				<option value='P'>Por pedido</option>
			</select>
		</div>
		<div id='divBuscarCliente'>
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w200' name='Cliente' />
		</div>
		<div id='divBuscarPedido'>
			<label for='inputBuscarPedido' class='filtroBuscar'>Pedido:</label>
			<input id='inputBuscarPedido' class='textbox obligatorio autoSuggestBox filtroBuscar w200' name='Pedido' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w180' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w180' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputAlmacen' class='filtroBuscar'>Almacén:</label>
			<select id='inputAlmacen' class='textbox filtroBuscar w200'>
				<option value='0'>Ambos</option>
				<option value='01'>01</option>
				<option value='02'>02</option>
			</select>
		</div>
		<div>
			<label for='inputBuscarArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarArticulo' class='textbox autoSuggestBox filtroBuscar w200' name='Articulo' alt='' />
		</div>
		<div>
			<label for='inputBuscarColor' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColor' class='textbox autoSuggestBox filtroBuscar w200' name='ColorPorArticulo' linkedTo='inputBuscarArticulo,Articulo' alt='' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
