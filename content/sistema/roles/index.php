<?php

$funcs = '';
$i = 1;
goDeep($i, $funcs, true);

function goDeep($i, &$echo, $first = false){
	$echo .= '<ul' . ($first ? ' id="tree"' : '') . '>';
	while (Funcionalidades::get($i)){
		$aux1 = explode('/', Funcionalidades::get($i));
		$nombreFun = implode(' ', explode('_', strtoupper($aux1[count($aux1) - 2])));
		$echo .= '<li><input type="checkbox" id="' . $i . '" /><label for="'. $i .'">'. trim($nombreFun) . '</label>';
		$j = $i * 10 + 1;
		$k = $i * 100 + 1;
		if (Funcionalidades::get($j))
			goDeep($j, $echo);
		elseif (Funcionalidades::get($k)) //Excepción para cuando hay más de 9 programas, pongo 01, 02... 09, 10, 11...
			goDeep($k, $echo);
		$echo .= '</li>';
		$i++;
	}
	$echo .= '</ul>';
}

?>

<style>
	#divCampos {
		float: left;
	}
	#divFuncionalidades {
		float: right;
		width: 45%;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Roles';
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

		$('#tree').checktree();
		cambiarModo('inicio');
	});

	function enableChk(modo){
		if (modo)
			$('#tree :checkbox').enable();
		else
			$('#tree :checkbox').disable();
	}

	function limpiarChk(){
		$('#tree :checkbox').uncheck();
	}

	function limpiarScreen(){
		limpiarChk();
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/sistema/roles/buscar.php?idRol=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El rol "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
				$(json.funcs).each(function(){
					$('#' + this).check();
				});
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar un nombre para el rol';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/sistema/roles/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idRol: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			tipo: $('#radioGroupTipo').radioVal(),
			funcionalidades: armoArray()
		};
	}

	function armoArray(){
		var array = new Array(),
			funcs = $('#tree input:checked');
		funcs.each(function(){
			array[array.length] = this.id;
		});
		return array;
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el rol "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/sistema/roles/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idRol: $('#inputBuscar_selectedValue').val()};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				enableChk(false);
				break;
			case 'buscar':
				enableChk(false);
				break;
			case 'editar':
				enableChk(true);
				$('#inputNombre').focus();
				break;
			case 'agregar':
				enableChk(true);
				$('#inputNombre').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divCampos' class='customScroll pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w200" type="text" rel="nombre" value="" maxlength="50" />';
			$cells[0][1]->style->width = '220px';
			$cells[1][0]->content = '<label>Tipo:</label>';
			$cells[1][1]->content = '<div id="radioGroupTipo" class="customRadio noEditable" default="rdPersonal"><input id="rdPersonal" class="textbox" type="radio" name="radioGroupTipo" rel="tipo" value="P" /><label for="rdPersonal">Personal</label>' .
								  	'<input id="rdContacto" class="textbox" type="radio" name="radioGroupTipo" rel="tipo" value="C" /><label for="rdContacto">Contacto</label></div>';

			$tabla->create();
		?>
	</div>
	<div class='vLine1 pantalla'></div>
	<div id='divFuncionalidades' class='customScroll pantalla'>
		<h3 class='s18'>Funcionalidades</h3>
		<?php echo $funcs; ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='filtroBuscar'>Rol:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Rol' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'sistema/roles/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'sistema/roles/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'sistema/roles/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
