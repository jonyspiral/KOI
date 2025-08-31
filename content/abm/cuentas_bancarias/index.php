<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Cuentas bancarias';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = funciones.controllerUrl('buscar', {id: $('#inputBuscar_selectedValue').val()}),
			msgError = 'La cuenta bancaria "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputBanco_selectedValue').val() == '')
			return 'Debe seleccionar el banco de la cuenta bancaria';
		if ($('#inputSucursal_selectedValue').val() == '')
			return 'Debe seleccionar la sucursal de la cuenta bancaria';
		if ($('#inputNombreCuenta').val() == '')
			return 'Debe ingresar el nombre de la cuenta bancaria';
		if ($('#inputProveedor_selectedValue').val() == '')
			return 'Debe seleccionar el proveedor de la cuenta bancaria (suele ser el banco)';
		if ($('#inputCaja_selectedValue').val() == '')
			return 'Debe seleccionar una caja para la cuenta bancaria';
		if ($('#inputImputacion_selectedValue').val() == '')
			return 'Debe seleccionar la imputación de la cuenta bancaria';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		funciones.guardar(funciones.controllerUrl(aux), armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			idBanco: $('#inputBanco_selectedValue').val(),
			idSucursal: $('#inputSucursal_selectedValue').val(),
			nombreCuenta: $('#inputNombreCuenta').val(),
			numeroCuenta: $('#inputNumeroCuenta').val(),
			idProveedor: $('#inputProveedor_selectedValue').val(),
			idCaja: $('#inputCaja_selectedValue').val(),
			idImputacion: $('#inputImputacion_selectedValue').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la cuenta bancaria "' + $('#inputBuscar_selectedName').val() + '"?';
		funciones.borrar(msg, funciones.controllerUrl('borrar'), armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {id: $('#inputBuscar_selectedValue').val()};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
			case 'editar':
				$('#inputNombreCuenta').focus();
				break;
			case 'agregar':
				$('#inputBanco').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divCampos' class='pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 10, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Banco:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputBanco" class="textbox obligatorio autoSuggestBox inputForm noEditable w230" name="Banco" rel="banco" />';
			$cells[0][1]->style->width = '230px';
			$cells[1][0]->content = '<label>Sucursal:</label>';
			$cells[1][1]->content = '<input id="inputSucursal" class="textbox obligatorio autoSuggestBox inputForm noEditable w230" name="BancoPropio" rel="sucursal" linkedTo="inputBuscar,Banco" />';

			$cells[2][0]->content = '<label>Nombre cuenta:</label>';
			$cells[2][1]->content = '<input id="inputNombreCuenta" class="textbox obligatorio inputForm w230" type="text" rel="nombreCuenta" />';

			$cells[3][0]->content = '<label>Numero cuenta:</label>';
			$cells[3][1]->content = '<input id="inputNumeroCuenta" class="textbox inputForm w230" type="text" rel="numeroCuenta" />';

			$cells[4][0]->content = '<label>Proveedor:</label>';
			$cells[4][1]->content = '<input id="inputProveedor" class="textbox obligatorio autoSuggestBox inputForm w230" name="Proveedor" rel="proveedor" />';

			$cells[5][0]->content = '<label>Caja:</label>';
			$cells[5][1]->content = '<input id="inputCaja" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaPorUsuario" rel="caja" />';

			$cells[6][0]->content = '<label>Imputación:</label>';
			$cells[6][1]->content = '<input id="inputImputacion" class="textbox obligatorio autoSuggestBox inputForm w230" name="Imputacion" rel="imputacion" />';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Cuenta bancaria:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='CuentaBancaria' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/cuentas_bancarias/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/cuentas_bancarias/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/cuentas_bancarias/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
