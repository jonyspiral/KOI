<?php
?>

<style>
#divPedidosHistoricoWrapper {
	height: 490px;
}
#divPedidosHistorico {
	padding-bottom: 10px;
}
</style>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Pedidos historico';
		cambiarModo('inicio');
		$('#inputBuscarVendedor').blur(function(){	
			if ($('#inputBuscarCliente_selectedValue').val() != null){
				$('#inputBuscarCliente').attr('alt' , 'idVendedor=' + $('#inputBuscarVendedor_selectedValue').val());
			}	
		});

	});

	function limpiarScreen(){
		$('#divPedidosHistorico').html('');
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/comercial/pedidos/historico/buscar.php?',
			desde = ($('#fechaDesde').val() != '' ? '&desde=' + funciones.escape($('#inputBuscarDesde').val()) : ''),
			hasta = ($('#fechaHasta').val() != '' ? '&hasta=' + funciones.escape($('#inputBuscarHasta').val()) : ''),
			cliente = ($('#cliente').val() != '' ? '&cliente=' + funciones.escape($('#inputBuscarCliente_selectedValue').val()) : ''),
			vendedor = ($('#vendedor').val() != '' ? '&vendedor=' + funciones.escape($('#inputBuscarVendedor_selectedValue').val()) : '');
		$.showLoading();
		$.post(url + desde + hasta + cliente + vendedor, function(result) {
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
				$('#divPedidosHistorico').html(result);
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
		var url = '/content/comercial/pedidos/historico/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?',
			desde = ($('#fechaDesde').val() != '' ? '&desde=' + funciones.escape($('#inputBuscarDesde').val()) : ''),
			hasta = ($('#fechaHasta').val() != '' ? '&hasta=' + funciones.escape($('#inputBuscarHasta').val()) : '');
			cliente = ($('#cliente').val() != '' ? '&cliente=' + funciones.escape($('#inputBuscarCliente_selectedValue').val()) : ''),
			vendedor = ($('#vendedor').val() != '' ? '&vendedor=' + funciones.escape($('#inputBuscarVendedor_selectedValue').val()) : ''),
			clienteName = ($('#clienteName').val() != '' ? '&clienteName=' + funciones.escape($('#inputBuscarCliente_selectedName').val()) : ''),
			vendedorName = ($('#vendedorName').val() != '' ? '&vendedorName=' + funciones.escape($('#inputBuscarVendedor_selectedName').val()) : '');
		return url + desde + hasta + cliente + vendedor + clienteName + vendedorName;
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
	<div id='divPedidosHistoricoWrapper'>
		<div id='divPedidosHistorico' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog '>
	  	
	  	<?php if (!Usuario::logueado()->esCliente() && !Usuario::logueado()->esVendedor()) {?>
		<div>
			<label class='filtroBuscar '>Vendedor:</label>
			<input id='inputBuscarVendedor' class='textbox autoSuggestBox filtroBuscar  w190 ' name='Vendedor' alt='' />
		</div>
		<?php } ?>
	 	<?php if (!Usuario::logueado()->esCliente()) {?>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar  w190' name='Cliente' alt='' />
		</div>
		<?php } ?>
	  	
		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' validate='Fecha' />
		</div>
		
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarDesde' validate='Fecha' />
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
