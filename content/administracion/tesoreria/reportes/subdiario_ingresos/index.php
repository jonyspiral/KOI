<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Subdiario de ingresos';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divSubdiarioIngresos').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getParams());
		funciones.load($('#divSubdiarioIngresos'), url);
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

	function getParams() {
		return {
			empresa: $('#inputBuscarEmpresa').val(),
			tipoRecibo: $('#inputBuscarTipoRecibo').val(),
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			idVendedor: $('#inputBuscarVendedor_selectedValue').val(),
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarDesde').val() + ' al ' + $('#inputBuscarHasta').val());
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divSubdiarioIngresos' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox obligatorio filtroBuscar w180' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w180' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarTipoRecibo' class='filtroBuscar'>Tipo recibo:</label>
			<select id='inputBuscarTipoRecibo' class='textbox filtroBuscar w200'>
				<option value='0'>Todos</option>
				<option value='1'>Cobranza Deudores</option>
				<option value='2'>Otros Ingresos</option>
			</select>
		</div>
		<div>
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w200' name='ClienteTodos' alt='' />
		</div>
		<div>
			<label for='inputBuscarVendedor' class='filtroBuscar'>Vendedor:</label>
			<input id='inputBuscarVendedor' class='textbox autoSuggestBox filtroBuscar w200' name='Vendedor' alt='' />
		</div>
		<div>
			<label for='inputBuscarEmpresa' class='filtroBuscar'>Empresa:</label>
			<select id='inputBuscarEmpresa' class='textbox filtroBuscar w200'>
				<option value='0'>Ambas</option>
				<option value='1'>1</option>
				<option value='2'>2</option>
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
