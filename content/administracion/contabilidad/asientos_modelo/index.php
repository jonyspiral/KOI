<?php

?>

<script type='text/javascript'>
	var configTablaDinamica;

	$(document).ready(function(){
		tituloPrograma = 'Asientos modelo';
		configTablaDinamica =
			{
				height: '220px',
				columnsConfig: [
					{
						id: 'imputacion',
						name: 'Imputación',
						width: '216px',
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w200" name="Imputacion" />',
						notEmpty: true
					},
					{
						id: 'observaciones',
						name: 'Observaciones',
						width: '475px',
						cellType: 'I',
						template: '<input class="textbox w420" type="text" />'
					}
				],
				defaultRows: 2,
				saveCallback: false,
				removeCallback: false,
				pluralName: 'asientos modelo',
				minRows: 2
			}
		;
		cambiarModo('inicio');
		buscar();
	});

	function limpiarScreen(){
		$('#divAsientosModelo').html('');
	}

	function buscar() {
		var url = funciones.controllerUrl('buscar');
		var msgError = 'Ocurrió un error al intentar buscar los asientos modelo',
			cbSuccess = function(json){
				funciones.limpiarScreen();
				llenarPantalla(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function llenarPantalla(json) {
		var div = $('#divAsientosModelo'),
			table;

		table = $('<table>').attr('id', 'tablaAsientos').attr('class', 'registrosAlternados w100p').append(
		$('<thead>').addClass('tableHeader').append(
			$('<tr>').append(
				$('<th>').addClass('w15p').text('Nº'),
				$('<th>').addClass('w75p').text('Asunto'),
				$('<th>').addClass('w10p')
			)
		)).append(
			$('<tbody>')
		);
		var body = table.find('tbody').eq(0);
		for (var i = 0; i < json.length; i++) {
			body.append(returnTr(json[i]));
		}
		div.append(table);
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.id).append(
			$('<td>').append($('<label>').text(o.id)).addClass('aCenter'),
			$('<td>').append($('<label>').text(o.nombre)),
			$('<td>').append(divBotones(o))
		);
	}

	function divBotones(o) {
		var btn;
		var div = $('<div>').addClass('aCenter');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
			.click($.proxy(function() {clickEditarAsiento(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/editar.gif'));
		div.append(btn);
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Eliminar')
			.click($.proxy(function() {borrarAsiento(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/borrar.gif'));
		div.append(btn);
		return div;
	}

	function removeTr(id) {
		$('#tr_' + id).remove();
	}

	function borrarAsiento(asientoModelo) {
		var msg = '¿Está seguro que desea borrar el asiento modelo Nº ' + asientoModelo.id + ' - "' + asientoModelo.nombre + '"?',
			url = funciones.controllerUrl('borrar'),
			objeto = {id: asientoModelo.id};

		funciones.borrar(msg, url, objeto, function(response) {
			removeTr(asientoModelo.id);
			$.success('El asiento modelo fue eliminado correctamente');
		});
	}

	function clickEditarAsiento(o){
		popUpAgregarEditar(function() {
			$('#inputAsunto').val(o.nombre);
			$('.tabladinamica').tablaDinamica('load', o.detalleJson);
			$('#inputAsientoId').val(o.id);
		});
	}

	function popUpAgregarEditar(callback){
		var div = '<div class="h270 w725  vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td class="w90"><label for="inputAsunto">Asunto:</label></td><td><input id="inputAsunto" class="textbox obligatorio w540" /></td></tr>' +
				  '<input id="inputAsientoId" class="hidden" />' +
				  '</tbody></table>' +
				  '<table class="tabladinamica registrosAlternados"></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {agregarEditarAsiento();}}, {value: 'Cancelar', action: function(){$('.tablaDinamica').tablaDinamica('clean'); $.jPopUp.close();}}];
		$.jPopUp.show(div, botones, null, callback);
		$('.tabladinamica').tablaDinamica(configTablaDinamica);
		$('#inputAsunto').focus();
	}

	function agregarEditarAsiento() {
		var objeto = {
			id: $('#inputAsientoId').val(),
			nombre: $('#inputAsunto').val(),
			detalleJson: $('.tabladinamica').tablaDinamica('getJson')
		};
		var url = funciones.controllerUrl(objeto.id ? 'editar' : 'agregar');
		if (objeto.nombre == '') {
			$.error('Deberá completar el asunto del asiento modelo');
		} else {
			funciones.guardar(url, objeto, function(){
				$('.tablaDinamica').tablaDinamica('clean');
				$.jPopUp.close();
				objeto.detalleJson = this.data.detalleJson;
				if (!objeto.id){
					objeto.id = this.data.id;
					$('#tablaAsientos > tbody').prepend(returnTr(objeto));
				} else {
					$('#tr_' + objeto.id).html('').append(
						$('<td>').append($('<label>').text(objeto.id)).addClass('aCenter'),
						$('<td>').append($('<label>').text(objeto.nombre)),
						$('<td>').append(divBotones(objeto))
					);
				}
			});
		}
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#btnAgregar').show();
				break;
			case 'buscar':
				$('#btnAgregar').show();
				funciones.cambiarTitulo();
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
	<div id='divAsientosModelo' class='w100p  pantalla customScroll h480'>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'popUpAgregarEditar();')); ?>
	</div>
</div>
