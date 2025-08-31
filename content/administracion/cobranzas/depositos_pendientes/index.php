<?php
?>

<script type='text/javascript'>
	$(document).ready(function() {
		tituloPrograma = 'Aplicación depósitos pendientes';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getParams());
		funciones.load($('#divAplicacionDepositosPendientes'), url, function() {
			$('#divAplicacionDepositosPendientes').fixedHeader({target: 'table'});
			$('.btnConfirmar').click(guardar);
			cambiarModo('agregar');
		});
	}

	function hayErrorGuardar(obj){
		if (!obj.idCliente) {
			return 'Debe especificar un cliente para el recibo';
		}
		return false;
	}

	function guardar(e) {
		var obj = armoObjetoGuardar((e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target)),
			error = hayErrorGuardar(obj);

		if (!error) {
			$.confirm('¿Está seguro que desea asignar el recibo Nº "' + obj.numerorecibo + '" correspondiente a la empresa ' + obj.empresa + ' al cliente Nº "' + obj.idCliente + '"?', function(r) {
				if (r == funciones.si) {
					var url = funciones.controllerUrl('agregar');
					funciones.guardar(url, obj, function() {
						refreshOne(obj);
					}, null, null, false);
				}
			});
		} else {
			$.error(error);
		}
	}

	function armoObjetoGuardar(obj) {
		var retorno = {};

		retorno.numeroRecibo = obj.data('numerorecibo');
		retorno.empresa = obj.data('empresa');
		retorno.idCliente = $('#inputCliente_' + retorno.numeroRecibo + '_' + retorno.empresa + '_selectedValue').val();

		return retorno;
	}

	function refreshOne(obj) {
		$.showLoading();
		$('#row_' + obj.numeroRecibo + '_' + obj.empresa).remove();
		$.hideLoading();
	}

	function getParams(){
		return {
				fechaDesde: $('#inputBuscarFechaDesde').val(),
				fechaHasta: $('#inputBuscarFechaHasta').val(),
				nroRecibo: $('#inputBuscarNroRecibo').val(),
				empresa: $('#inputBuscarEmpresa').val()
		}
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divAplicacionDepositosPendientes').html('');
				break;
			case 'buscar':
				funciones.cambiarTitulo();
				break;
			case 'editar':
				break;
			case 'agregar':
				$('#btnPdf').show();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divAplicacionDepositosPendientes' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Rango fecha cumplido:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar w80' to='inputBuscarFechaHasta' validate='Fecha' />
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar w80' from='inputBuscarFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarNroRecibo' class='filtroBuscar'>Nro. recibo:</label>
			<input id='inputBuscarNroRecibo' class='textbox filtroBuscar w220' />
		</div>
		<div>
			<label for='inputBuscarEmpresa' class='filtroBuscar'>Empresa:</label>
			<select id='inputBuscarEmpresa' class='textbox filtroBuscar w220'>
				<option value=''>Ambas</option>
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
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
