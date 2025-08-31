<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Repoorte retiro/aporte de socios';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divReporteRetiroAporteSocio').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getParams());
		funciones.load($('#divReporteRetiroAporteSocio'), url);
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

	function getParams(){
		return {
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			operacion: $('#inputBuscarOperacion').val()
		}
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
		<div id='divReporteRetiroAporteSocio' class='w100p customScroll acordeon h480 '></div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarOperacion' class='filtroBuscar'>Operación:</label>
			<select id='inputBuscarOperacion' class='textbox filtroBuscar w235'>
				<option value='0'>Todas</option>
				<option value='1'>Aporte de socio</option>
				<option value='2'>Retiro de socio</option>
			</select>
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar' title='Corresponde a la fecha de creación de la órden de compra'>Rango fecha:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w80' to='inputFechaHasta' validate='Fecha' />
			<input id='inputFechaHasta' class='textbox filtroBuscar w80' from='inputFechaDesde' validate='Fecha' />
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