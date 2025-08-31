<?php
?>

<style>
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Stock disponible';
		cambiarModo('inicio');
	});

	function checks(divId){
		var arr = [];
		var checks = $('#' + divId + ' input[type="checkbox"]:checked');
		for (var i = 0; i < checks.length; i++){
			arr.push(checks[i].value);
		}
		return arr;
	}

	function limpiarScreen(){
		$('#divStock').html('');
	}

	function ampliarFoto(link){
		$.jPopUp.show($('<div class="w600 h300 vaBottom table-cell aCenter"><img src="' + link + '" height="300" />'), [{value: 'Cerrar', action: function(){$.jPopUp.close();}}]);
	}

	function buscar() {
		if ($('#inputBuscarAlmacen_selectedValue').val() == '') {
			$.error('El filtro almacén es obligatorio');
		} else {
			funciones.limpiarScreen();
			var url = funciones.controllerUrl('buscar', {
				idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
				idColor: $('#inputBuscarColor_selectedValue').val(),
				tipoProducto: checks('divTipoProducto'),
				lineaProducto: checks('divLineaProducto'),
				clasificacionComercial: checks('divClasificacionComercial'),
				tipoStock: $('#inputTipoStock').val(),
				idAlmacen: $('#inputBuscarAlmacen_selectedValue').val()
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
			idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
			idColor: $('#inputBuscarColor_selectedValue').val(),
			tipoProducto: checks('divTipoProducto'),
			lineaProducto: checks('divLineaProducto'),
			clasificacionComercial: checks('divClasificacionComercial'),
			tipoStock: $('#inputTipoStock').val(),
			articuloName: funciones.escape($('#inputBuscarArticulo_selectedName').val()),
			colorName: funciones.escape($('#inputBuscarColor_selectedName').val()),
			idAlmacen: $('#inputBuscarAlmacen_selectedValue').val()
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#inputBuscarAlmacen').val('01').autoComplete();
				break;
			case 'buscar':
				funciones.cambiarTitulo('Stock disponible (stock ' + ($('#inputTipoStock').val() == '1' ? 'real' : 'menos pendiente') + ')');
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
			<input type='text' id='inputBuscarArticulo' class='textbox autoSuggestBox filtroBuscar w200' name='Articulo' />
		</div>
		<div>
			<label class='filtroBuscar'>Color:</label>
			<input type='text' id='inputBuscarColor' class='textbox autoSuggestBox filtroBuscar w200' name='ColorPorArticulo' linkedTo='inputBuscarArticulo,Articulo' />
		</div>
		<div>
			<label for='inputTipoStock' class='filtroBuscar'>Tipo stock:</label>
			<select id='inputTipoStock' class='textbox filtroBuscar w200'>
				<option value='0'>Stock menos pendiente</option>
				<option value='1'>Stock real</option>
			</select>
		</div>
		<div>
			<div class="p5 aLeft"><label class='filtroBuscar'>Tipo de producto:</label></div>
			<div id='divTipoProducto' class='w217 aLeft'>
				<input id='inputLanzamiento' type='checkbox' class='filtroBuscar' value='01'/>
				<label for='inputLanzamiento'>Lanzamiento</label>
				<br/>
				<input id='inputRegular' type='checkbox' class='filtroBuscar' value='02'/>
				<label for='inputRegular'>Regular</label>
				<br/>
				<input id='inputDiscontinuo' type='checkbox' class='filtroBuscar' value='04'/>
				<label for='inputDiscontinuo'>Discontinuo</label>
				<br/>
				<input id='inputOtrasMarcas' type='checkbox' class='filtroBuscar' value='05'/>
				<label for='inputOtrasMarcas'>Otras marcas</label>
				<br/>
			</div>
		</div>
		<div>
			<div class="p5 aLeft"><label class='filtroBuscar'>Linea:</label></div>
			<div id='divLineaProducto' class='w217 aLeft'>
				<input id='inputMen' type='checkbox' class='filtroBuscar' value='01'/>
				<label for='inputMen'>Men</label>
				<br/>
				<input id='inputWomen' type='checkbox' class='filtroBuscar' value='02'/>
				<label for='inputWomen'>Women</label>
				<br/>
				<input id='inputKids' type='checkbox' class='filtroBuscar' value='03'/>
				<label for='inputKids'>Kids</label>
				<br/>
				<input id='inputLittle' type='checkbox' class='filtroBuscar' value='05'/>
				<label for='inputLittle'>Little</label>
				<br/>
				<input id='inputIndumentaria' type='checkbox' class='filtroBuscar' value='06'/>
				<label for='inputIndumentaria'>Indumentaria</label>
				<br/>
			</div>
		</div>
		<div>
			<div class="p5 aLeft"><label class='filtroBuscar'>Clasificación:</label></div>
			<div id='divClasificacionComercial' class='w217 aLeft'>
                <input id='inputDsw' type='checkbox' class='filtroBuscar' value='DSW'/>
                <label for='inputDsw'>DSW</label>
                <br/>
                <input id='inputDsx' type='checkbox' class='filtroBuscar' value='DSX'/>
                <label for='inputDsx'>DSX</label>
                <br/>
                <input id='inputDsy' type='checkbox' class='filtroBuscar' value='DSY'/>
                <label for='inputDsy'>DSY</label>
                <br/>
                <input id='inputDsz' type='checkbox' class='filtroBuscar' value='DSZ'/>
                <label for='inputDsz'>DSZ</label>
                <br/>
                <input id='inputNew' type='checkbox' class='filtroBuscar' value='NEW'/>
                <label for='inputNew'>NEW</label>
                <br/>
			</div>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'window.print();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>