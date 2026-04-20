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
		tituloPrograma = 'Cuenta corriente';
		<?php if (Usuario::logueado()->esCliente()) { ?>
			cliente = {	id: '<?php echo Usuario::logueado()->contacto->cliente->id; ?>',
						razonSocial: '<?php echo Usuario::logueado()->contacto->cliente->razonSocial; ?>'};
		<?php } ?>
		cambiarModo('inicio');
		$('#radioGroupEmpresa input').click(function(){buscar();});
		<?php if (Funciones::get('idCliente')) { ?>
			$('#inputBuscarCliente, #inputBuscarCliente_selectedValue').val(<?php echo Funciones::get('idCliente'); ?>).blur();
			$('#inputBuscarDesde').val('<?php echo Funciones::get('desde'); ?>');
			buscar();
		<?php } ?>
	});

	function limpiarScreen(){
		$('#divCuentaCorrienteHistorica').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', armoObjetoBuscar());
		funciones.load($('#divCuentaCorrienteHistorica'), url, function(){
			$('#divCuentaCorrienteHistorica').fixedHeader({target: 'table'});
			$('.recibo').click(function(e) {
				funciones.pdfClick(funciones.controllerUrl('getPdfRec', {id: $(e.target).parents('tr').attr('id'), empresa: ($(e.target).parents('tr').hasClass('bold') ? '1' : '2')}));
			});
		});
	}

	function armoObjetoBuscar() {
		var e = $('#radioGroupEmpresa').radioVal();
		return {
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val(),
			empresa: (e == '1' || e == '2') ? e : ''
		};
	}

	function sendMail() {
		funciones.sendMail(funciones.controllerUrl('sendMail', armoObjetoBuscar()));
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
		var url = funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), armoObjetoBuscar());
		return url;
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
				$('#btnMail').hide();
				$('#radioGroupEmpresa').enableRadioGroup();
				<?php if (Usuario::logueado()->esCliente()) { ?>
					$('#inputBuscarCliente').autoComplete(cliente);
					$('#inputBuscarCliente').disable();
					$('#btnRendir').hide();
				<?php } ?>
				break;
			case 'buscar':
				$('#btnMail').show();
				funciones.cambiarTitulo(tituloPrograma + ' - [' + $('#inputBuscarCliente_selectedValue').val() + '] ' + $('#inputBuscarCliente_selectedName').val());
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
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox obligatorio autoSuggestBox filtroBuscar w200' name='Cliente' alt='' />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Fecha doc. desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w180' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Fecha doc. hasta:</label>
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
		<?php Html::echoBotonera(array('boton' => 'mail', 'accion' => 'sendMail();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
