<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Contactos';
		//Bindeo eventos click
		$('#rdCliente').click(function(){
			$('.trProveedor').hide();
			$('.trCliente').show();
		});
		$('#rdProveedor').click(function(){
			$('.trCliente').hide();
			$('.trProveedor').show();
		});
		$('#rdOtro').click(function(){
			$('.trProveedor').hide();
			$('.trCliente').hide();
		});

		<?php if (Funciones::get('buscar')) { ?>
		$('#inputBuscar, #inputBuscar_selectedValue').val(<?php echo Funciones::get('buscar'); ?>).blur();
		buscar();
		<?php } else {?>
		cambiarModo('inicio');
		<?php } ?>
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/contactos/buscar.php?idContacto=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El contacto "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos, #tablaDatos2').loadJSON(json);
				$('#inputAreaEmpresa').val(json.idAreaEmpresa).autoComplete();
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del contacto';
		if ($('#inputApellido').val() == '')
			return 'Debe ingresar el apellido del contacto';
		if (($('#rdCliente').isChecked()) && (($('#inputCliente_selectedValue').val() == '') || ($('#inputSucursal_selectedValue').val() == '')))
			return 'Debe elegir un cliente y sucursal para vincular el contacto';
		if (($('#rdProveedor').isChecked()) && ($('#inputProveedor_selectedValue').val() == ''))
			return 'Debe elegir un proveedor para vincular el contacto';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/contactos/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idContacto: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			apellido: $('#inputApellido').val(),
			tipo: $('#radioGroupTipo').radioVal(),
			idCliente: $('#inputCliente_selectedValue').val(),
			idSucursal: $('#inputSucursal_selectedValue').val(),
			idProveedor: $('#inputProveedor_selectedValue').val(),
			referencia: $('#inputReferencia').val(),
			telefono1: $('#inputTelefono1').val(),
			interno1: $('#inputInterno1').val(),
			telefono2: $('#inputTelefono2').val(),
			interno2: $('#inputInterno2').val(),
			celular: $('#inputCelular').val(),
			observaciones: $('#inputObservaciones').val(),
			email1: $('#inputEmail1').val(),
			email2: $('#inputEmail2').val(),
			calle: $('#inputCalle').val(),
			numero: $('#inputNumero').val(),
			piso: $('#inputPiso').val(),
			dpto: $('#inputDpto').val(),
			codPostal: $('#inputCodPostal').val(),
			idPais: $('#inputPais_selectedValue').val(),
			idProvincia: $('#inputProvincia_selectedValue').val(),
			idLocalidad: $('#inputLocalidad_selectedValue').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el contacto "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/contactos/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idContacto: $('#inputBuscar_selectedValue').val()};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
			case 'editar':
				$('#inputCliente').disable();
				$('#inputSucursal').disable();
				$('#inputProveedor').disable();
				$('.customRadio').disableRadioGroup();
				$('#inputNombre').focus();
				break;
			case 'agregar':
				$('#inputNombre').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divContacto1' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 9, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$rows[3]->class = 'trCliente';
			$rows[4]->class = 'trCliente';
			$rows[5]->class = 'trProveedor';
			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" rel="nombre" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Apellido:</label>';
			$cells[1][1]->content = '<input id="inputApellido" class="textbox obligatorio inputForm w230" rel="apellido" />';
			$cells[2][0]->content = '<label>Tipo:</label>';
			$cells[2][1]->content = '<div id="radioGroupTipo" class="customRadio" default="rdCliente"><input id="rdCliente" class="textbox" type="radio" name="radioGroupTipo" value="C" rel="tipo" /><label for="rdCliente">Cliente</label>' .
									 	'<input id="rdProveedor" class="textbox" type="radio" name="radioGroupTipo" value="P" rel="tipo" /><label for="rdProveedor">Proveedor</label>' . 
									 	'<input id="rdOtro" class="textbox" type="radio" name="radioGroupTipo" value="O" rel="tipo" /><label for="rdOtro">Otro</label></div>';
			$cells[3][0]->content = '<label>Cliente:</label>';
			$cells[3][1]->content = '<input id="inputCliente" class="textbox autoSuggestBox obligatorio inputForm w230" name="Cliente" alt="" rel="cliente" />';
			$cells[4][0]->content = '<label>Sucursal:</label>';
			$cells[4][1]->content = '<input id="inputSucursal" class="textbox autoSuggestBox obligatorio inputForm w230" name="Sucursal" linkedTo="inputCliente,Cliente" alt="" rel="sucursal" />';
			$cells[5][0]->content = '<label>Proveedor:</label>';
			$cells[5][1]->content = '<input id="inputProveedor" class="textbox autoSuggestBox obligatorio inputForm w230" name="Proveedor" alt="" rel="proveedor" />';
			$cells[6][0]->content = '<label>Referencia:</label>';
			$cells[6][1]->content = '<input id="inputReferencia" class="textbox inputForm w230" rel="referencia" />';
			$cells[7][0]->content = '<label>Área de la empresa:</label>';
			$cells[7][1]->content = '<input id="inputAreaEmpresa" class="textbox autoSuggestBox inputForm w230" name="AreaEmpresa" alt="" />';
			$cells[8][0]->content = '<label>Observaciones:</label>';
			$cells[8][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones" ></textarea>';

			$tabla->create();
		?>
	</div>
	<div id='divContacto2' class='fRight pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 10, 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Teléfono 1:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputTelefono1" class="textbox inputForm w135" maxlength="12" rel="telefono1" />
									<label>Int:</label>
									<input id="inputInterno1" class="textbox inputForm w50" maxlength="4" rel="interno1" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Teléfono 2:</label>';
			$cells[1][1]->content = '<input id="inputTelefono2" class="textbox inputForm w135" maxlength="12" rel="telefono2" />
									<label>Int:</label>
									<input id="inputInterno2" class="textbox inputForm w50" maxlength="4" rel="interno2" />';
			$cells[2][0]->content = '<label>Celular:</label>';
			$cells[2][1]->content = '<input id="inputCelular" class="textbox inputForm w230" maxlength="12" rel="celular" />';
			$cells[3][0]->content = '<label>Email 1:</label>';
			$cells[3][1]->content = '<input id="inputEmail1" class="textbox inputForm w230" validate="Email" rel="email1" />';
			$cells[4][0]->content = '<label>Email 2:</label>';
			$cells[4][1]->content = '<input id="inputEmail2" class="textbox inputForm w230" validate="Email" rel="email2" />';
			$cells[5][0]->content = '<label>Calle:</label>';
			$cells[5][1]->content = '<input id="inputCalle" class="textbox inputForm w230" rel="direccionCalle" />';
			$cells[6][0]->content = '<label>Número:</label>';
			$cells[6][1]->content = '<input id="inputNumero" class="textbox inputForm w65" maxlength="5" rel="direccionNumero" />
									<label>Piso:</label>
									<input id="inputPiso" class="textbox inputForm w25" maxlength="3" rel="direccionPiso" />
									<label>Dpto:</label>
									<input id="inputDpto" class="textbox inputForm w25" maxlength="3" rel="direccionDepartamento" />';
			$cells[7][0]->content = '<label>País:</label>';
			$cells[7][1]->content = '<input id="inputPais" class="textbox autoSuggestBox inputForm w230" name="Pais" alt="" rel="direccionPais" />';
			$cells[8][0]->content = '<label>Provincia:</label>';
			$cells[8][1]->content = '<input id="inputProvincia" class="textbox autoSuggestBox inputForm w230" name="Provincia" linkedTo="inputPais,Pais" alt="" rel="direccionProvincia" />';
			$cells[9][0]->content = '<label>Localidad:</label>';
			$cells[9][1]->content = '<input id="inputLocalidad" class="textbox autoSuggestBox inputForm w135" name="Localidad" linkedTo="inputPais,Pais;inputProvincia,Provincia" alt="" rel="direccionLocalidad" />
									<label>CP:</label>
									<input id="inputCodPostal" class="textbox inputForm w50" rel="direccionCodigoPostal" />';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for="inputBuscar" class='filtroBuscar'>Contacto:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Contacto' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/contactos/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/contactos/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/contactos/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
