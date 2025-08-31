<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Provincias';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/regiones/provincias/buscar.php?idPais=' + $('#inputBuscarPais_selectedValue').val() + '&idProvincia=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'La provincia "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputId').val() == '')
			return 'Debe ingresar el código de la provincia';
		if ($('#inputPais_selectedValue').val() == '')
			return 'Debe elegir a qué país pertenece la provincia';
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre de la provincia';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/regiones/provincias/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idPais: $('#inputPais_selectedValue').val(),
			idProvincia: $('#inputId').val(),
			nombre: $('#inputNombre').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la provincia "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/regiones/provincias/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
			idPais: $('#inputBuscarPais_selectedValue').val(),
			idProvincia: $('#inputBuscar_selectedValue').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divProvincia').hide();
				break;
			case 'buscar':
				$('#divProvincia').show();
				break;
			case 'editar':
				$('#inputNombre').focus();
				break;
			case 'agregar':
				$('#divProvincia').show();
				$('#inputId').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divProvincia'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Código:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputId" class="textbox obligatorio inputForm w230 noEditable" rel="id" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>País:</label>';
			$cells[1][1]->content = '<input id="inputPais" class="textbox obligatorio autoSuggestBox inputForm w230 noEditable" name="Pais" rel="pais" />';
			$cells[2][0]->content = '<label>Nombre:</label>';
			$cells[2][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" rel="nombre" />';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>País:</label>
			<input id='inputBuscarPais' class='textbox autoSuggestBox filtroBuscar w200' name='Pais' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Provincia:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Provincia' linkedTo="inputBuscarPais,Pais" alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/regiones/provincias/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/regiones/provincias/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/regiones/provincias/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
