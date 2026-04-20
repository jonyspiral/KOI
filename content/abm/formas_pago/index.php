<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Formas de pago';	
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/formas_pago/buscar.php?dias=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'La forma de pago "' + $('#inputBuscar_selectedName').val() + '" no existe.',
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
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/formas_pago/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			cantDias: $('#cantidadDias').val(),
			nombre: $('#formasPagoNombre').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la forma de pago "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/formas_pago/borrar.php';
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
				$('#cantidadDias').disable();
				$('#formasPagoNombre').focus();
				break;
			case 'agregar':
				$('#cantidadDias').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divIzquierda' class='pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);
  
			//imprime el cuadro con campos
			$cells[0][0]->content = '<label>Cantidad de días:</label>';
			$cells[0][0]->style->width = '150px';			
			$cells[0][1]->content = '<input id="cantidadDias" class="textbox obligatorio inputForm w230" name="CantidadDias" alt="" rel="id" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Nombre:</label>';
			$cells[1][1]->content = '<input id="formasPagoNombre" class="textbox inputForm w230" name="Sucursal"   alt="" rel="nombre" />';
			$tabla->create();//impresion
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label for='inputBuscar' class='filtroBuscar'>Formas de pago:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='FormaDePago' alt='' />
		</div>		
	</div><!-- fin campos busqueda -->
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
