<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'IVA Compras';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divReporteFacturacion').html('');
	}

	function getParams() {
		return '&fechaDesde=' + $('#inputFechaDesde').val() + '&fechaHasta=' + $('#inputFechaHasta').val() + '&orderBy=' + $('#inputOrdenarPor').val() + '&docFAC=' + $('#checkboxFAC').is(':checked') + '&docNCR=' + $('#checkboxNCR').is(':checked') + '&docNDB=' + $('#checkboxNDB').is(':checked') + '&proveedor=' + $('#inputProveedor_selectedValue').val() + '&tipoReporte=' + $('#inputTipoReporte').val() + '&empresa=' + $('#inputEmpresa').val() + '&tipoFecha=' + $('#inputFecha').val() + '&tipoDocumento=' + $('#inputFacturaGastos').val();
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/proveedores/facturacion/buscar.php?' + getParams();
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
		return '/content/administracion/proveedores/facturacion/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?' + getParams();
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
			<input id='inputFechaDesde' class='textbox filtroBuscar w180' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputFechaHasta' class='textbox filtroBuscar w180' from='inputFechaHasta'' validate='Fecha' />
		</div>
		<div>
			<label for='inputFecha' class='filtroBuscar'>Tipo fecha:</label>
			<select id='inputFecha' class='textbox filtroBuscar w200'>
				<option value='F'>Fiscal</option>
				<option value='D'>Documento</option>
			</select>
		</div>
		<div>
			<label for='inputFacturaGastos' class='filtroBuscar'>Tipo documento:</label>
			<select id='inputFacturaGastos' class='textbox filtroBuscar w200'>
				<option value='X'>Todos</option>
				<option value='N'>Proveedor</option>
				<option value='S'>Gastos</option>
			</select>
		</div>
		<div>
			<label for='inputProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputProveedor' class='textbox autoSuggestBox filtroBuscar w200' name='Proveedor' />
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