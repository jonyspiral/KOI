<?php
$idUsuarioLogueado = Usuario::logueado()->id;
$admin = Usuario::logueado()->puede('sistema/tickets/administrador/');
?>

<script type='text/javascript'>
	idUsuario = <?php echo '"' . $idUsuarioLogueado . '"'; ?>;
	esAdmin = <?php echo ($admin ? '"S"' : '"N"'); ?>;
	esAdmin = esAdmin == 'S';
	$(document).ready(function(){
		tituloPrograma = 'Seguimiento clientes';
		cambiarModo('inicio');
		<?php if (Funciones::get('idCliente')) { ?>
		$('#inputBuscarCliente, #inputBuscarCliente_selectedValue').val(<?php echo Funciones::get('idCliente'); ?>).blur();
		buscar();
		<?php } ?>
	});

	function limpiarScreen(){
		$('#divGestionesClientesCobranza').html('');
	}
	
	function buscar() {
		if($('#inputBuscarCliente_selectedValue').val() == '') {
			$.error('Debe seleccionar un cliente');
		} else {
			var url = '/content/administracion/cobranzas/seguimiento_clientes/buscar.php?';
			url += 'idCliente=' + $('#inputBuscarCliente_selectedValue').val();
			url += '&fechaDesde=' + $('#inputBuscarDesde').val();
			url += '&fechaHasta=' + $('#inputBuscarHasta').val();
			var msgError = 'Ocurrió un error al intentar buscar',
				cbSuccess = function(json){
					$('#divGestionesClientesCobranza').html('');
					llenarPantalla(json);
				};
			funciones.buscar(url, cbSuccess, msgError);
		}
	}

	function llenarPantalla(json) {
		var div = $('#divGestionesClientesCobranza');
		var table = $('<table>').attr('id', 'tablaGestiones').attr('class', 'registrosAlternados w100p').append(
			$('<thead>').addClass('tableHeader').append(
				$('<tr>').append(
					$('<th>').addClass('w30p').text('Detalle'),
					$('<th>').addClass('w55p').text('Descripción'),
					$('<th>').addClass('w5p').text('Estado'),
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
		$('#divGestionesClientesCobranza').fixedHeader({target: 'table'});
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.id).append(
			$('<td>').append(divDatos(o)),
			$('<td>').append(divDescripcion(o)).addClass('vaTop'),
			$('<td>').append(divEstado(o)),
			$('<td>').append(divBotones(o))
		);
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text('Nº: ' + o.id + ' - Cliente: ' + o.cliente.razonSocial)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha gestión: ' + o.fechaGestion)
				)
			)
		);
		return table;
	}

	function divDescripcion(o) {
		return $('<div>').text(o.observaciones);
	}

	function divEstado(o) {
		return $('<div>').addClass('bold').addClass('aCenter').append($('<input type="checkbox" id="inputEstado' + o.id + '" class="textbox koiCheckbox inputForm"' + (o.estado == '1' ? ' checked ' : ' ') + '>').click($.proxy(editarEstado, o)));
	}

	function editarEstado(){
		var o = this,
			url = '/content/administracion/cobranzas/seguimiento_clientes/editar.php';
		o.estado = ($('#inputEstado' + o.id).isChecked() ? '1' : '0');
		$.showLoading();
		$.postJSON(url, o, function(json){
			$.hideLoading();
			switch (funciones.getJSONType(json)){
				case funciones.jsonNull:
				case funciones.jsonEmpty:
					$.error('Ocurrió un error.');
					break;
				case funciones.jsonError:
					$.error(funciones.getJSONMsg(json));
					break;
				case funciones.jsonSuccess:
					break;
			}
			$.hideLoading();
		});
	}

	function divBotones(o) {
		var btn;
		var div = $('<div>').addClass('aCenter');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
			.click($.proxy(function() {editarGestion(this);}, o))
			.append($('<img>').attr('src', '/img/botones/40/editar.gif'));
		div.append(btn);
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Eliminar')
			.click($.proxy(function() {borrarGestion(this);}, o))
			.append($('<img>').attr('src', '/img/botones/40/borrar.gif'));
		div.append(btn);
		return div;
	}

	function removeTr(id) {
		$('#tr_' + id).remove();
	}

	function borrarGestion(o) {
		var msg = '¿Está seguro que desea borrar la gestión Nº "' + o.id + '"?',
			url = '/content/administracion/cobranzas/seguimiento_clientes/borrar.php',
			objeto = {id: o.id};
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, objeto, function(json){
					$.hideLoading();
					switch (funciones.getJSONType(json)){
						case funciones.jsonNull:
						case funciones.jsonEmpty:
							$.error('Ocurrió un error.');
							break;
						case funciones.jsonError:
							$.error(funciones.getJSONMsg(json));
							break;
						case funciones.jsonSuccess:
							removeTr(o.id);
							$.success('El ticket fue eliminado correctamente');
							break;
					}
				});
			}
		});
	}

	function doAgregarGestion() {
		var objeto = {
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			//fecha: $('#inputFecha').val(),
			observaciones: $('#inputObservaciones').val()
		};
		var url = '/content/administracion/cobranzas/seguimiento_clientes/agregar.php',
			msj = 'La gestión se agregó correctamente';
		if (/*objeto.fecha == '' || */objeto.observaciones == '') {
			$.error('Todos los campos son obligatorios.');
		} else {
			$.showLoading();
			$.jPopUp.close();
			$.postJSON(url, objeto, function(json){
				$.hideLoading();
				switch (funciones.getJSONType(json)){
					case funciones.jsonNull:
					case funciones.jsonEmpty:
						$.error('Ocurrió un error.');
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonSuccess:
						$.success(msj, function(){
							$('#tablaGestiones > tbody').prepend(returnTr(json.data));
						});
						break;
				}
				$.hideLoading();
			});
		}
	}

	function popUpAgregar(){
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td><label for="inputCliente">Cliente:</label></td><td><label>' + $('#inputBuscarCliente').val() + '</label></td></tr>' +
				  //'<tr><td><label for="inputFecha">Fecha:</label></td><td><input id="inputFecha" class="textbox obligatorio w210" validate="Fecha" /></td></tr>' +
				  '<tr><td><label for="inputObservaciones">Observaciones:</label></td><td><textarea id="inputObservaciones" class="textbox obligatorio w230 h150" ></textarea></td></tr>' +
				  '<input id="inputTicketId" class="hidden" rel="id" />' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {doAgregarGestion();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputObservaciones').focus();
	}

	function popUpEditarTicket(o){
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td><label for="inputObservaciones">Observaciones:</label></td><td><textarea id="inputObservaciones" class="textbox obligatorio inputForm w230 h150" ></textarea></td></tr>' +
				  '<input id="inputTicketId" class="hidden" />' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {doEditarGestion(o);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputObservaciones').focus();
	}

	function editarGestion(o){
		popUpEditarTicket(o);
		$('#inputObservaciones').val(o.observaciones);
		$('#inputTicketId').val(o.id);
	}

	function doEditarGestion(o) {
		var objeto = {
			id: $('#inputTicketId').val(),
			observaciones: $('#inputObservaciones').val(),
			estado: ($('#inputEstado' + o.id).isChecked() ? '1' : '0')
		};
		var url = '/content/administracion/cobranzas/seguimiento_clientes/editar.php',
			msj = 'La gestión se editó correctamente';
		if (objeto.observaciones == '') {
			$.error('Todos los campos son obligatorios');
		} else {
			$.showLoading();
			$.jPopUp.close();
			$.postJSON(url, objeto, function(json){
				$.hideLoading();
				switch (funciones.getJSONType(json)){
					case funciones.jsonNull:
					case funciones.jsonEmpty:
						$.error('Ocurrió un error.');
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonSuccess:
						$.success(msj, function(){
							$('#tr_' + json.data.id).html('');
							$('#tr_' + json.data.id).append(
								$('<td>').addClass('w30p').append(divDatos(json.data)),
								$('<td>').addClass('w55p').append(divDescripcion(json.data)).addClass('vaTop'),
								$('<td>').addClass('w5p').append(divEstado(json.data)),
								$('<td>').addClass('w10p').append(divBotones(json.data))
							)
						});
						break;
				}
				$.hideLoading();
			});
		}
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
				funciones.cambiarTitulo(tituloPrograma);
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
	<div id='divGestionesClientesCobranza' class='w100p customScroll h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarCliente' >Cliente:</label>
			<input id='inputBuscarCliente' class='textbox obligatorio autoSuggestBox w200' name='ClienteTodos' />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w180' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w180' from='inputBuscarDesde' validate='Fecha' />
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
