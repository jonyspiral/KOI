<?php


?>

<style>
#divPedidosPendientesWrapper {
	height: 490px;
}
#divPedidosPendientes {
	padding-bottom: 10px;
}
</style>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Pendientes';
		cambiarModo('inicio');
		$('#inputBuscarVendedor').blur(function(){	
			if ($('#inputBuscarCliente_selectedValue').val()!=null){
				$('#inputBuscarCliente').attr('alt' , 'idVendedor=' + $('#inputBuscarVendedor_selectedValue').val());
			}	
		});

	});

	function limpiarScreen(){
		$('#divPedidosPendientes').html('');
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/comercial/pedidos/pendientes/buscar.php?',
			desde = ($('#inputBuscarDesde').val() != '' ? '&desde=' + funciones.escape($('#inputBuscarDesde').val()) : ''),
			hasta = ($('#inputBuscarHasta').val() != '' ? '&hasta=' + funciones.escape($('#inputBuscarHasta').val()) : ''),
			articulo = '&articulo=' + funciones.escape($('#inputBuscarArticulo_selectedValue').val()),
			color = '&color=' + $('#inputBuscarColor_selectedValue').val(),
			cliente = ($('#inputBuscarCliente_selectedValue').val() != '' ? '&cliente=' + funciones.escape($('#inputBuscarCliente_selectedValue').val()) : ''),
			vendedor = ($('#inputBuscarVendedor_selectedValue').val() != '' ? '&vendedor=' + funciones.escape($('#inputBuscarVendedor_selectedValue').val()) : '');
		$.showLoading();
		$.post(url + desde + hasta + articulo + color  + cliente + vendedor, function(result) {
			try {
				var json = $.parseJSON(result);
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
						$.error('Ocurrió un error al intentar realizar la consulta');
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonInfo:
						$.info(funciones.getJSONMsg(json));
						break;
				}
			} catch (ex) {
				$('#divPedidosPendientes').html(result);
				cambiarModo('buscar');
			}
			$.hideLoading();
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
		var url = '/content/comercial/pedidos/pendientes/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?',
			desde = ($('#fechaDesde').val() != '' ? '&desde=' + funciones.escape($('#inputBuscarDesde').val()) : ''),
			hasta = ($('#fechaHasta').val() != '' ? '&hasta=' + funciones.escape($('#inputBuscarHasta').val()) : ''),
			articulo = '&articulo=' + funciones.escape($('#inputBuscarArticulo_selectedValue').val()),
			color = '&color=' + $('#inputBuscarColor_selectedValue').val(),
			cliente = ($('#cliente').val() != '' ? '&cliente=' + funciones.escape($('#inputBuscarCliente_selectedValue').val()) : ''),
			vendedor = ($('#vendedor').val() != '' ? '&vendedor=' + funciones.escape($('#inputBuscarVendedor_selectedValue').val()) : ''),
			clienteName = ($('#clienteName').val() != '' ? '&clienteName=' + funciones.escape($('#inputBuscarCliente_selectedName').val()) : ''),
			vendedorName = ($('#vendedorName').val() != '' ? '&vendedorName=' + funciones.escape($('#inputBuscarVendedor_selectedName').val()) : '');
		return url + desde + hasta + articulo + color + cliente + vendedor + clienteName + vendedorName;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + '  ' + $('#inputBuscarDesde').val() + ' - ' + $('#inputBuscarHasta').val());
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divPedidosPendientesWrapper'>
		<div id='divPedidosPendientes' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog '>
	  	
	  	<?php if (!Usuario::logueado()->esCliente() && !Usuario::logueado()->esVendedor()) {?>
		<div>
			<label class='filtroBuscar '>Vendedor:</label>
			<input id='inputBuscarVendedor' class='textbox autoSuggestBox filtroBuscar  w230 ' name='Vendedor' alt='' />
		</div>
		<?php } ?>
	 	<?php if (!Usuario::logueado()->esCliente()) {?>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar  w230' name='Cliente' alt='' />
		</div>
		<?php } ?>

		<div>
			<label for='inputBuscarArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarArticulo' class='textbox autoSuggestBox filtroBuscar w230' name='Articulo' />
		</div>

		<div>
			<label for='inputBuscarColor' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColor' class='textbox autoSuggestBox filtroBuscar w230' name='ColorPorArticulo' linkedTo="inputBuscarArticulo,Articulo" />
		</div>

		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w210' to='inputBuscarHasta' validate='Fecha' />
		</div>
		
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w210' from='inputBuscarDesde' validate='Fecha' />
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
