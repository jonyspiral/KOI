<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte facturación por jurisdicción';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divReporteFacturacion').html('');
	}

	function getParams() {
		return '&fechaDesde=' + $('#inputFechaDesde').val() + '&fechaHasta=' + $('#inputFechaHasta').val() + '&orderBy=' + $('#inputOrdenarPor').val() + '&empresa=' + $('#inputEmpresa').val();
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/finanzas/reportes/facturacion_por_jurisdiccion/buscar.php?' + getParams();
		funciones.load($('#asd'), url, function() {
			$('#divReporteFacturacion').fixedHeader({target: 'table'});
		});
	}

	function xlsClick(){
		funciones.xlsClick(urlToExport('xls'));
	}

	function pdfClick(){
		funciones.xlsClick(urlToExport('pdf'));
	}

	function urlToExport(tipo){
		return '/content/administracion/finanzas/reportes/facturacion_por_jurisdiccion/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?' + getParams();
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
	<div id='asd' class='w100p customScroll'></div>
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
			<label for="inputEmpresa" class='filtroBuscar'>Empresa:</label>
			<select id='inputEmpresa' class='textbox filtroBuscar w200'>
				<option value='0'>Todas</option>
				<option value='1'>1</option>
				<option value='2'>2</option>
			</select>
		</div>
		<div>
			<label for='inputOrdenarPor' class='filtroBuscar'>Ordenar por:</label>
			<select id='inputOrdenarPor' class='textbox filtroBuscar w200'>
				<option value='total'>Total</option>
				<option value='pares'>Pares</option>
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