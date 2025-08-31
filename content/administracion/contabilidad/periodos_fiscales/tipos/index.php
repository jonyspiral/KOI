<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Tipos de períodos fiscales';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = funciones.controllerUrl('buscar', {
			id: $('#inputBuscar_selectedValue').val()
		});
		var msgError = 'El tipo de período fiscal "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos, #tablaDatos2').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#cantidadDias').val() == '')
			return 'Debe ingresar un nombre';		
		return false;
	}

	function guardar(){
		var url = funciones.controllerUrl(($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar'));
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			nombre: $('#tipoPeriodoFiscalNombre').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el tipo de período fiscal "' + $('#inputBuscar_selectedName').val() + '"?',
			url = funciones.controllerUrl('borrar');
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
				break;
			case 'buscar':
				break;
			case 'editar':
				break;
			case 'agregar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divIzquierda' class='pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';			
			$cells[0][1]->content = '<input id="tipoPeriodoFiscalNombre" class="textbox obligatorio inputForm w230" rel="nombre" />';
			$cells[0][1]->style->width = '250px';
			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Tipos de períodos fiscales:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='TipoPeriodoFiscal' alt='' />
		</div>		
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/formas_pago/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/formas_pago/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/formas_pago/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
