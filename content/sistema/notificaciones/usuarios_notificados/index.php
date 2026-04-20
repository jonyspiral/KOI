<?php

function esc($o) {
	return str_replace('@', '\\\\@', str_replace('.', '\\\\.', $o));
}

function htmlUsuarios(){
	$usuarios = Factory::getInstance()->getListObject('Usuario', 'anulado = \'N\'');
	$tabla = new HtmlTable(array('cantRows' => count($usuarios), 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
	$tabla->getRowCellArray($rows, $cells);
	$cells[0][0]->style->width = '50%';
	$cells[0][1]->style->width = '50%';
	for ($i = 0; $i < count($usuarios); $i++) {
		$usu = $usuarios[$i];
		$divRadio = '<div id="radioGroupUsuario_' . $usu->id . '" class="customRadio" default="rdUsuario_' . $usu->id . '_No">';
		$divRadio .= '<input id="rdUsuario_' . $usu->id . '_No" type="radio" name="radioGroupUsuario_' . $usu->id . '" value="0" idUsuario="' . $usu->id . '" /><label for="rdUsuario_' . $usu->id . '_No">No</label>';
		$divRadio .= '<input id="rdUsuario_' . $usu->id . '_Eliminable" type="radio" name="radioGroupUsuario_' . $usu->id . '" value="1" idUsuario="' . $usu->id . '" /><label for="rdUsuario_' . $usu->id . '_Eliminable">Info</label>';
		$divRadio .= '<input id="rdUsuario_' . $usu->id . '_NoEliminable" type="radio" name="radioGroupUsuario_' . $usu->id . '" value="2" idUsuario="' . $usu->id . '" /><label for="rdUsuario_' . $usu->id . '_NoEliminable">Acción</label>';
		$divRadio .= '</div>';
		$cells[$i][0]->content = '<label>' . $usu->id . '</label>';
		$cells[$i][1]->content = $divRadio;
	}
	$tabla->create();
}

function htmlRoles(){
	$roles = Factory::getInstance()->getListObject('Rol');
	$tabla = new HtmlTable(array('cantRows' => count($roles), 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
	$tabla->getRowCellArray($rows, $cells);
	$cells[0][0]->style->width = '50%';
	$cells[0][1]->style->width = '50%';
	for ($i = 0; $i < count($roles); $i++) {
		$rol = $roles[$i];
		$divRadio = '<div id="radioGroupRol_' . $rol->id . '" class="customRadio" default="rdRol_' . $rol->id . '_No">';
		$divRadio .= '<input id="rdRol_' . $rol->id . '_No" type="radio" name="radioGroupRol_' . $rol->id . '" value="0" idRol="' . $rol->id . '" /><label for="rdRol_' . $rol->id . '_No">No</label>';
		$divRadio .= '<input id="rdRol_' . $rol->id . '_Eliminable" type="radio" name="radioGroupRol_' . $rol->id . '" value="1" idRol="' . $rol->id . '" /><label for="rdRol_' . $rol->id . '_Eliminable">Info</label>';
		$divRadio .= '<input id="rdRol_' . $rol->id . '_NoEliminable" type="radio" name="radioGroupRol_' . $rol->id . '" value="2" idRol="' . $rol->id . '" /><label for="rdRol_' . $rol->id . '_NoEliminable">Acción</label>';
		$divRadio .= '</div>';
		$cells[$i][0]->content = '<label>' . $rol->nombre . '</label>';
		$cells[$i][1]->content = $divRadio;
	}
	$tabla->create();
}

?>

<style>

</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Usuarios notificados';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('.customRadio').radioDefault();
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/sistema/notificaciones/usuarios_notificados/buscar.php?idTipoNotificacion=' + $('#inputBuscar_selectedValue').val() ,
			msgError = 'La notificacion "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
			 	$(json.roles).each(function(){
				 	$('#rdRol_' + this.id + (this.eliminable == 'S' ? '_' : '_No') + 'Eliminable').radioClick();
				});
			 	$(json.usuarios).each(function(){
				 	$('#rdUsuario_' + this.id + (this.eliminable == 'S' ? '_' : '_No') + 'Eliminable').radioClick();
				});
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function guardar(){
		var url = '/content/sistema/notificaciones/usuarios_notificados/editar.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function hayErrorGuardar(){
		return false;
	}

	function armoObjetoGuardar(){
		return {
			idTipoNotificacion : $('#inputBuscar_selectedValue').val(),
			rxtn: armoArrayRoles(),
			uxtn: armoArrayUsuarios()
		};
	}

	function armoArrayRoles(){
		var array = [];
			roles = $('#divRoles').find(':checked').not('[value="0"]');
		roles.each(function(){
			array[array.length] = {idRol: $(this).attr('idRol'), eliminable: $(this).val()};
		});
		return array;
	}

	function armoArrayUsuarios(){
		var array = [];
			usuarios = $('#divUsuarios').find(':checked').not('[value="0"]');
		usuarios.each(function(){
			array[array.length] = {idUsuario: $(this).attr('idUsuario'), eliminable: $(this).val()};
		});
		return array;
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
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divUsuarios' class='customScroll pantalla w45p fLeft'>
		<h3 class='aCenter'>Usuarios</h3>
		<?php htmlUsuarios(); ?>
	</div>
	<div class='vLine1 pantalla'></div>
	<div id='divRoles' class='customScroll pantalla w45p fRight'>
		<h3 class='aCenter'>Roles</h3>
		<?php htmlRoles(); ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='filtroBuscar'>Tipo Notificación:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='TipoNotificacion' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'sistema/notificaciones/usuarios_notificados/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
