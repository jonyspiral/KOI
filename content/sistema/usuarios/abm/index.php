<?php

$roles = Factory::getInstance()->getListObject('Rol', 'anulado = \'N\'');
$htmlRoles = generoHtmlRoles($roles);

function generoHtmlRoles($roles){
	$echo = '<ul id="roles"">';
	foreach ($roles as $rol) {
		$echo .= '<li class="rol_' . $rol->tipo . '"><input id="' . $rol->id . '" type="checkbox" /><label for="' . $rol->id . '" class="s16">' . $rol->nombre . '</label></li>';
	}
	$echo .= '</ul>';
	return $echo;
}

?>

<style>
	#divCampos {
		float: left;
	}
	#divRoles {
		float: right;
		width: 45%;
	}
	#roles li {
		list-style: none;
		padding-top: 4px;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Usuarios';
		//Bindeo eventos click
		$('#rdPersonal').click(function(){
			limpiarChk();
			$('.trContacto').hide();
			$('.trPersonal').show();
			$('.rol_C').hide();
			$('.rol_P').show();
		});
		$('#rdContacto').click(function(){
			limpiarChk();
			$('.trPersonal').hide();
			$('.trContacto').show();
			$('.rol_P').hide();
			$('.rol_C').show();
		});
		cambiarModo('inicio');
	});

	function enableChk(modo){
		if (modo)
			$('#roles :checkbox').enable();
		else
			$('#roles :checkbox').disable();
	}

	function limpiarChk(){
		$('#roles :checkbox').uncheck();
	}

	function limpiarScreen(){
		limpiarChk();
	}

	function limpiarBuscar(){
		$('#inputBuscar, #inputBuscar_selectedValue, #inputBuscar_selectedName').val('');
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/sistema/usuarios/abm/buscar.php?idUsuario=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El usuario "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
				$('#inputPassword').val('******');
			 	$(json.roles).each(function(){
				 	$('#' + this.id).check();
				});
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputUsername').val() == '')
			return 'Debe ingresar un nombre de usuario';
		if ($('#inputPassword').val() == '')
			return 'Debe ingresar una contraseña para el usuario';
		if (($('#rdPersonal').isChecked()) && ($('#inputPersonal_selectedValue').val() == ''))
			return 'Debe elegir un personal para vincular el usuario';
		if (($('#rdContacto').isChecked()) && ($('#inputContacto_selectedValue').val() == ''))
			return 'Debe elegir un contacto para vincular el usuario';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/sistema/usuarios/abm/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idUsuario: $('#inputUsername').val(),
			password: $('#inputPassword').val(),
			tipo: $('#radioGroupTipo').radioVal(),
			idPersonal: $('#inputPersonal_selectedValue').val(),
			idContacto: $('#inputContacto_selectedValue').val(),
			newPassword: $('#newPassword').val(),
			roles: armoArray()
		};
	}

	function armoArray(){
		var array = [],
			funcs = $('#roles input:checked');
		funcs.each(function(){
			array[array.length] = this.id;
		});
		return array;
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el usuario "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/sistema/usuarios/abm/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idUsuario: $('#inputBuscar_selectedValue').val()};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.altaPassword').hide();
				$('.trPassword').hide();
				$('.trCambioPassword').hide();
				enableChk(false);
				break;
			case 'buscar':
				$('.altaPassword').hide();
				$('.trPassword').hide();
				$('.trCambioPassword').hide();
				enableChk(false);
				break;
			case 'editar':
				$('.trPassword').hide();
				$('#inputUsername').disable();
				$('#inputPersonal').disable();
				$('#inputContacto').disable();
				$('.customRadio').disableRadioGroup();
				$('.trCambioPassword').show();
				enableChk(true);
				$('#newPassword').focus();
				break;
			case 'agregar':
				$('.trPassword').show();
				$('.trCambioPassword').hide();
				enableChk(true);
				$('#inputUsername').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divCampos' class='pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 8, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Usuario:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputUsername" class="textbox obligatorio inputForm w200" type="text" rel="id" value="" />';
			$cells[0][1]->style->width = '220px';
			$cells[1][0]->content = '<label>Contraseña:</label>';
			$cells[1][1]->content = '<input id="inputPassword" class="textbox obligatorio inputForm w200" type="password" value="" />';
			$cells[2][0]->content = '<label>Tipo:</label>';
			$cells[2][1]->content = '<div id="radioGroupTipo" class="customRadio" default="rdPersonal"><input id="rdPersonal" class="textbox" type="radio" name="radioGroupTipo" rel="tipoUsuario" value="P" /><label for="rdPersonal">Personal</label>' .
					'<input id="rdContacto" class="textbox" type="radio" name="radioGroupTipo" rel="tipoUsuario" value="C" /><label for="rdContacto">Contacto</label></div>';
			$cells[3][0]->content = '<label>Personal:</label>';
			$cells[3][1]->content = '<input id="inputPersonal" class="textbox autoSuggestBox obligatorio inputForm w200" rel="personal" name="Personal" alt="" />';
			$cells[4][0]->content = '<label>Contacto:</label>';
			$cells[4][1]->content = '<input id="inputContacto" class="textbox autoSuggestBox obligatorio inputForm w200" rel="contacto" name="Contacto" alt="" />';
			$cells[6][0]->content = '<label>Cambio de contraseña</label>';
			$cells[7][0]->content = '<label>Contraseña nueva:</label>';
			$cells[7][1]->content = '<input id="newPassword" class="textbox obligatorio inputForm w200" type="password" value="" />';
			$rows[1]->class= 'trPassword';
			$rows[3]->class= 'trPersonal';
			$rows[4]->class= 'trContacto';
			$rows[5]->style->width = '20px';
			$rows[6]->class= 'trCambioPassword';
			$rows[7]->class= 'trCambioPassword';

			$tabla->create();
		?>
	</div>
	<div class='vLine1 pantalla'></div>
	<div id='divRoles' class='customScroll pantalla'>
		<h3 class='s18'>Roles</h3>
		<?php echo $htmlRoles; ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Usuario:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Usuario' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'sistema/usuarios/abm/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'sistema/usuarios/abm/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'sistema/usuarios/abm/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
