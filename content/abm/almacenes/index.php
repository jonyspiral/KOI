<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Almacenes';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/almacenes/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),	
			msgError = 'El almacén "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe elegir un nombre para el almacén';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/almacenes/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idAgregar: $('#inputIdAgregar').val(),
			id: $('#inputBuscar_selectedValue').val(),
			nombreCorto: $('#inputNombreCorto').val(),
			nombre: $('#inputNombre').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el almacén "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/almacenes/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
				id: $('#inputBuscar_selectedValue').val()	
			};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.pantalla').hide();
				break;
			case 'buscar':
				$('.pantalla').show();
				break;
			case 'editar':
				$('#inputNombre').focus();
				break;
			case 'agregar':
				$('.pantalla').show();
				$('#inputNombre').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divAlmacen' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);
			
			$cells[0][0]->content = '<label>ID:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputIdAgregar" class="textbox obligatorio noEditable inputForm w230"  alt="" rel="id" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label for="inputNombre">Nombre:</label>';
			$cells[1][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230"  alt="" rel="nombre" />';
			$cells[2][0]->content = '<label>Nombre Corto:</label>';
			$cells[2][1]->content = '<input id="inputNombreCorto" class="textbox inputForm inputForm w230" rel="nombreCorto" />';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label for='inputBuscar' class='filtroBuscar'>Almacén:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Almacen' alt='' />
		</div>
		
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/zonas/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/zonas/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/zonas/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
