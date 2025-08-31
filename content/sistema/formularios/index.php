<?php



?>

<style>
	#divSucursales {
		height: 440px;
	}
	#divContactos {
		height: 440px;
	}
	
	
.drag {
	position: absolute;
	border: 1px solid #89B;
	background: #BCE;
	height: 58px;
	width: 58px;
	cursor: move;
	top: 120px;
	}
.selected {
	background-color: #ECB;
	border-color: #B98;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Fajas horarias';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/fajas_horarias/buscar.php?id=' + $('#inputBuscar_selectedValue').val(), 
			msgError = 'La faja horaria "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar un nombre para la faja horaria';
		if ($('#inputEntrada').val() == '')
			return 'Debe ingresar un horario de entrada';
		if ($('#inputSalida').val() == '')
			return 'Debe ingresar un horario de salida';				
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/fajas_horarias/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),		
			horarioEntrada: $('#inputEntrada').val(),
			horarioSalida: $('#inputSalida').val(),
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la faja horaria "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/fajas_horarias/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
				id: $('#inputBuscar_selectedValue').val(),	
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

		jQuery(function($){
		$('.drag')
			.click(function(){
				$( this ).toggleClass("selected");
			})
			.drag("init",function(){
				if ( $( this ).is('.selected') )
					return $('.selected');
			})
			.drag(function( ev, dd ){
				$( this ).css({
					top: dd.offsetY,
					left: dd.offsetX
				});
			});
	});

	
</script>

<div id='programaTitulo'></div>
	<div id='programaContenido'>


	

<h1>Multi Drag Demo</h1>
<p>Click to select multiple boxes, and drag them around the screen.</p>
<div class="drag" style="left:400px;"></div>
<div class="drag" style="left:500px;"></div>
<div class="drag" style="left:600px;"></div>


	
	
	
	
	</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label class='filtroBuscar'>Faja horaria:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='FajaHoraria'  alt='' />
		</div>
	</div><!-- fin campos busqueda -->
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/fajas_horarias/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/fajas_horarias/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/fajas_horarias/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>