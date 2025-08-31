<?php

?>

<script type='text/javascript'>
	var configTablaDinamica;

	$(document).ready(function(){
		tituloPrograma = 'Asientos contables';
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
						id: 'importeDebe',
						name: 'Debe',
						width: '86px',
						cellType: 'I',
						template: '<input class="textbox obligatorio w70 aRight importesDebe" type="text" validate="DecimalPositivo" />',
						blur: sumarizarImportes
					},
					{
						id: 'importeHaber',
						name: 'Haber',
						width: '86px',
						cellType: 'I',
						template: '<input class="textbox obligatorio w70 aRight importesHaber" type="text" validate="DecimalPositivo" />',
						blur: sumarizarImportes
					},
					{
						id: 'fechaVencimiento',
						name: 'Fecha vto.',
						width: '112px',
						cellType: 'I',
						template: '<input class="textbox obligatorio w80" type="text" validate="Fecha" />',
						notEmpty: true,
						valueByFunction: function() {return funciones.hoy();}
					},
					{
						id: 'observaciones',
						name: 'Observaciones',
						width: '196px',
						cellType: 'I',
						template: '<input class="textbox w180" type="text" />'
					}
				],
				defaultRows: 2,
				saveCallback: false,
				removeCallback: false,
				pluralName: 'asientos',
				minRows: 2
			}
		;
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divAsientos').html('');
	}

	function buscar() {
		var url = funciones.controllerUrl('buscar', {
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			asientoDesde: $('#inputAsientoDesde').val(),
			asientoHasta: $('#inputAsientoHasta').val(),
			consolidado: ($('#inputConsolidado').isChecked() ? 'S' : 'N'),
			orden: $('#inputOrden').val()
		});
		var msgError = 'Ocurrió un error al intentar buscar los asientos',
			cbSuccess = function(json){
				funciones.limpiarScreen();
				llenarPantalla(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function llenarPantalla(json) {
		var div = $('#divAsientos'),
			empresa = '',
			table;

		if($('#inputConsolidado').isChecked()){
			empresa = $('<th>').addClass('w3p').text('E');
		}

		table = $('<table>').attr('id', 'tablaAsientos').attr('class', 'registrosAlternados w100p').append(
		$('<thead>').addClass('tableHeader').append(
			$('<tr>').append(
				$('<th>').addClass('w7p').text('Fecha'),
				empresa,
				$('<th>').addClass('w5p').text('Nº'),
				$('<th>').addClass('w67p').text('Asunto'),
				$('<th>').addClass('w10p').text('Importe'),
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
		var empresa = '';

		if($('#inputConsolidado').isChecked()){
			empresa = $('<td>').append($('<label>').text(o.empresa)).addClass('aCenter');
		}

		return $('<tr>').attr('id', 'tr_' + o.id).append(
			$('<td>').append($('<label>').text(o.fecha)).addClass('aCenter'),
			empresa,
			$('<td>').append($('<label>').text(o.id)).addClass('aCenter'),
			$('<td>').append($('<label>').text(o.nombre)),
			$('<td>').append($('<label>').text(funciones.formatearMoneda(o.importe))).addClass('aRight'),
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

	function borrarAsiento(asiento) {
		var msg = '¿Está seguro que desea borrar el asiento Nº ' + asiento.id + ' - "' + asiento.nombre + '"?',
			url = funciones.controllerUrl('borrar'),
			objeto = {id: asiento.id};

		funciones.borrar(msg, url, objeto, function(response) {
			removeTr(asiento.id);
			$.success('El asiento fue eliminado correctamente');
		});
	}

	function clickEditarAsiento(o){
		popUpAgregarEditar(function() {
			$('#inputAsunto').val(o.nombre);
			$('#inputFechaAsiento').val(o.fecha);
			$('.tabladinamica').tablaDinamica('load', o.detalleJson);
			$('#inputAsientoId').val(o.id);
		});
	}

	function popUpAgregarEditar(callback){
		var div = '<div class="h270 w800  vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td class="w120"><label for="inputAsientoModelo">Modelo:</label></td><td class="w240"><input id="inputAsientoModelo" class="textbox autoSuggestBox w200" name="AsientoContableModelo" /></td><td><a class="boton vaMiddle actionAplicarModelo" href="#"><img src="/img/botones/25/aceptar.gif"></a></td></tr>' +
				  '<tr><td class="w120"><label for="inputAsunto">Asunto:</label></td><td colspan="2"><input id="inputAsunto" class="textbox obligatorio w580" /></td></tr>' +
				  '<tr><td><label for="inputFechaAsiento">Fecha del asiento:</label></td><td><input id="inputFechaAsiento" class="textbox obligatorio w100" validate="Fecha" /></td></tr>' +
				  '<input id="inputAsientoId" class="hidden" />' +
				  '</tbody></table>' +
				  '<table class="tabladinamica registrosAlternados"></table>' +
				  '<table><tbody>' +
				  '<tr><td style="width: 216px;"><label>Totales:</label></td><td id="labelImporteDebe" style="width: 86px;"></td><td id="labelImporteHaber" style="width: 86px;"></td></tr>' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {agregarEditarAsiento();}}, {value: 'Cancelar', action: function(){$('.tablaDinamica').tablaDinamica('clean'); $.jPopUp.close();}}];
		$.jPopUp.show(div, botones, null, callback);
		$('.tabladinamica').tablaDinamica(configTablaDinamica);
		$('.actionAplicarModelo').click(aplicarModelo);
		$('#inputFechaAsiento').val(funciones.hoy()).focus();
		$('#inputAsientoModelo').focus();
	}

	function agregarEditarAsiento() {
		var objeto = {
			id: $('#inputAsientoId').val(),
			nombre: $('#inputAsunto').val(),
			fecha: $('#inputFechaAsiento').val(),
			detalleJson: $('.tabladinamica').tablaDinamica('getJson')
		};
		var url = funciones.controllerUrl(objeto.id ? 'editar' : 'agregar');
		if (objeto.nombre == '' || objeto.fecha == '') {
			$.error('Deberá completar al menos el asunto y la fecha del asiento');
		} else {
			funciones.guardar(url, objeto, function(){
				$('.tablaDinamica').tablaDinamica('clean');
				$.jPopUp.close();
				objeto.importe = json.data.importe;
				objeto.detalleJson = this.data.detalleJson;
				if (!objeto.id){
					objeto.id = this.data.id;
					$('#tablaAsientos > tbody').prepend(returnTr(objeto));
				} else {
					$('#tr_' + objeto.id).html('').append(
						$('<td>').append($('<label>').text(objeto.fecha)).addClass('aCenter'),
						$('<td>').append($('<label>').text(objeto.id)).addClass('aCenter'),
						$('<td>').append($('<label>').text(objeto.nombre)),
						$('<td>').append($('<label>').text(funciones.formatearMoneda(objeto.importe))).addClass('aRight'),
						$('<td>').append(divBotones(objeto))
					);
				}
			});
		}
	}

	function aplicarModelo() {
		var idAsientoModelo = $('#inputAsientoModelo_selectedValue').val();
		if (idAsientoModelo) {
			var url = funciones.controllerUrl('getInfoAsientoModelo', {id: idAsientoModelo});
			var msgError = 'Ocurrió un error al intentar aplicar el asiento modelo',
				cbSuccess = function(json){
					$('.tablaDinamica').tablaDinamica('clean');
					$('#inputAsunto').val(json.data.nombre);
					$('.tabladinamica').tablaDinamica('load', json.data.detalleJson);
				};
			funciones.get(url, {}, cbSuccess, null, msgError);
		} else {
			$('#inputAsientoModelo, #inputAsientoModelo_selectedValue').val('')
		}
	}

	function sumarizarImportes() {
		var importeDebe = 0, importeHaber = 0;
		$('.importesDebe').each(function() {
			importeDebe += funciones.toFloat($(this).val());
		});
		$('.importesHaber').each(function() {
			importeHaber += funciones.toFloat($(this).val());
		});
		$('#labelImporteDebe').text(funciones.formatearMoneda(importeDebe));
		$('#labelImporteHaber').text(funciones.formatearMoneda(importeHaber));
	}

	function pdfClick(){
		var finalUrl = urlToExport('pdf');
		if (finalUrl)
			funciones.pdfClick(finalUrl);
	}

	function xlsClick(){
		var finalUrl = urlToExport('xls');
		if (finalUrl)
			funciones.xlsClick(finalUrl);
	}

	function urlToExport(tipo){
		var url = funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), {
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			asientoDesde: $('#inputAsientoDesde').val(),
			asientoHasta: $('#inputAsientoHasta').val(),
			consolidado: ($('#inputConsolidado').isChecked() ? 'S' : 'N'),
			orden: $('#inputOrden').val()
		});
		return url;
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
	<div id='divAsientos' class='w100p  pantalla customScroll h480'>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w160' to='inputFechaHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputFechaHasta' class='textbox filtroBuscar w160' from='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputAsientoDesde' class='filtroBuscar'>Nº asiento desde:</label>
			<input id='inputAsientoDesde' class='textbox filtroBuscar w180' />
		</div>
		<div>
			<label for='inputAsientoHasta' class='filtroBuscar'>Nº asiento hasta:</label>
			<input id='inputAsientoHasta' class='textbox filtroBuscar w180' />
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w180'>
				<option value='0'>Nº de asiento descendente</option>
				<option value='1'>Nº de asiento ascendente</option>
				<option value='2'>Fecha descendente</option>
				<option value='3'>Fecha ascendente</option>
			</select>
		</div>
		<div class='fLeft'>
			<label class='filtroBuscar fLeft pRight3 w99'>Consolidado:</label>
			<input id='inputConsolidado' type='checkbox' class='filtroBuscar' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'popUpAgregarEditar();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
