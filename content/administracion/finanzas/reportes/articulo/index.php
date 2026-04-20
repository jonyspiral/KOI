<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte artículos';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divReporteArticulos').html('');
	}

	function getParams() {
		return '&fechaDesde=' + $('#inputFechaDesde').val() + '&fechaHasta=' + $('#inputFechaHasta').val() + '&orderBy=' + $('#inputOrdenarPor').val() + '&cliente=' + $('#inputCliente_selectedValue').val() + '&articulo=' + $('#inputArticulo_selectedValue').val() + '&color=' + $('#inputColor_selectedValue').val() + '&empresa=' + $('#inputEmpresa').val();
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/finanzas/reportes/articulo/buscar.php?' + getParams();
		$.showLoading();
		$.get(url, function(result) {
			try {
				var json = $.parseJSON(result);
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
					case funciones.jsonError:
						$.error('Ocurrió un error al intentar realizar la consulta');
						break;
					case funciones.jsonInfo:
						$.info(funciones.getJSONMsg(json));
						break;
				}
			} catch (ex) {
				$('#divReporteArticulos').html(result);
				$('#divReporteArticulos').fixedHeader({target: 'table'});
				cambiarModo('buscar');
			}
			$.hideLoading();
		});
	}

	function xlsClick(){
		funciones.xlsClick(urlToExport('xls'));
	}

	function pdfClick(){
		funciones.xlsClick(urlToExport('pdf'));
	}

	function urlToExport(tipo){
		return '/content/administracion/finanzas/reportes/articulo/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?' + getParams();
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div class="divTabla">
		<div id='divReporteArticulos' class='w100p customScroll acordeon h480 '></div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputFechaDesde' class='textbox obligatorio filtroBuscar w180' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputFechaHasta' class='textbox obligatorio filtroBuscar w180' from='inputFechaHasta'' validate='Fecha' />
		</div>
		<div>
			<label for='inputCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputCliente' class='textbox autoSuggestBox filtroBuscar w200' name='Cliente' />
		</div>
		<div>
			<label for='inputArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputArticulo' class='textbox autoSuggestBox filtroBuscar w200' name='Articulo' />
		</div>
		<div>
			<label for='inputColor' class='filtroBuscar'>Color:</label>
			<input id='inputColor' class='textbox autoSuggestBox filtroBuscar w200' name='ColorPorArticulo' linkedTo="inputArticulo,Articulo" />
		</div>
		<div>
			<label for="inputEmpresa" class='filtroBuscar'>Empresa:</label>
			<select id='inputEmpresa' class='textbox filtroBuscar w200'>
				<option value='0'>Todas</option>
				<option value='1'>1</option>
				<option value='2'>2</option>
			</select>
		</div>
		<div>
			<label for="inputOrdenarPor" class='filtroBuscar'>Ordenar por:</label>
			<select id='inputOrdenarPor' class='textbox filtroBuscar w200'>
				<option value='cod_cliente'>Cliente</option>
				<option value='cod_articulo'>Artículo</option>
				<option value='monto desc'>Monto</option>
				<option value='pares desc'>Pares</option>
			</select>
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