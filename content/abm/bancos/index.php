<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Bancos';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = funciones.controllerUrl('buscar', {id: $('#inputBuscar_selectedValue').val()}),
			msgError = 'El banco "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del banco';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		funciones.guardar(funciones.controllerUrl(aux), armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			id:					$('#inputBuscar_selectedValue').val(),
			nombre:				$('#inputNombre').val(),
			codigoBanco:		$('#inputCodigoBanco').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el banco "' + $('#inputBuscar_selectedName').val() + '"?';
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
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w200" type="text" rel="nombre" />';
			$cells[0][1]->style->width = '220px';
			$cells[1][0]->content = '<label>Código BCRA:</label>';
			$cells[1][1]->content = '<input id="inputCodigoBanco" class="textbox inputForm w200" type="text" rel="codigoBanco" />';

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
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/bancos/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/bancos/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/bancos/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
