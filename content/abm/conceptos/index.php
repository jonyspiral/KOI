<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Conceptos';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/conceptos/buscar.php?idConcepto=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El concepto "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del concepto';

		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/conceptos/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idConcepto: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),		
			descripcion: $('#inputDescripcion').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el concepto "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/conceptos/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
				idConcepto: $('#inputBuscar_selectedValue').val()
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
<div id='programaContenido'>
	<div id='divImpuestos' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm inputForm w230" rel="nombre" />';
			$cells[0][1]->style->width = '250px';

			$cells[1][0]->content = '<label>Descripción:</label>';
			$cells[1][1]->content = '<textarea id="inputDescripcion" class="textbox inputForm inputForm w230" rel="descripcion" ></textarea>';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label for='inputBuscar' class='filtroBuscar'>Concepto:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Concepto' />
		</div>
	</div><!-- fin campos busqueda -->
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/conceptos/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/conceptos/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/conceptos/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
