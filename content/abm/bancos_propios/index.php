<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Bancos propios';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscar_selectedValue').val() == '' || $('#inputBuscarSucursal_selectedValue').val() == '')
			return $('#inputBuscar, #inputBuscarSucursal').val('');
		var url = funciones.controllerUrl('buscar', {
				idBanco: $('#inputBuscar_selectedValue').val(),
				idSucursal: $('#inputBuscarSucursal_selectedValue').val()
			}),
			msgError = 'El banco propio "' + $('#inputBuscar_selectedName').val() + ' - ' + $('#inputBuscarSucursal_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombreSucursal').val() == '')
			return 'Debe ingresar el nombre de sucursal del banco propio';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscarSucursal_selectedValue').val() != '' ? 'editar' : 'agregar');
		funciones.guardar(funciones.controllerUrl(aux), armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idBanco: $('#inputBanco_selectedValue').val(),
			idSucursal: $('#inputBuscarSucursal_selectedValue').val(),
			nombreSucursal: $('#inputNombreSucursal').val(),
			calle: $('#inputCalle').val(),
			numero: $('#inputNumero').val(),
			piso: $('#inputPiso').val(),
			dpto: $('#inputDpto').val(),
			codPostal: $('#inputCP').val(),
			idPais: $('#inputPais_selectedValue').val(),
			idProvincia: $('#inputProvincia_selectedValue').val(),
			idLocalidad: $('#inputLocalidad_selectedValue').val(),
			fechaInicioCuenta: $('#inputFechaInicioCuenta').val(),
			telefono: $('#inputTelefono').val(),
			observaciones: $('#inputObservaciones').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el banco propio "' + $('#inputBuscar_selectedName').val() + ' - ' + $('#inputBuscarSucursal_selectedName').val() + '"?';
		funciones.borrar(msg, funciones.controllerUrl('borrar'), armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
			idBanco: $('#inputBuscar_selectedValue').val(),
			idSucursal: $('#inputBuscarSucursal_selectedValue').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscar_selectedName').val() + ' ' + $('#inputBuscarSucursal_selectedName').val());
				break;
			case 'editar':
				$('#inputNombreSucursal').focus();
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
			$cells[1][0]->content = '<label>Nombre sucursal:</label>';
			$cells[1][1]->content = '<input id="inputNombreSucursal" class="textbox obligatorio inputForm w230" type="text" rel="nombreSucursal" />';

			$cells[2][0]->content = '<label>Calle:</label>';
			$cells[2][1]->content = '<input id="inputCalle" class="textbox inputForm w230" rel="direccionCalle" />';

			$cells[3][0]->content = '<label>Número:</label>';
			$cells[3][1]->content = '<input id="inputNumero" class="textbox inputForm w65" rel="direccionNumero" />
										 <label>Piso:</label>
										 <input id="inputPiso" class="textbox inputFormSuc inputForm w25" maxlength="3" rel="direccionPiso" />
										 <label>Dpto:</label>
										 <input id="inputDpto" class="textbox inputFormSuc inputForm w25" maxlength="3" rel="direccionDepartamento" />';

			$cells[4][0]->content = '<label>País:</label>';
			$cells[4][1]->content = '<input id="inputPais" class="textbox autoSuggestBox inputForm w230" name="Pais" rel="direccionPais" />';

			$cells[5][0]->content = '<label>Provincia:</label>';
			$cells[5][1]->content = '<input id="inputProvincia" class="textbox autoSuggestBox inputForm w230" name="Provincia" linkedTo="inputPais,Pais" rel="direccionProvincia" />';

			$cells[6][0]->content = '<label>Localidad:</label>';
			$cells[6][1]->content = '<input id="inputLocalidad" class="textbox autoSuggestBox inputForm w135" name="Localidad" linkedTo="inputPais,Pais;inputProvincia,Provincia" rel="direccionLocalidad" />
										 <label>CP:</label>
										 <input id="inputCP" class="textbox inputFormSuc inputForm w45" maxlength="4" rel="direccionCodigoPostal" />';

			// Falta "imputacionContable" que aparentemente no se usa

			$cells[7][0]->content = '<label>Fecha de inicio:</label>';
			$cells[7][1]->content = '<input id="inputFechaInicioCuenta" class="textbox inputForm w210" validate="Fecha" rel="fechaInicioCuenta" />';

			$cells[8][0]->content = '<label>Teléfono:</label>';
			$cells[8][1]->content = '<input id="inputTelefono" class="textbox inputForm w230" rel="telefono" />';

			$cells[9][0]->content = '<label>Observaciones:</label>';
			$cells[9][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones"></textarea>';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Banco:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Banco' alt='' />
		</div>
		<div>
			<label for='inputBuscarSucursal' class='filtroBuscar'>Sucursal:</label>
			<input id='inputBuscarSucursal' class='textbox autoSuggestBox filtroBuscar w200' name='BancoPropio' linkedTo="inputBuscar,Banco" />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/bancos_propios/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/bancos_propios/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/bancos_propios/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
