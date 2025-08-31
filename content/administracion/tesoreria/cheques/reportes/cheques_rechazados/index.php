<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte cheques rechazados';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divChequesRechazados').html('');
	}

	function getParams() {
		return '&fechaDesde=' + $('#inputFechaDesde').val() + '&fechaHasta=' + $('#inputFechaHasta').val() + '&fechaVtoDesde=' + $('#inputFechaVtoDesde').val() +
			   '&fechaVtoHasta=' + $('#inputFechaVtoHasta').val() + '&orderBy=' + $('#inputOrdenarPor').val() + '&empresa=' + $('#inputEmpresa').val() +
			   '&idCliente=' + $('#inputCliente_selectedValue').val() + '&numero=' + $('#inputNumeroCheque').val() + '&librador=' + $('#inputLibrador').val();
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content' + window.location.pathname + 'buscar.php?' + getParams();
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
				$('#divChequesRechazados').html(result);
				$('#divChequesRechazados').fixedHeader({target: 'table'});
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
		return '/content' + window.location.pathname + 'get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?' + getParams();
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
	<div id='divChequesRechazados' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputCliente' class='textbox autoSuggestBox filtroBuscar w200' name='ClienteTodos' />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Fecha rechazo desde:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w180' to='fechaHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha rechazo hasta:</label>
			<input id='inputFechaHasta' class='textbox filtroBuscar w180' from='fechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaVtoDesde' class='filtroBuscar'>Fecha vto. desde:</label>
			<input id='inputFechaVtoDesde' class='textbox filtroBuscar w180' to='fechaVtoHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaVtoHasta' class='filtroBuscar'>Fecha vto. hasta:</label>
			<input id='inputFechaVtoHasta' class='textbox filtroBuscar w180' from='fechaVtoDesde' validate='Fecha' />
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
			<label for='inputNumeroCheque' class='filtroBuscar'>Número cheque:</label>
			<input id='inputNumeroCheque' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputLibrador' class='filtroBuscar'>Librador:</label>
			<input id='inputLibrador' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputOrdenarPor' class='filtroBuscar'>Ordenar por:</label>
			<select id='inputOrdenarPor' class='textbox filtroBuscar w200'>
				<option value='fecha_vencimiento'>Fecha vto.</option>
				<option value='cod_cli'>Cliente</option>
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