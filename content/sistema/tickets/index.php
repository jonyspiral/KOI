<?php
?>

<script type='text/javascript'>
	$(document).ready(function () {
		tituloPrograma = 'Koi Tickets';
		cambiarModo('inicio');
		<?php if (Funciones::get('idAreaEmpresa')) { ?>
		$('#inputBuscarAreaEmpresa, #inputBuscarAreaEmpresa_selectedValue').val(<?php echo Funciones::get('idAreaEmpresa'); ?>).blur();
		<?php } ?>
		<?php if (Funciones::get('id')) { ?>
		$('#inputBuscarNumeroTicket').val(<?php echo Funciones::get('id'); ?>).blur();
		<?php } ?>
		<?php if (Funciones::get('idAreaEmpresa') || Funciones::get('id')) { ?>
		buscar();
		<?php } ?>
	});

	function limpiarScreen() {
		$('#divTickets').html('');
	}

	function buscar(mios) {
		mios = !(typeof mios == 'undefined');
		var url = funciones.controllerUrl('buscar', {
			idAreaEmpresa: $('#inputBuscarAreaEmpresa_selectedValue').val(),
			estado: $('#inputBuscarEstado').val(),
			autor: (mios ? '2' : $('#inputBuscarAutor').val()),
			prioridad: $('#inputBuscarPrioridad').val(),
			fechaDesde: $('#inputBuscarFechaDesde').val(),
			fechaHasta: $('#inputBuscarFechaHasta').val(),
			numeroTicket: $('#inputBuscarNumeroTicket').val(),
			ordenarPor: $('#inputBuscarOrdenarPor').val()
		});
		var msgError = 'Ocurrió un error al intentar buscar los tickets', cbSuccess = function (json) {
			$('#divTickets').html('');
			llenarPantalla(json);
		};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function llenarPantalla(json) {
		var div = $('#divTickets');
		var table = $('<table>').attr('id', 'tablaTickets').attr('class', 'registrosAlternados w100p').append($('<thead>').addClass('tableHeader').append($('<tr>').append($('<th>').addClass('w25p').text('Detalle'), $('<th>').addClass('w45p').text('Descripción'), $('<th>').addClass('w20p').text('Estado'), $('<th>').addClass('w10p').text('Acción')))).append($('<tbody>'));
		var body = table.find('tbody').eq(0);
		for (var i = 0; i < json.length; i++) {
			body.append(returnTr(json[i]));
		}
		div.append(table);
	}

	function returnTr(o) {
		var estado, clase, fecha, responsable = (o.idResponsable ? o.responsable.id : '');
		if (o.fechaCierre) {
			estado = o.estadoNombre;
			fecha = 'F. cierre: ' + o.fechaCierre;
			clase = 'indicador-' + o.colorIndicador;
		} else if (o.fechaEstimadaResolucion || responsable) {
			estado = 'En progreso';
			if (o.fechaEstimadaResolucion) {
				fecha = 'F. estimada: ' + o.fechaEstimadaResolucion;
			} else {
				fecha = 'Prioridad: ' + o.prioridadNombre;
			}
			clase = 'indicador-amarillo';
		} else {
			estado = 'Pendiente';
			fecha = 'Prioridad: ' + o.prioridadNombre;
			clase = 'indicador-gris';
		}
		return $('<tr>').attr('id', 'tr_' + o.id).append($('<td>').append(divDatos(o)), $('<td>').append(divDescripcion(o), divRespuesta(o)).addClass('vaTop'), $('<td>').append(divEstado(estado, responsable, fecha)).addClass(clase), $('<td>').append(divBotones(o)));
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append($('<td>').addClass('bold aLeft').append($('<label>').text('Ticket Nº: ' + o.id + ' - Usuario: ' + o.usuario.id))),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append($('<label>').text('Fecha alta: ' + o.fechaAlta)),
				$('<td>').addClass('aRight').append($('<label>').attr('title', 'Area: ' + o.areaEmpresa.nombre).text('Area: ' + funciones.acortarString(o.areaEmpresa.nombre, 12, '...')))
			));

		return table;
	}

	function divDescripcion(o) {
		return $('<div>').addClass('w90p').text(o.descripcion);
	}

	function divRespuesta(o) {
		return (!o.respuesta) ? $('<div>') : $('<div>').addClass('fRight w90p aRight pTop5 bold s14 lightRed').text(o.respuesta)
	}

	function divEstado(estado, responsable, fecha) {
		return $('<div>').addClass('bold').addClass('aCenter').append($('<label>').text(estado), $('<label>').addClass('orange').text(responsable ? ' (' + responsable + ')': ''), $('<br>'), $('<label>').text(fecha));
	}

	function divBotones(o) {
		var btn, puedeEditar = (o.esResponsable || o.esAutor) && !o.fechaCierre, puedeBorrar = o.esAutor && !o.fechaCierre;
		var div = $('<div>').addClass('aCenter');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar').click($.proxy(function () {
			if (puedeEditar) {
				popUpEditarTicket(this);
				setTimeout($.proxy(function(){fillPopUpEditarTicket(this)}, this), 300);
			}
		}, o)).append($('<img>').attr('src', '/img/botones/40/editar' + ((puedeEditar) ? '' : '_off') + '.gif'));
		div.append(btn);
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Eliminar').click($.proxy(function () {
			if (puedeBorrar) {
				borrarTicket(this);
			}
		}, o)).append($('<img>').attr('src', '/img/botones/40/borrar' + ((puedeBorrar) ? '' : '_off') + '.gif'));
		div.append(btn);
		return div;
	}

	function borrarTicket(o) {
		var msg = '¿Está seguro que desea borrar el ticket Nº ' + o.id + ' de "' + o.usuario.id + '"?', url = funciones.controllerUrl('borrar'), objeto = {id: o.id};
		$.confirm(msg, function (r) {
			if (r == funciones.si) {
				$.showLoading();
				$.postJSON(url, objeto, function (json) {
					$.hideLoading();
					switch (funciones.getJSONType(json)) {
						case funciones.jsonNull:
						case funciones.jsonEmpty:
							$.error('Ocurrió un error.');
							break;
						case funciones.jsonError:
							$.error(funciones.getJSONMsg(json));
							break;
						case funciones.jsonSuccess:
							$('#tr_' + o.id).remove();
							$.success('El ticket fue eliminado correctamente');
							break;
					}
				});
			}
		});
	}

	function popUpAgregar() {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10"><table><tbody>' +
				  '<tr><td><label for="inputAreaEmpresa">Área/departamento del ticket:</label></td><td><input id="inputAreaEmpresa" class="textbox autoSuggestBox obligatorio inputForm w230" name="AreaEmpresa" alt="&habilitadaTicket=1" /></td></tr>' +
				  '<tr><td><label for="inputDescripcion">Descripción del problema/sugerencia:</label></td><td><textarea id="inputDescripcion" class="textbox obligatorio inputForm w230 h150"></textarea></td></tr>' +
				  '<tr><td><label for="inputPrioridadExterna">Prioridad:</label></td><td><select id="inputPrioridadExterna" class="textbox obligatorio filtroBuscar w245"><option value="1">Baja</option><option value="2">Media</option><option value="3">Alta</option><option value="4">Urgente</option></select></td></tr></tbody></table></div>';
		var botones = [{value: 'Guardar', action: function () {doAgregarTicket();}}, {value: 'Cancelar', action: function () {$.jPopUp.close();}}];
		$.jPopUp.show(div, botones, null, function() {
			if ($('#inputBuscarAreaEmpresa_selectedValue').val()) {
				$('#inputAreaEmpresa').val($('#inputBuscarAreaEmpresa_selectedValue').val()).blur();
				$('#inputDescripcion').focus();
			} else {
				$('#inputAreaEmpresa').focus();
			}
		});
	}

	function doAgregarTicket() {
		var objeto = {
			idAreaEmpresa: $('#inputAreaEmpresa_selectedValue').val(),
			descripcion: $('#inputDescripcion').val(),
			prioridadExterna: $('#inputPrioridadExterna').val()
		};
		var url = funciones.controllerUrl('agregar'), msj = 'El ticket se agregó correctamente';
		if (objeto.descripcion == '' || objeto.prioridadExterna == '') {
			$.error('Todos los campos son obligatorios.');
		} else {
			$.showLoading();
			$.jPopUp.close();
			$.postJSON(url, objeto, function (json) {
				$.hideLoading();
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
					case funciones.jsonEmpty:
						$.error('Ocurrió un error.');
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonSuccess:
						objeto.id = json.data.id;
						objeto.usuario = json.data.usuario;
						$.success(msj, function () {
							buscar(true);
						});
						break;
				}
				$.hideLoading();
			});
		}
	}

	function popUpEditarTicket(o) {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10"><table><tbody>' +
				  '<tr><td><label for="inputAreaEmpresa">Área/departamento del ticket:</label></td><td><input id="inputAreaEmpresa" class="textbox autoSuggestBox obligatorio inputForm w230" name="AreaEmpresa" alt="&habilitadaTicket=1" /></td></tr>' +
				  '<tr><td><label for="inputDescripcion">Descripción del problema/sugerencia:</label></td><td><textarea id="inputDescripcion" class="textbox obligatorio inputForm w230 h150" ></textarea></td></tr>' +
				  '<tr><td><label for="inputPrioridadExterna">Prioridad:</label></td><td><select id="inputPrioridadExterna" class="textbox obligatorio filtroBuscar w245"><option value="1">Baja</option><option value="2">Media</option><option value="3">Alta</option><option value="4">Urgente</option></select></td></tr>';
		if (o.esResponsable) {
			div += '<tr><td><label for="inputPrioridadInterna">Prioridad interna:</label></td><td><select id="inputPrioridadInterna" class="textbox filtroBuscar w245"><option value="1">Baja</option><option value="2">Media</option><option value="3">Alta</option><option value="4">Urgente</option></select></td></tr>' +
				   '<tr><td><label for="inputResponsable">Responsable:</label></td><td><input id="inputResponsable" class="textbox autoSuggestBox inputForm w230" name="UsuarioPorAreaEmpresa" linkedTo="inputAreaEmpresa,AreaEmpresa" /></td></tr>' +
				   '<tr><td><label for="inputFechaEstimadaResolucion">Fecha estimada resolución:</label></td><td><input id="inputFechaEstimadaResolucion" class="textbox w210" validate="Fecha" /></td></tr>' +
				   '<tr><td><label for="inputRespuesta">Respuesta del responsable:</label></td><td><textarea id="inputRespuesta" class="textbox inputForm w230 h150" ></textarea></td></tr>';
		}
		div += '<input id="inputTicketId" class="hidden" /></tbody></table></div>';
		var botones;
		if (o.esResponsable) {
			botones = [
				{value: 'Guardar', action: function () {doEditarTicket(getObjetoPopUp());}},
				{value: 'Resolver', action: function () {resolverTicket()}},
				{value: 'Delegar', action: function () {delegarTicket(o);}},
				{value: 'Rechazar', action: function () {rechazarTicket(o);}},
				{value: 'Cancelar', action: function () {$.jPopUp.close();}}
			];
		} else {
			botones = [{value: 'Guardar', action: function () {doEditarTicket(getObjetoPopUp());}}, {value: 'Cancelar', action: function () {$.jPopUp.close();}}];
		}
		$.jPopUp.show(div, botones);
		$('#inputDescripcion').focus();
		if (!o.esAutor) {
			$('#inputDescripcion, #inputPrioridadExterna').disable();
			(!o.idResponsable) ? $('#inputResponsable').focus() : ((!o.fechaEstimadaResolucion) ? $('#inputFechaEstimadaResolucion').focus() : $('#inputRespuesta').focus());
		}
	}

	function getObjetoPopUp() {
		return {
			id: $('#inputTicketId').val(),
			descripcion: $('#inputDescripcion').val(),
			prioridadExterna: $('#inputPrioridadExterna').val(),
			prioridadInterna: $('#inputPrioridadInterna').val(),
			idResponsable: $('#inputResponsable_selectedValue').val(),
			fechaEstimadaResolucion: $('#inputFechaEstimadaResolucion').val(),
			respuesta: $('#inputRespuesta').val()
		};
	}

	function resolverTicket() {
		doEditarTicket(getObjetoPopUp(), 'R');
	}

	function delegarTicket(o) {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10"><table><tbody>' +
				  '<tr><td><label for="inputAreaEmpresaDelegar">Área/departamento a delegar:</label></td><td><input id="inputAreaEmpresaDelegar" class="textbox autoSuggestBox obligatorio inputForm w230" name="AreaEmpresa" alt="&habilitadaTicket=1" /></td></tr>' +
				  '</tbody></table></div>';
		var objeto = getObjetoPopUp();
		var botones = [{value: 'Aceptar', action: $.proxy(function () {
			var auxValue = $('#inputAreaEmpresaDelegar_selectedValue').val();
			if (!auxValue) {
				$.error('Debe elegir un área para delegar el ticket');
			} else {
				doEditarTicket(this, 'D', auxValue);
			}
		}, objeto)}, {value: 'Cancelar', action: function () {$.jPopUp.close();}}];
		$.jPopUp.close(function() {
			$.jPopUp.show(div, botones, null, $.proxy(function() {
				$('#inputAreaEmpresaDelegar').focus();
			}, o));
		});
	}

	function rechazarTicket(o) {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10"><table><tbody>' +
				  '<tr><td><label for="inputRespuestaRechazar">Motivo del rechazo (respuesta):</label></td><td><textarea id="inputRespuestaRechazar" class="textbox inputForm w230 h150" ></textarea></td></tr>' +
				  '</tbody></table></div>';
		var objeto = getObjetoPopUp();
		var botones = [{value: 'Aceptar', action: $.proxy(function () {
			var auxValue = $('#inputRespuestaRechazar').val();
			doEditarTicket(this, 'Z', auxValue);
		}, objeto)}, {value: 'Cancelar', action: function () {$.jPopUp.close();}}];
		$.jPopUp.close(function() {
			$.jPopUp.show(div, botones, null, $.proxy(function() {
				$('#inputRespuestaRechazar').val(this.respuesta).focus();
			}, o));
		});
	}

	function fillPopUpEditarTicket(o) {
		$('#inputAreaEmpresa').val(o.areaEmpresa.id).disable().blur();
		$('#inputDescripcion').val(o.descripcion);
		$('#inputPrioridadExterna').val(o.prioridadExterna);
		$('#inputPrioridadInterna').val(o.prioridadInterna);
		$('#inputResponsable').val(o.idResponsable).blur();
		$('#inputFechaEstimadaResolucion').val(o.fechaEstimadaResolucion);
		$('#inputRespuesta').val(o.respuesta);
		$('#inputTicketId').val(o.id);
	}

	function doEditarTicket(objeto, estado, auxValue) {
		estado = (typeof estado == 'undefined' ? '' : estado);
		auxValue = (typeof auxValue == 'undefined' ? '' : auxValue);
		objeto['estado'] = estado;
		objeto['auxValue'] = auxValue;
		var url = funciones.controllerUrl('editar'),
			msj = 'El ticket se editó correctamente';
		if (objeto.descripcion == '' || objeto.prioridadExterna == '') {
			$.error('Por favor complete los campos obligatorios');
		} else {
			$.showLoading();
			$.jPopUp.close();
			$.postJSON(url, objeto, function (json) {
				$.hideLoading();
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
					case funciones.jsonEmpty:
						$.error('Ocurrió un error.');
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonSuccess:
						$.success(msj, function () {
							buscar();
						});
						break;
				}
				$.hideLoading();
			});
		}
	}

	function cambiarModo(modo) {
		funciones.cambiarModo(modo);
		switch (modo) {
			case 'inicio':
				break;
			case 'buscar':
				$('#btnAgregar').show();
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
	<div id='divTickets' class='w100p customScroll h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarAreaEmpresa' class='filtroBuscar'>Área empresa:</label>
			<input id='inputBuscarAreaEmpresa' class='textbox autoSuggestBox filtroBuscar w200' name='AreaEmpresa' alt='&habilitadaTicket=1'/>
		</div>
		<div>
			<label for='inputBuscarEstado' class='filtroBuscar'>Estado:</label>
			<select id='inputBuscarEstado' class='textbox obligatorio filtroBuscar w200'>
				<option value='0'>Pendientes</option>
				<option value='1'>Terminados</option>
				<option value='2'>Todos</option>
			</select>
		</div>
		<div>
			<label for='inputBuscarAutor' class='filtroBuscar'>Autor:</label>
			<select id='inputBuscarAutor' class='textbox obligatorio filtroBuscar w200'>
				<option value='1'>Todos</option>
				<option value='2'>Yo</option>
			</select>
		</div>
		<div>
			<label for='inputBuscarPrioridad' class='filtroBuscar'>Prioridad:</label>
			<select id='inputBuscarPrioridad' class='textbox obligatorio filtroBuscar w200'>
				<option value='0'>Todos</option>
				<option value='1'>Baja</option>
				<option value='2'>Media</option>
				<option value='3'>Alta</option>
				<option value='4'>Urgente</option>
			</select>
		</div>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar w180' to='inputFechaDesde' validate='Fecha'/>
		</div>
		<div>
			<label for='inputBuscarFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar w180' from='inputFechaHasta'' validate='Fecha'/>
		</div>
		<div>
			<label for='inputBuscarNumeroTicket' class='filtroBuscar'>Número ticket:</label>
			<input id='inputBuscarNumeroTicket' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputBuscarOrdenarPor' class='filtroBuscar'>Ordenar por:</label>
			<select id='inputBuscarOrdenarPor' class='textbox filtroBuscar w200'>
				<option value=''>---</option>
				<option value='0'>Prioridad descendente</option>
				<option value='1'>Fecha ascendente</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif"/></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'popUpAgregar();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
