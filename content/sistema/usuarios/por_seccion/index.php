<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Usuarios por sección';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divUsuariosPorSeccion').html('');
	}

	function buscar() {
		if($('#inputBuscarSeccion_selectedValue').val() == '') {
			$.error('Debe seleccionar una sección');
		} else {
			var url = funciones.controllerUrl('buscar', {idSeccionProduccion: $('#inputBuscarSeccion_selectedValue').val()}),
				msgError = 'Ocurrió un error al intentar buscar',
				cbSuccess = function(json){
					limpiarScreen();
					llenarPantalla(json);
				};
			funciones.buscar(url, cbSuccess, msgError);
		}
	}

	function llenarPantalla(json) {
		var div = $('#divUsuariosPorSeccion');
		var table = $('<table>').attr('id', 'tablaUsuarios').attr('class', 'registrosAlternados w100p').append(
			$('<thead>').addClass('tableHeader').append(
				$('<tr>').append(
					$('<th>').addClass('w30p').text('Usuario'),
					$('<th>').addClass('w60p').text('Nombre y apellido'),
					$('<th>').addClass('w10p').text('Acción')
				)
			)
		).append(
			$('<tbody>')
		);
		var body = table.find('tbody').eq(0);
		for (var i = 0; i < json.length; i++) {
			body.append(returnTr(json[i]));
		}
		div.append(table);
		$('#divUsuariosPorSeccion').fixedHeader({target: 'table'});
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.id + '_' + o.idSeccionProduccion).append(
			$('<td>').append(divUsuario(o)),
			$('<td>').append(divNombre(o)),
			$('<td>').append(divBotones(o))
		);
	}

	function divUsuario(o) {
		return $('<div>').text(o.id);
	}

	function divNombre(o) {
		return $('<div>').text(o.nombreApellido);
	}

	function divBotones(o) {
		var btn;
		var div = $('<div>').addClass('aCenter');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Eliminar')
			.click($.proxy(function() {borrarUsuario(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/borrar.gif'));
		div.append(btn);
		return div;
	}

	function removeTr(id) {
		$('#tr_' + id).remove();
	}

	function borrarUsuario(o) {
		var msg = '¿Está seguro que desea borrar el usuario "' + o.id + '" de los permisos de la sección?',
			url = funciones.controllerUrl('borrar'),
			objeto = {
				idUsuario: o.id,
				idSeccionProduccion: o.idSeccionProduccion
			};
		funciones.borrar(msg, url, objeto, function() {
			removeTr(o.id + '_' + o.idSeccionProduccion);
			$.success(this.responseMsg);
		});
	}

	function doAgregarUsuario() {
		var url = funciones.controllerUrl('agregar'),
			objeto = {
				idUsuario: $('#inputUsuario_selectedValue').val(),
				idSeccionProduccion: $('#inputBuscarSeccion_selectedValue').val()
			};
		if (objeto.idUsuario == '' || objeto.idSeccionProduccion == '') {
			$.error('No se ingresó correctamente el usuario. Si continúa con este problema, recargue la página');
		} else {
			funciones.guardar(url, objeto, function() {
				$.jPopUp.close();
				var json = this;
				$.success(json.responseMsg, function(){
					$('#tablaUsuarios > tbody').prepend(returnTr(json.data));
				});
			}, null, null, false);
		}
	}

	function popUpAgregar(){
		var div = '<div class="h50 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td><label for="inputUsuario">Usuario:</label></td><td><input id="inputUsuario" class="textbox autoSuggestBox w200" name="Usuario" /></td></tr>' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {doAgregarUsuario();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputUsuario').focus();
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#btnAgregar').hide();
				$('#btnCancelarBuscar').hide();
				break;
			case 'buscar':
				$('#btnAgregar').show();
				funciones.cambiarTitulo(tituloPrograma + ' - Sección "' + $('#inputBuscarSeccion').val() + '"');
				cambiarModo('editar');
				$('#btnAgregar').show();
				$('#btnCancelarBuscar').show();
				break;
			case 'editar':
				break;
			case 'agregar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divUsuariosPorSeccion' class='w100p customScroll h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarSeccion' class='filtroBuscar'>Sección:</label>
			<input id='inputBuscarSeccion' class='textbox autoSuggestBox obligatorio filtroBuscar w220' name='SeccionProduccion' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();', 'id' => 'btnBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'popUpAgregar();', 'id' => 'btnAgregar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
