<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Cambiar contraseña';
		cambiarModo('inicio');
	});

	function hayErrorGuardar(){
		if ($('#oldPassword').val() == '')
			return 'Debe ingresar la contraseña actual para poder cambiarla';
		if ($('#newPassword').val() == '')
			return 'Ingrese la nueva contraseña';
		return false;
	}

	function guardar(){
		var url = '/content/sistema/usuarios/cambiar_contrasena/editar.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			oldPassword: $('#oldPassword').val(),
			newPassword: $('#newPassword').val()
		};
	}

	function cambiarModo(modo){
		switch (modo){
			case 'inicio':
				funciones.cambiarTitulo();
				$('#btnGuardar').show();
				$('#oldPassword').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divCampos'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Contraseña actual:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="oldPassword" class="textbox obligatorio inputForm w200" type="password" value="" />';
			$cells[0][1]->style->width = '220px';
			$cells[1][0]->content = '<label>Contraseña nueva:</label>';
			$cells[1][1]->content = '<input id="newPassword" class="textbox obligatorio inputForm w200" type="password" value="" />';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();', 'permiso' => 'sistema/usuarios/cambiar_contrasena/')); ?>
	</div>
</div>
