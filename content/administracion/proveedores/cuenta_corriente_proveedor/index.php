<?php


?>

<style>
#divCuentaCorrienteHistoricaWrapper {
	height: 490px;
}
#divCuentaCorrienteHistorica {
	padding-bottom: 10px;
}
</style>

<script type='text/javascript'>
	var cliente = null;

	$(document).ready(function(){
		tituloPrograma = 'Cuenta corriente proveedor';
		<?php if (Usuario::logueado()->esCliente()) { ?>
			cliente = {	id: '<?php echo Usuario::logueado()->contacto->cliente->id; ?>',
						razonSocial: '<?php echo Usuario::logueado()->contacto->cliente->razonSocial; ?>'};
		<?php } ?>
		cambiarModo('inicio');
		$('#radioGroupEmpresa input').click(function(){buscar();});
		<?php if (Funciones::get('idProveedor')) { ?>
			$('#inputBuscarProveedor, #inputBuscarProveedor_selectedValue').val(<?php echo Funciones::get('idProveedor'); ?>).blur();
			buscar();
		<?php } ?>
	});

	function limpiarScreen(){
		$('#divCuentaCorrienteHistorica').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscarProveedor_selectedValue').val() == '') {
			$('#radioGroupEmpresa').radioDefault();
			return $('#inputBuscarProveedor').val('');
		}

		funciones.load($('#divCuentaCorrienteHistorica'), funciones.controllerUrl('buscar', getParametros()));
	}

	function getParametros(){
		return {
			idProveedor: $('#inputBuscarProveedor_selectedValue').val(),
			nameProveedor: $('#inputBuscarProveedor_selectedName').val(),
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val(),
			empresa: $('#radioGroupEmpresa').radioVal()
		}
	}

	function pdfClick(){
		var finalUrl = urlToExport('pdf');
		if (finalUrl)
			funciones.pdfClick(finalUrl);
	}

	function xlsClick(){
		var finalUrl = urlToExport('xls');
		if (finalUrl)
			funciones.xlsClick(finalUrl);
	}

	function urlToExport(tipo){
		return funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), getParametros());
	}

	function hayErrorGuardar(){
		if ($('#inputCliente_selectedValue').val() == '')
			return 'Debe seleccionar un cliente';
		if ($('#inputSucursal_selectedValue').val() == '')
			return 'Debe seleccionar una sucursal';
		if (funciones.objectLength(notaDePedido) == 0)
			return 'Debe elegir algún artículo';
		return false;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#radioGroupEmpresa').enableRadioGroup();
				<?php if (Usuario::logueado()->esCliente()) { ?>
					$('#inputBuscarProveedor').autoComplete(cliente);
					$('#inputBuscarProveedor').disable();
				<?php } ?>
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarProveedor_selectedName').val());
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divCuentaCorrienteHistoricaWrapper'>
		<div id='divCuentaCorrienteHistorica' class='w100p customScroll'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox obligatorio autoSuggestBox filtroBuscar w200' name='Proveedor' alt='' />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w180' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w180' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div>
			<label class='filtroBuscar'>Empresa:</label>
			<div id='radioGroupEmpresa' class='customRadio w180 inline-block'>
				<input id='rdEmpresa_0' type='radio' name='radioGroupEmpresa' value='0' /><label for='rdEmpresa_0'>Ambas</label>
				<input id='rdEmpresa_1' type='radio' name='radioGroupEmpresa' value='1' /><label for='rdEmpresa_1'>1</label>
				<input id='rdEmpresa_2' type='radio' name='radioGroupEmpresa' value='2' /><label for='rdEmpresa_2'>2</label>
			</div>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
