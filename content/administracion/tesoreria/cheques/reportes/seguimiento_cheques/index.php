<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Seguimiento de cheques';
		cambiarModo('inicio');
	});

	function limpiarScreen() {
		$('#divSeguimientoCheques').html('');
	}

	function armoObjetoBuscar() {
		return {
			idCliente:			$('#inputCliente_selectedValue').val(),
			diasDesde:			$('#inputDiasDesde').val(),
			diasHasta:			$('#inputDiasHasta').val(),
			importeDesde:		$('#inputImporteDesde').val(),
			importeHasta:		$('#inputImporteHasta').val(),
			fechaDesde:			$('#inputFechaDesde').val(),
			fechaHasta:			$('#inputFechaHasta').val(),
			idCuentaBancaria:	$('#inputCuentaBancaria_selectedValue').val(),
			idCaja:				$('#inputCaja_selectedValue').val(),
			tipo:				$('#inputTipo').val(),
			numero:				$('#inputNumero').val(),
			rechazado:			$('#inputRechazado').val(),
			orden:				$('#inputOrden').val()
		};
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', armoObjetoBuscar());
		funciones.load($('#divSeguimientoCheques'), url, function() {
			$('.acordeon').acordeon({fixedHeight: false});
		});
	}

	function pdfClick(){
		funciones.pdfClick(funciones.controllerUrl('getPdf', armoObjetoBuscar()));
	}

	function xlsClick(){
		funciones.xlsClick(funciones.controllerUrl('getXls', armoObjetoBuscar()));
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('#radioGroupAlmacen').enableRadioGroup();
		switch (modo){
			case 'inicio':
				$('.pantalla').hide();
				break;
			case 'buscar':
				$('.pantalla').show();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class=''>
	<div class="pantalla">
		<table cellspacing="1" border="0" style="width: 99%">
			<thead class="tableHeader">
				<tr class="tableRow">
					<th class="tableHeader" title="Número de cheque" style="width: 8%; ">Número</th>
					<th class="tableHeader" title="Fecha de vencimiento" style="width: 7%; ">F. Vto.</th>
					<th class="tableHeader" title="Importe" style="width: 9%; ">Importe</th>
					<th class="tableHeader" title="Banco" style="width: 19%; ">Banco</th>
					<th class="tableHeader" title="Librador" style="width: 15%; ">Librador</th>
					<th class="tableHeader" title="Cliente" style="width: 19%; ">Cliente</th>
					<th class="tableHeader" title="Proveedor" style="width: 23%; ">Proveedor</th>
				</tr>
			</thead>
		</table>
		<div id='divSeguimientoCheques' class='w100p customScroll acordeon h480'>
			<?php // TABLOTA ?>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputCliente' class='textbox autoSuggestBox filtroBuscar w220' name='Cliente' alt='' />
		</div>
		<div>
			<label for='inputNumero' class='filtroBuscar'>Número de cheque:</label>
			<input id='inputNumero' class='textbox filtroBuscar w220' />
		</div>
		<div>
			<label for='inputDiasDesde' class='filtroBuscar'>Rango días vto.:</label>
			<input id='inputDiasDesde' class='textbox filtroBuscar w100' />
			<input id='inputDiasHasta' class='textbox filtroBuscar w100' />
		</div>
		<div>
			<label for='inputImporteDesde' class='filtroBuscar'>Rango importe:</label>
			<input id='inputImporteDesde' class='textbox filtroBuscar w100' />
			<input id='inputImporteHasta' class='textbox filtroBuscar w100' />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Rango fecha vto.:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w80' to='inputFechaHasta' validate='Fecha' />
			<input id='inputFechaHasta' class='textbox filtroBuscar w80' from='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputCuentaBancaria' class='filtroBuscar'>Cuenta bancaria:</label>
			<input id='inputCuentaBancaria' class='textbox autoSuggestBox filtroBuscar w220' name='CuentaBancaria' />
		</div>
		<div>
			<label for='inputCaja' class='filtroBuscar'>Caja:</label>
			<input id='inputCaja' class='textbox autoSuggestBox filtroBuscar w220' name='CajaPorUsuario' />
		</div>
		<div>
			<label for="inputtipo" class='filtroBuscar'>Propio o de 3ros:</label>
			<select id='inputTipo' class='textbox filtroBuscar w220'>
				<option value='0'>Todos</option>
				<option value='1'>Propios</option>
				<option value='2'>De terceros</option>
			</select>
		</div>
		<div>
			<label for="inputRechazado" class='filtroBuscar'>Rechazado:</label>
			<select id='inputRechazado' class='textbox filtroBuscar w220'>
				<option value='0'>Todos</option>
				<option value='1'>Sí</option>
				<option value='2'>No</option>
			</select>
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w220'>
				<option value='0'>Fecha de vto. ascendente</option>
				<option value='1'>Fecha de vto. descendente</option>
				<option value='2'>Importe ascendente</option>
				<option value='3'>Importe decendente</option>
				<option value='4'>Número ascendente</option>
				<option value='5'>Número descendente</option>
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
