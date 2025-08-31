<?php
?>

<style>
	.tableRow > td {
		font-size: 12px;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte programación empaque';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divReporteProgramacionEmpaque').html('');
	}

	function getParams() {
		return {
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			fechaDesdeEmpaque: $('#inputFechaDesdeEmpaque').val(),
			fechaHastaEmpaque: $('#inputFechaHastaEmpaque').val(),
			lote: $('#inputLote').val(),
			tarea: $('#inputTarea').val(),
			articulo: $('#inputArticulo_selectedValue').val(),
			anulado: $('#inputAnulado').val(),
			cumplidoPaso: $('#inputCumplidoPaso').val(),
			tipoTarea: $('#inputTipoTarea').val(),
			situacion: $('#inputSituacion').val(),
			orderBy: $('#inputOrdenarPor').val()
		};
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getParams());
		funciones.load($('#divReporteProgramacionEmpaque'), url, function() {
			$('#divReporteProgramacionEmpaque').fixedHeader({target: 'table'});
		});
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
				break;
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divReporteProgramacionEmpaque' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputFechaDesdeEmpaque' class='filtroBuscar'>Rango fecha empaque:</label>
			<input id='inputFechaDesdeEmpaque' class='textbox filtroBuscar w80' to='inputFechaHastaEmpaque' validate='Fecha' />
			<input id='inputFechaHastaEmpaque' class='textbox filtroBuscar w80' from='inputFechaDesdeEmpaque' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Rango fecha inicio:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w80' to='inputFechaHasta' validate='Fecha' />
			<input id='inputFechaHasta' class='textbox filtroBuscar w80' from='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputLote' class='filtroBuscar'>Lote:</label>
			<input id='inputLote' class='textbox filtroBuscar w220' validate="Numero" />
		</div>
		<div>
			<label for='inputTarea' class='filtroBuscar'>Tarea:</label>
			<input id='inputTarea' class='textbox filtroBuscar w220' validate="Numero" />
		</div>
		<div>
			<label for='inputArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputArticulo' class='textbox autoSuggestBox filtroBuscar w220' name='Articulo' />
		</div>
		<div>
			<label for='inputAnulado' class='filtroBuscar'>Anulado:</label>
			<select id='inputAnulado' class='textbox filtroBuscar w220'>
				<option value='N'>No</option>
				<option value='S'>Si</option>
			</select>
		</div>
		<div>
			<label for='inputCumplidoPaso' class='filtroBuscar'>Cumplido paso:</label>
			<select id='inputCumplidoPaso' class='textbox filtroBuscar w220'>
				<option value='N'>No</option>
				<option value='S'>Si</option>
				<option value=''>Todas</option>
			</select>
		</div>
		<div>
			<label for='inputTipoTarea' class='filtroBuscar'>Tipo tarea:</label>
			<select id='inputTipoTarea' class='textbox filtroBuscar w220'>
				<option value=''>Todas</option>
				<option value='D'>Derivadas</option>
				<option value='N'>No derivadas</option>
			</select>
		</div>
		<div>
			<label for='inputSituacion' class='filtroBuscar'>Situación tarea:</label>
			<select id='inputSituacion' class='textbox filtroBuscar w220'>
				<option value=''>Todas</option>
				<option value='I'>Iniciada</option>
				<option value='T'>Terminada</option>
				<option value='S'>Suspendida</option>
				<option value='P'>Programada</option>
			</select>
		</div>
		<div>
			<label for='inputOrdenarPor' class='filtroBuscar'>Ordenar por:</label>
			<select id='inputOrdenarPor' class='textbox filtroBuscar w220'>
				<option value='0'>Fecha descendente</option>
				<option value='1'>Articulo</option>
				<option value='2'>Tarea</option>
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