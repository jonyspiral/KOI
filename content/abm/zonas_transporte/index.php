<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Zonas de transporte';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')//agregado los dos campos de busqueda
			return $('#inputBuscar').val('');
		
		var url = '/content/abm/zonas_transporte/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),	
			msgError = 'La zona de transporte "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe elegir un nombre para la zona de transporte';				
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/zonas_transporte/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}
	

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			descripcion: $('#inputDescripcion').val(),
			nombre: $('#inputNombre').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la zona de transporte "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/zonas_transporte/borrar.php';
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
	<div id='divSucursales1' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);
  
			//imprime el cuadro con campos	
			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230"  alt="" rel="nombre" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Descripcion:</label>';
			$cells[1][1]->content = '<input id="inputDescripcion" class="textbox inputForm inputForm w230" rel="descripcion" />';
	
			$tabla->create();//impresion
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label class='filtroBuscar'>Zona de transporte:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='ZonaTransporte' alt='' />
		</div>
		
	</div><!-- fin campos busqueda -->
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/zonas_transporte/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/zonas_transporte/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/zonas_transporte/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
