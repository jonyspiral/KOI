<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Ejercicios contables';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = funciones.controllerUrl('buscar', {
				idEjercicioContable: $('#inputBuscar_selectedValue').val()
			}),
			msgError = 'El ejercicio contable "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del ejercicio contable';
		if ($('#inputFechaDesde').val() == '')
			return 'Debe ingresar la fecha de inicio del ejercicio contable';
		if ($('#inputFechaHasta').val() == '')
			return 'Debe ingresar la fecha de fin del ejercicio contable';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		funciones.guardar(funciones.controllerUrl(aux), armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idEjercicioContable: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el ejercicio contable "' + $('#inputBuscar_selectedName').val() + '"?';
		funciones.borrar(msg, funciones.controllerUrl('borrar'), armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
			idEjercicioContable: $('#inputBuscar_selectedValue').val()
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
	<div id='divCampos' class='pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" type="text" rel="nombre" />';
			$cells[0][1]->style->width = '230px';

			$cells[1][0]->content = '<label>Fecha de inicio:</label>';
			$cells[1][1]->content = '<input id="inputFechaDesde" class="textbox inputForm w210" validate="Fecha" rel="fechaDesde" />';

			$cells[2][0]->content = '<label>Fecha de fin:</label>';
			$cells[2][1]->content = '<input id="inputFechaHasta" class="textbox inputForm w210" validate="Fecha" rel="fechaHasta" />';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Ejercicio contable:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='EjercicioContable' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/ejercicios_contables/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/ejercicios_contables/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/ejercicios_contables/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
