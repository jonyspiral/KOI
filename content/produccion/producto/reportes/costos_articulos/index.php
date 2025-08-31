<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte Costos Artículos';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divReporteFacturacion').html('');
	}

	function getParams() {
		return '&idArticulo=' + $('#inputArticulo_selectedValue').val() + '&idColor=' + $('#inputColor_selectedValue').val() + '&tipoReporte=' + $('#inputTipoReporte').val();
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/produccion/producto/reportes/costos_articulos/buscar.php?' + getParams();
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
				$('#divReporteFacturacion').html(result);
				$('#divReporteFacturacion').fixedHeader({target: 'table'});
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
		return '/content/produccion/producto/reportes/costos_articulos/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?' + getParams();
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
	<div id='divReporteFacturacion' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputArticulo' class='textbox autoSuggestBox filtroBuscar w230' name='Articulo' />
		</div>
		<div>
			<label for='inputColor' class='filtroBuscar'>Color:</label>
			<input id='inputColor' class='textbox autoSuggestBox filtroBuscar w230' name='ColorPorArticulo' linkedTo="inputArticulo,Articulo" />
		</div>
		<div>
			<label for='inputTipoReporte' class='filtroBuscar'>Tipo de reporte:</label>
			<select id='inputTipoReporte' class='textbox filtroBuscar w230'>
				<option value='A'>Agrupado</option>
				<option value='D'>Detallado</option>
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