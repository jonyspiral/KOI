<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Cierres de períodos fiscales';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divCierresPeriodosFiscales').html('');
	}

	function buscar() {
		var url = funciones.controllerUrl('buscar', {
			idTipoPeriodoFiscal: $('#inputBuscarTipoPeriodoFiscal_selectedValue').val()
		});
		var msgError = 'Ocurrió un error al intentar buscar los cierres de períodos fiscales',
			cbSuccess = function(json){
				funciones.limpiarScreen();
				llenarPantalla(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function llenarPantalla(json) {
		var div = $('#divCierresPeriodosFiscales'),
			table;

		table = $('<table>').attr('id', 'tablaCierresPeriodosFiscales').attr('class', 'registrosAlternados w100p').append(
		$('<thead>').addClass('tableHeader').append(
			$('<tr>').append(
				$('<th>').addClass('w8p').text('Nº'),
				$('<th>').addClass('w44p').text('Tipo'),
				$('<th>').addClass('w20p').text('Fecha desde'),
				$('<th>').addClass('w20p').text('Fecha hasta'),
				$('<th>').addClass('w8p')
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
			$('<td>').append($('<label>').text(o.tipoPeriodoFiscal.nombre)),
			$('<td>').append($('<label>').text(o.fechaDesde)).addClass('aCenter'),
			$('<td>').append($('<label>').text(o.fechaHasta)).addClass('aCenter'),
			$('<td>').append(divBotones(o))
		);
	}

	function divBotones(o) {
		var btn;
		var div = $('<div>').addClass('aCenter');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
			.click($.proxy(function() {clickEditarCierrePeriodoFiscal(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/editar.gif'));
		div.append(btn);
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Eliminar')
			.click($.proxy(function() {borrarCierrePeriodoFiscal(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/borrar.gif'));
		div.append(btn);
		return div;
	}

	function removeTr(id) {
		$('#tr_' + id).remove();
	}

	function borrarCierrePeriodoFiscal(cierrePeriodoFiscal) {
		var msg = '¿Está seguro que desea borrar el cierre de período fiscal que va desde el ' + cierrePeriodoFiscal.fechaDesde + ' hasta el ' + cierrePeriodoFiscal.fechaHasta + '?',
			url = funciones.controllerUrl('borrar'),
			objeto = {id: cierrePeriodoFiscal.id};

		funciones.borrar(msg, url, objeto, function(response) {
			removeTr(cierrePeriodoFiscal.id);
			$.success('El cierre de período fiscal fue eliminado correctamente');
		});
	}

	function clickEditarCierrePeriodoFiscal(o){
		popUpAgregarEditar(function() {
			$('#inputTipoPeriodoFiscal, #inputTipoPeriodoFiscal_selectedValue').val(o.idTipoPeriodoFiscal).blur();
			$('#inputFechaDesde').val(o.fechaDesde);
			$('#inputFechaHasta').val(o.fechaHasta);
			$('#inputCierrePeriodoFiscalId').val(o.id);
		});
	}

	function popUpAgregarEditar(callback){
		var div = '<div class="h150 w400  vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td><label for="inputFechaDesde">Fecha desde:</label></td><td><input id="inputFechaDesde" class="textbox obligatorio w140" validate="Fecha" /></td></tr>' +
				  '<tr><td><label for="inputFechaHasta">Fecha hasta:</label></td><td><input id="inputFechaHasta" class="textbox obligatorio w140" validate="Fecha" /></td></tr>' +
				  '<tr><td class="w160"><label for="inputTipoPeriodoFiscal">Tipo de período fiscal:</label></td><td class="w240"><input id="inputTipoPeriodoFiscal" class="textbox obligatorio autoSuggestBox w200" name="TipoPeriodoFiscal" /></td></tr>' +
				  '<input id="inputCierrePeriodoFiscalId" class="hidden" />' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {agregarEditarCierrePeriodoFiscal();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones, null, callback);
		$('#inputFechaDesde').focus();
	}

	function agregarEditarCierrePeriodoFiscal() {
		var objeto = {
			id: $('#inputCierrePeriodoFiscalId').val(),
			idTipoPeriodoFiscal: $('#inputTipoPeriodoFiscal_selectedValue').val(),
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val()
		};
		var url = funciones.controllerUrl(objeto.id ? 'editar' : 'agregar');
		if (objeto.fechaDesde == '' || objeto.fechaHasta == '' || objeto.idTipoPeriodoFiscal == '') {
			$.error('Deberá completar todos los campos');
		} else {
			funciones.guardar(url, objeto, function(){
				$.jPopUp.close();
				objeto.tipoPeriodoFiscal = this.data.tipoPeriodoFiscal;
				if (!objeto.id) {
					if ($('#inputBuscarTipoPeriodoFiscal_selectedValue').val() == objeto.idTipoPeriodoFiscal) {
						objeto.id = this.data.id;
						$('#tablaCierresPeriodosFiscales > tbody').prepend(returnTr(objeto));
					}
				} else {
					var row = $('#tr_' + objeto.id);
					row.html('');
					if ($('#inputBuscarTipoPeriodoFiscal_selectedValue').val() == objeto.idTipoPeriodoFiscal) {
						row.append(returnTr(objeto));
					}
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
				funciones.cambiarTitulo(tituloPrograma + $('#inputBuscarTipoPeriodoFiscal_selectedName').val());
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
	<div id='divCierresPeriodosFiscales' class='w100p  pantalla customScroll h480'>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarTipoPeriodoFiscal' class='filtroBuscar'>Tipo de período fiscal:</label>
			<input id='inputBuscarTipoPeriodoFiscal' class='textbox autoSuggestBox filtroBuscar w200' name='TipoPeriodoFiscal' alt='' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'popUpAgregarEditar();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
