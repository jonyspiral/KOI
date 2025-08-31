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
		tituloPrograma = 'Estadísticas';
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

	function getParams() {
		var desde = ($('#inputBuscarDesde').val() != '' ? '&desde=' + funciones.escape($('#inputBuscarDesde').val()) : ''),
			hasta = ($('#inputBuscarHasta').val() != '' ? '&hasta=' + funciones.escape($('#inputBuscarHasta').val()) : ''),
			idCliente = ($('#inputBuscarCliente_selectedValue').val() != '' ? '&idCliente=' + funciones.escape($('#inputBuscarCliente_selectedValue').val()) : ''),
			idVendedor = ($('#inputBuscarVendedor_selectedValue').val() != '' ? '&idVendedor=' + funciones.escape($('#inputBuscarVendedor_selectedValue').val()) : ''),
			idAlmacen = ($('#radioGroupAlmacen').radioVal() != '0' ? '&idAlmacen=' + funciones.escape($('#radioGroupAlmacen').radioVal()) : '');
			idArticulo = ($('#inputBuscarArticulo_selectedValue').val() != '' ? '&idArticulo=' + funciones.escape($('#inputBuscarArticulo_selectedValue').val()) : ''),
			idColor = ($('#inputBuscarColor_selectedValue').val() != '' ? '&idColor=' + funciones.escape($('#inputBuscarColor_selectedValue').val()) : ''),
			modo = '&modo=' + funciones.escape($('#inputModo').val());
			tipoProducto = '&tipoProducto=' + funciones.escape(checks());
		return desde + hasta + idCliente + idVendedor + idAlmacen + idArticulo + idColor + tipoProducto + modo;
	}

	function hayErrorBuscar() {
		var desde = $('#inputBuscarDesde').val(),
			hasta = $('#inputBuscarHasta').val(),
			cliente = $('#inputBuscarCliente_selectedValue').val(),
			vendedor = $('#inputBuscarVendedor_selectedValue').val(),
			color = $('#inputBuscarColor_selectedValue').val();
		if (desde == '' && hasta == '') {
			$.error('Debe ingresar una fecha desde o hasta');
			return true;
		}
		if (cliente == '' || vendedor == '' || color == '') {
			var msg = 'Si no ingresa un cliente, vendedor o artículo, esta consulta va a demorar aproximadamente 1 minuto y 30 segundos';
			msg += '¿Está seguro que desea continuar con la consulta?';
			$.confirm(msg, function(respuesta) {
				return !respuesta;
			});
		}
	}

	function goBuscar() {
		var url = '/content/comercial/pedidos/estadisticas/buscar.php?' + getParams();
		$.showLoading();
		$.post(url, function(result) {
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

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		//Hago las comprobaciones para buscar
		var desde = $('#inputBuscarDesde').val(),
			hasta = $('#inputBuscarHasta').val(),
			cliente = $('#inputBuscarCliente_selectedValue').val(),
			vendedor = $('#inputBuscarVendedor_selectedValue').val(),
			color = $('#inputBuscarColor_selectedValue').val();
		if (desde == '' && hasta == '') {
			$.error('Debe ingresar una fecha desde o hasta');
		} else if (cliente == '' && vendedor == '' && color == '') {
			var msg = 'Si no ingresa un cliente, vendedor o artículo, esta consulta va a demorar aproximadamente 2 minutos. ';
			msg += '¿Está seguro de que desea continuar con la consulta?';
			$.confirm(msg, function(respuesta) {
				if (respuesta == funciones.si) {
					goBuscar();
				}
			});
		} else {
			goBuscar();
		}
	}

	function checks(){
		var val = [];
		var checks = $('#divTipoProducto :checkbox:checked');
		for (var i = 0; i < checks.length; i++){
			val[i] = checks[i].value;
		}
		return val;
	}

	function pdfClick(){
		funciones.pdfClick(urlToExport('pdf'));
	}

	function xlsClick(){
		funciones.xlsClick(urlToExport('xls'));
	}

	function urlToExport(tipo){
		var url = '/content/comercial/pedidos/estadisticas/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?' + getParams();
		return url;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#radioGroupAlmacen').enableRadioGroup();
				$('#inputBuscarVendedor').focus();
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
			<label class='filtroBuscar'>Almacén:</label>
			<div id='radioGroupAlmacen' class='customRadio w180 inline-block' default='rdAlmacen_0'>
				<input id='rdAlmacen_0' type='radio' name='radioGroupAlmacen' value='0' /><label for='rdAlmacen_0'>Ambos</label>
				<input id='rdAlmacen_1' type='radio' name='radioGroupAlmacen' value='01' /><label for='rdAlmacen_1'>1</label>
				<input id='rdAlmacen_2' type='radio' name='radioGroupAlmacen' value='02' /><label for='rdAlmacen_2'>2</label>
			</div>
		</div>
	  	<div>
			<label class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarArticulo' class='textbox autoSuggestBox filtroBuscar  w190' name='Articulo' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColor' class='textbox autoSuggestBox filtroBuscar  w190' name='ColorPorArticulo' linkedTo='inputBuscarArticulo,Articulo' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' validate='Fecha' />
		</div>
		
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div class='fLeft'>
			<label class='filtroBuscar fLeft pRight3'>Tipo de producto:</label>
			<div id='divTipoProducto' class='fRight w206 aLeft'>
				<input id='inputLanzamiento' type='checkbox' class='filtroBuscar' value='01'/>
				<label class='cPointer' for='inputLanzamiento'>Lanzamiento</label>
				<br/>
				<input id='inputRegular' type='checkbox' class='filtroBuscar' value='02'/>
				<label class='cPointer' for='inputRegular'>Regular</label>
				<br/>
				<input id='inputDiscontinuo' type='checkbox' class='filtroBuscar' value='04'/>
				<label class='cPointer' for='inputDiscontinuo'>Discontinuo</label>
				<br/>
				<input id='inputOtrasMarcas' type='checkbox' class='filtroBuscar' value='05'/>
				<label class='cPointer' for='inputOtrasMarcas'>Otras marcas</label>
				<br/>
				<input id='inputMarketing' type='checkbox' class='filtroBuscar' value='07'/>
				<label class='cPointer' for='inputMarketing'>Marketing</label>
				<br/>
                <input id='inputPrelanzamiento' type='checkbox' class='filtroBuscar' value='08'/>
                <label class='cPointer' for='inputPrelanzamiento'>Prelanzamiento</label>
			</div>
	  	</div>
	  	<div>
			<label class='filtroBuscar'>Modo:</label>
			<select id='inputModo' class='textbox filtroBuscar w205'>
				<option value='1'>Clientes por vendedor</option>
				<option value='2'>Artículos</option>
				<option value='3'>Artículos por cliente</option>
				<option value='4'>Clientes por artículo</option>
				<option value='5'>Clientes</option>
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
