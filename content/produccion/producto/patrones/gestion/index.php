<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Gestión de patrones';
		cambiarModo('inicio');
	});

	function limpiarScreen() {
		$('#divGestionPatrones').html('');

		var table = $('<table>').addClass('w100p').append($('<thead>').addClass('tableHeader').append($('<tr>')
			.append($('<th>').addClass('w30p').append('Artículo'))
			.append($('<th>').addClass('w20p').append('Color'))
			.append($('<th>').addClass('w10p').append('Versión'))
			.append($('<th>').addClass('w10p').append('Tipo'))
			.append($('<th>').addClass('w15p').append('Confirmado'))
			.append($('<th>').addClass('w15p').append('Vigente'))
		));

		$('#divGestionPatrones').append(table);
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', {
				idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
				idColor: $('#inputBuscarColor_selectedValue').val()
			}),
			msgError = 'No hay patrones con ese filtro',
			cbSuccess = function(json) {
				llenarPantalla(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function divArticulo(o) {
		return $('<div>').html('[' + o.idArticulo + '] - ' + o.denomArticulo);
	}

	function divColor(o) {
		return $('<div>').html('[' + o.idColor + '] - ' + o.denomColor);
	}

	function divVersion(o) {
		return $('<div>').html(o.idVersion);
	}

	function divTipo(o) {
		var idCombinado = o.idArticulo + '_' + o.idColor + '_' + o.idVersion,
			select = $('<select id="inputTipo_' + idCombinado + '" class="textbox w100"><option value="P">P</option><option value="D">D</option></select>');

		select.val(o.tipoPatron);

		select.change(function() {
			var url = funciones.controllerUrl('cambiar_version'),
				that = $(this);

			o.tipoPatron = that.val();
			funciones.guardar(url, o, function() {
				that.val(o.tipoPatron);
			});
		});

		return $('<div>').html(select);
	}

	function divConfirmado(o) {
		return $('<div>').addClass('bold').addClass('aCenter').append($('<input type="checkbox" id="inputConfirmado_' + o.idArticulo + '_' + o.idColor + '_' + o.idVersion + '" class="textbox koiCheckbox"' + (o.confirmado == 'S' ? ' checked ' : ' ') + '>').click($.proxy(confirmar, o)));
	}

	function divVersionActual(o) {
		return $('<div>').addClass('bold').addClass('aCenter').append($('<input type="checkbox" id="inputVersionActual_' + o.idArticulo + '_' + o.idColor + '_' + o.idVersion + '" class="textbox koiCheckbox"' + (o.versionActual == 'S' ? ' checked ' : ' ') + '>').click($.proxy(hacerActual, o)));
	}

	function confirmar() {
		var url = funciones.controllerUrl('confirmar'),
			id = this.idArticulo + '_' + this.idColor + '_' + this.idVersion,
			obj = this,
			checkbox = $('#inputConfirmado_' + id);

		obj.confirmado = (checkbox.isChecked() ? 'S' : 'N');

		funciones.guardar(url, obj, function() {
			buscar();
		});

		if (checkbox.isChecked()) {
			checkbox.uncheck();
		} else {
			checkbox.check();
		}
	}

	function hacerActual() {
		var url = funciones.controllerUrl('hacer_actual'),
			id = this.idArticulo + '_' + this.idColor + '_' + this.idVersion,
			obj = this,
			checkbox = $('#inputVersionActual_' + id);

		obj.versionActual = (checkbox.isChecked() ? 'S' : 'N');

		funciones.guardar(url, obj, function() {
			buscar();
		});

		if (checkbox.isChecked()) {
			checkbox.uncheck();
		} else {
			checkbox.check();
		}
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.idArticulo + '_' + o.idColor + '_' + o.idVersion).append(
			$('<td>').addClass('w30p').append(divArticulo(o)),
			$('<td>').addClass('w20p').append(divColor(o)),
			$('<td>').addClass('w10p aCenter').append(divVersion(o)),
			$('<td>').addClass('w10p aCenter').append(divTipo(o)),
			$('<td>').addClass('w15p').append(divConfirmado(o)),
			$('<td>').addClass('w15p').append(divVersionActual(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divGestionPatrones');
        var table = $('<table>').attr('id', 'tablaRecibos').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function cambiarModo(modo) {
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divGestionPatrones').html('');
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
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
	<div id='divGestionPatrones' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarArticulo' class='textbox autoSuggestBox filtroBuscar w230' name='Articulo' />
		</div>
		<div>
			<label for='inputBuscarColor' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColor' class='textbox autoSuggestBox filtroBuscar w230' name='ColorPorArticulo' linkedTo='inputBuscarArticulo,Articulo' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
