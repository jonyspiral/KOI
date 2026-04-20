<?php

$usuarios = Factory::getInstance()->getListObject('Usuario', 'anulado = ' . Datos::objectToDB('N') . ' AND tipo = ' . Datos::objectToDB('P') . ' ORDER BY cod_usuario ASC');
$htmlUsuarios = generoHtmlUsuarios($usuarios);

function generoHtmlUsuarios($usuarios){
	$echo = '<ul id="usuarios"">';
	foreach ($usuarios as $usuario) {
		$echo .= '<li><input id="user_' . $usuario->id . '" data-usuario="' . $usuario->id . '" type="checkbox" /><label for="user_' . $usuario->id . '" class="s16">' . $usuario->id . '</label></li>';
	}
	$echo .= '</ul>';
	return $echo;
}

?>

<style>
	#divCampos {
		float: left;
	}
	#divUsuarios {
		float: right;
		width: 45%;
	}
	#usuarios li {
		list-style: none;
		padding-top: 4px;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Áreas de empresa';
		$('#inputHabilitadaTicket').change(function() {
			hideShowUsuarios();
		});
		cambiarModo('inicio');
	});

	function enableChk(modo){
		modo ? $('#usuarios :checkbox').enable() : $('#usuarios :checkbox').disable();
	}

	function limpiarChk(){
		$('#usuarios :checkbox').uncheck();
	}

	function hideShowUsuarios() {
		if ($('#inputHabilitadaTicket').isChecked()) {
			$('#divUsuarios').show();
		} else {
			limpiarChk();
			$('#divUsuarios').hide();
		}
	}

	function limpiarScreen(){
		limpiarChk();
	}

	function limpiarBuscar(){
		$('#inputBuscar, #inputBuscar_selectedValue, #inputBuscar_selectedName').val('');
	}

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = funciones.controllerUrl('buscar', {id: $('#inputBuscar_selectedValue').val()}),
			msgError = 'El área empresa "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			 	$(json.usuarios).each(function(){
				 	$('#user_' + this.id).check();
				});
				hideShowUsuarios();
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del área empresa';
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
			habilitadaTicket:	$('#inputHabilitadaTicket').isChecked() ? 'S' : 'N',
			usuarios:			armoArray()
		};
	}

	function armoArray(){
		var array = [],
			funcs = $('#usuarios input:checked');
		funcs.each(function(){
			array[array.length] = $(this).data('usuario');
		});
		return array;
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el área empresa "' + $('#inputBuscar_selectedName').val() + '"?';
		funciones.borrar(msg, funciones.controllerUrl('borrar'), armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {id: $('#inputBuscar_selectedValue').val()};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				enableChk(false);
				break;
			case 'buscar':
				enableChk(false);
				hideShowUsuarios();
				break;
			case 'editar':
				enableChk(true);
				hideShowUsuarios();
				$('#inputNombre').focus();
				break;
			case 'agregar':
				enableChk(true);
				hideShowUsuarios();
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
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w200" type="text" rel="nombre" value="" />';
			$cells[0][1]->style->width = '220px';
			$cells[1][0]->content = '<label>Habilitada ticket:</label>';
			$cells[1][1]->content = '<input type="checkbox" id="inputHabilitadaTicket" class="textbox koiCheckbox inputForm" rel="habilitadaTicket" >';

			$tabla->create();
		?>
	</div>
	<div class='vLine1 pantalla'></div>
	<div id='divUsuarios' class='customScroll pantalla'>
		<h3 class='s18'>Responsables</h3>
		<?php echo $htmlUsuarios; ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Área empresa:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='AreaEmpresa' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/areas_empresa/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/areas_empresa/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/areas_empresa/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
