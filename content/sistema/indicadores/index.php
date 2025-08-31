<?php

$roles = Factory::getInstance()->getListObject('Rol', 'anulado = \'N\'');
$htmlRoles = generoHtmlRoles($roles);

function generoHtmlRoles($roles){
	$echo = '<ul id="roles"">';
	foreach ($roles as $rol) {
		$echo .= '<li class="rol_' . $rol->tipo . ' pTop4"><input id="' . $rol->id . '" type="checkbox" /><label for="' . $rol->id . '" class="s16">' . $rol->nombre . '</label></li>';
	}
	$echo .= '</ul>';
	return $echo;
}

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Indicadores';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		limpiarChk();
	}

	function enableChk(modo){
		if (modo)
			$('#roles :checkbox').enable();
		else
			$('#roles :checkbox').disable();
	}

	function limpiarChk(){
		$('#roles :checkbox').uncheck();
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/sistema/indicadores/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El indicador "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
				$(json.roles).each(function(){
					$('#' + this.id).check();
				});
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe elegir un nombre para el indicador';
		if ($('#inputQuery').val() == '' && $('#inputView').val() == '')
			return 'Debe elegir una view o ingresar una query';
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/sistema/indicadores/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}
	

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			descripcion: $('#inputDescripcion').val(),
			view: $('#inputView').val(),
			valor1: $('#inputValor1').val(),
			valor2: $('#inputValor2').val(),
			valor3: $('#inputValor3').val(),
			fields: $('#inputFields').val(),
			where: $('#inputWhere').val(),
			query: $('#inputQuery').val(),
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
		var msg = '¿Está seguro que desea borrar el indicador "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/sistema/indicadores/borrar.php';
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
				enableChk(false);
				break;
			case 'editar':
				$('#inputNombre').focus();
				enableChk(true);
				break;
			case 'agregar':
				$('#inputNombre').focus();
				enableChk(true);
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divIndicador' class='pantalla w55p fLeft'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 9, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w350" rel="nombre" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Descripción:</label>';
			$cells[1][1]->content = '<textarea id="inputDescripcion" class="textbox inputForm w350" rel="descripcion"/></textarea>';
			$cells[2][0]->content = '<label>View:</label>';
			$cells[2][1]->content = '<input id="inputView" class="textbox inputForm w350" rel="view" placeholder="Nombre de la view que trae los valores del indicador" />';
			$cells[3][0]->content = '<label>Valor verde:</label>';
			$cells[3][1]->content = '<input id="inputValor1" class="textbox inputForm w350" rel="valor1" placeholder="Valor desde o hasta el cual el indicador es verde" />';
			$cells[4][0]->content = '<label>Valor amarillo:</label>';
			$cells[4][1]->content = '<input id="inputValor2" class="textbox inputForm w350" rel="valor2" placeholder="Valor desde o hasta el cual el indicador es amarillo" />';
			$cells[5][0]->content = '<label>Valor rojo:</label>';
			$cells[5][1]->content = '<input id="inputValor3" class="textbox inputForm w350" rel="valor3" placeholder="Valor desde o hasta el cual el indicador es rojo" />';
			$cells[6][0]->content = '<label>Campos:</label>';
			$cells[6][1]->content = '<textarea id="inputFields" class="textbox inputForm w350" rel="fields" placeholder="Lista de campos de la view que desean mostrarse en el indicador (separados por coma)"/></textarea>';
			$cells[7][0]->content = '<label>Where:</label>';
			$cells[7][1]->content = '<textarea id="inputWhere" class="textbox inputForm w350 h60" rel="where" placeholder="Condición para mandarle a la view a partir del usuario logueado (Ej: cod_cli=///contacto->cliente->id///"/></textarea>';
			$cells[8][0]->content = '<label>Query:</label>';
			$cells[8][1]->content = '<textarea id="inputQuery" class="textbox inputForm w350 h100" rel="query" placeholder="Consulta entera a ejecutar. Se debe ingresar en lugar de view y where. Se puede además elegir los campos. Ej: SELECT SUM(cantidad) FROM pedidos_d_v WHERE fecha_pedido > CONVERT(DATETIME, \'2013-01-01\', 102)"/></textarea>';

			$tabla->create();
		?>
	</div>
	<div class='vLine1 pantalla'></div>
	<div id='divRoles' class='customScroll pantalla w35p fRight'>
		<h3 class='s18'>Roles</h3>
		<?php echo $htmlRoles; ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Indicador:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Indicador' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'sistema/indicadores/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'sistema/indicadores/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'sistema/indicadores/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
