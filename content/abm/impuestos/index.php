<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Impuestos';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/impuestos/buscar.php?idImpuesto=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El impuesto "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del impuesto';

		if ($('#inputImputacion_selectedValue').val() == '')
			return 'Debe ingresar la imputación del impuesto';

		if ($('#inputPorcentaje').val() == '')
			return 'Debe ingresar el porcentaje del impuesto';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/impuestos/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idImpuesto: $('#inputBuscar_selectedValue').val(),
			tipo: $('#inputTipo').val(),
			nombre: $('#inputNombre').val(),
			idImputacion: $('#inputImputacion_selectedValue').val(),
			porcentaje: $('#inputPorcentaje').val(),
			esGravado: ($('#inputEsGravado').isChecked() ? 'S' : 'N'),
			descripcion: $('#inputDescripcion').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el impuesto "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/impuestos/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
				idImpuesto: $('#inputBuscar_selectedValue').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
			case 'editar':
				$('#inputId').focus();
				$('#inputId').disable();
				break;
			case 'agregar':
				$('#inputId').focus();
				$('#inputId').enable();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divImpuestos' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 6, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" rel="nombre" />';
			$cells[0][1]->style->width = '250px';

			$cells[1][0]->content = '<label for="inputTipo" >Tipo:</label>';
			$cells[1][1]->content = '<select id="inputTipo" class="textbox obligatorio inputForm w245" rel="tipo" >
										<option value="1">IVA</option>
										<option value="2">IIBB</option>
										<option value="3">Ganancias</option>
									</select>';

			$cells[2][0]->content = '<label>Imputación:</label>';
			$cells[2][1]->content = '<input id="inputImputacion" class="textbox autoSuggestBox obligatorio inputForm w230" rel="imputacion" name="Imputacion" />';

			$cells[3][0]->content = '<label>Porcentaje:</label>';
			$cells[3][1]->content = '<input id="inputPorcentaje" class="textbox obligatorio inputForm w230" rel="porcentaje" validate="Porcentaje" />';

			$cells[4][0]->content = '<label>Es gravado:</label>';
			$cells[4][1]->content = '<input type="checkbox" id="inputEsGravado" class="textbox koiCheckbox inputForm" rel="esGravado" >';

			$cells[5][0]->content = '<label>Descripción:</label>';
			$cells[5][1]->content = '<textarea id="inputDescripcion" class="textbox inputForm w230" rel="descripcion" ></textarea>';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label for='inputBuscar' class='filtroBuscar'>Impuesto:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Impuesto' />
		</div>
	</div><!-- fin campos busqueda -->
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/impuestos/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/impuestos/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/impuestos/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
