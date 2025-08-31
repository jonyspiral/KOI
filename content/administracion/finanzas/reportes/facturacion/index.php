<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'IVA Ventas';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divReporteFacturacion').html('');
	}

	function getParams() {
		return {
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			orderBy: $('#inputOrdenarPor').val(),
			docFAC: $('#checkboxFAC').is(':checked'),
			docNCR: $('#checkboxNCR').is(':checked'),
			docNDB: $('#checkboxNDB').is(':checked'),
			cliente: $('#inputCliente_selectedValue').val(),
			tipoReporte: $('#inputTipoReporte').val(),
			empresa: $('#inputEmpresa').val()
		};
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getParams());
		funciones.load($('#divReporteFacturacion'), url, function() {
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
		return funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), getParams());
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#checkboxFAC').check();
				$('#checkboxNDB').check();
				$('#checkboxNCR').check();
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
			<label for='inputFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputFechaDesde' class='textbox obligatorio filtroBuscar w180' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputFechaHasta' class='textbox obligatorio filtroBuscar w180' from='inputFechaHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputCliente' class='textbox autoSuggestBox filtroBuscar w200' name='Cliente' />
		</div>
		<div>
			<label class='filtroBuscar fLeft'>Documentos:</label>
			<div class="filtroBuscar inline-block w215 aLeft">
				<label for='checkboxFAC' class='filtroBuscar'>FAC</label>
				<input type='checkbox' class='textbox koiCheckbox' id='checkboxFAC' />
				<br/>
				<label for='checkboxNCR' class='filtroBuscar'>NCR</label>
				<input type='checkbox' class='textbox koiCheckbox' id='checkboxNCR' />
				<br/>
				<label for='checkboxNDB' class='filtroBuscar'>NDB</label>
				<input type='checkbox' class='textbox koiCheckbox' id='checkboxNDB' />
			</div>
		</div>
		<div>
			<label for='inputTipoReporte' class='filtroBuscar'>Tipo reporte:</label>
			<select id='inputTipoReporte' class='textbox filtroBuscar w200'>
				<option value='D'>Detallado</option>
				<option value='T'>Totales</option>
			</select>
		</div>
		<div>
			<label for='inputEmpresa' class='filtroBuscar'>Empresa:</label>
			<select id='inputEmpresa' class='textbox filtroBuscar w200'>
				<option value='0'>Todas</option>
				<option value='1'>1</option>
				<option value='2'>2</option>
			</select>
		</div>
		<div>
			<label for='inputOrdenarPor' class='filtroBuscar'>Ordenar por:</label>
			<select id='inputOrdenarPor' class='textbox filtroBuscar w200'>
				<option value='fecha'>Fecha</option>
				<option value='cod_cliente'>Cliente</option>
				<option value='total desc'>Total</option>
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