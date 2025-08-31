<?php
$idNotificacion = Funciones::get('id');
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Plan de cuentas';
		cambiarModo('inicio');
	});

	function limpiarScreen() {
		$('#divImputaciones').html('');
	}

	function buscar() {
		var url = funciones.controllerUrl('buscar', getFiltrosBuscar());
		var msgError = 'Ocurrió un error al intentar buscar las imputaciones', cbSuccess = function (json) {
			limpiarScreen();
			llenarPantalla(json);
		};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function getFiltrosBuscar() {
		return {
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val(),
			concepto: $('#inputBuscarConcepto').val()
		};
	}

	function llenarPantalla(json) {
		var div = $('#divImputaciones');
		var table = $('<table>').attr('id', 'tablaImputaciones').attr('class', 'registrosAlternados w100p').append(
			$('<thead>').addClass('tableHeader')
				.append($('<tr>').append(
					$('<th>').addClass('w15p').text('Cuenta'),
					$('<th>').addClass('w60p').text('Concepto'),
					$('<th>').addClass('w15p').text('Imputable'),
					$('<th>').addClass('w10p').text('Acción')
				))).append($('<tbody>'));
		var body = table.find('tbody').eq(0);
		for (var i = 0; i < json.length; i++) {
			body.append(returnTr(json[i]));
		}
		div.append(table);
		div.fixedHeader({target: 'table'});
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.cuenta).append(
			$('<td>').append($('<div>').addClass('aCenter').text(o.cuenta)),
			$('<td>').append($('<div>').text(o.concepto)),
			$('<td>').append($('<div>').addClass('bold aCenter').text((o.imputable == 'S' ? 'Imputable': 'No imputable'))),
			$('<td>').append(divBotones(o))
		);
	}

	function divBotones(o) {
		var div = $('<div>').addClass('aCenter'),
			btn;
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar').click($.proxy(function () {
				popUpEditarImputacion();
				setTimeout($.proxy(function(){fillPopUpEditarImputacion(this)}, this), 500);
		}, o)).append($('<img>').attr('src', '/img/botones/40/editar.gif'));
		div.append(btn);
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Eliminar').click($.proxy(function () {
				borrarImputacion(this);
		}, o)).append($('<img>').attr('src', '/img/botones/40/borrar.gif'));
		div.append(btn);
		return div;
	}

	function borrarImputacion(o) {
		var msg = '¿Está seguro que desea borrar la imputacion Nº "' + o.cuenta + '"?',
			url = funciones.controllerUrl('borrar'),
			objeto = {id: o.cuenta};

		funciones.borrar(msg, url, objeto, function(){
			$('#tr_' + o.cuenta).remove();
		});
	}

	function popUpAgregar() {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10"><table><tbody>' +
				  '<tr><td><label for="inputCuenta">Cuenta:</label></td><td><input id="inputCuenta" class="textbox obligatorio inputForm w230" maxlength="7" validate="EnteroPositivo" /></td></tr>' +
				  '<tr><td><label for="inputConcepto">Concepto:</label></td><td><input id="inputConcepto" class="textbox obligatorio inputForm w230" /></td></tr>' +
				  '<tr><td><label>Imputable:</label></td><td><input type="checkbox" class="textbox koiCheckbox" id="inputImputable"></td></tr></tbody></table></div>';
		var botones = [{value: 'Guardar', action: function () {doAgregarImputacion();}}, {value: 'Cancelar', action: function () {$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
	}

	function doAgregarImputacion() {
		var objeto = {
			cuenta: $('#inputCuenta').val(),
			concepto: $('#inputConcepto').val(),
			imputable: ($('#inputImputable').isChecked() ? 'S' : 'N')
		};
		if (objeto.id == '' || objeto.concepto == '') {
			$.error('Complete todos los campos obligatorios.');
		} else {
			funciones.guardar(funciones.controllerUrl('agregar'), objeto, function(){$.jPopUp.close();});
		}
	}

	function popUpEditarImputacion() {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10"><table><tbody>' +
				  '<tr><td><input id="inputId" class="textbox obligatorio inputForm w230 hidden" maxlength="7" validate="EnteroPositivo" /></td></tr>' +
				  '<tr><td><label for="inputCuenta">Cuenta:</label></td><td><input id="inputCuenta" class="textbox obligatorio inputForm w230" maxlength="7" validate="EnteroPositivo" /></td></tr>' +
				  '<tr><td><label for="inputConcepto">Concepto:</label></td><td><input id="inputConcepto" class="textbox obligatorio inputForm w230" /></td></tr>' +
				  '<tr><td><label>Imputable:</label></td><td><input type="checkbox" class="textbox koiCheckbox" id="inputImputable"></td></tr></tbody></table></div>',
			botones = [{value: 'Guardar', action: function(){doEditarImputacion(getObjetoPopUp());$.jPopUp.close();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputCuenta').focus();
	}

	function getObjetoPopUp() {
		return {
			id: $('#inputId').val(),
			cuenta: $('#inputCuenta').val(),
			concepto: $('#inputConcepto').val(),
			imputable: ($('#inputImputable').isChecked() ? 'S' : 'N')
		};
	}

	function fillPopUpEditarImputacion(o) {
		$('#inputId').val(o.cuenta);
		$('#inputCuenta').val(o.cuenta);
		$('#inputConcepto').val(o.concepto);
		if(o.imputable == 'S'){
			$('#inputImputable').check();
		} else {
			$('#inputImputable').uncheck();
		}
	}

	function doEditarImputacion(objeto) {
		if (objeto.cuenta == '' || objeto.concepto == '') {
			$.error('Por favor complete los campos obligatorios');
		} else {
			funciones.guardar(funciones.controllerUrl('editar'), objeto, function(){buscar();});
		}
	}

	function pdfClick(){
		funciones.pdfClick(funciones.controllerUrl('getPdf', getFiltrosBuscar()));
	}

	function xlsClick(){
		funciones.pdfClick(funciones.controllerUrl('getXls', getFiltrosBuscar()));

	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				$('#btnAgregar').show();
				funciones.cambiarTitulo(tituloPrograma);
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divImputaciones' class='w100p customScroll h480'>
		<?php
			//TABLA
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Rango Nº cuenta:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w100' maxlength='7' validate='EnteroPositivo' />
			<input id='inputBuscarHasta' class='textbox filtroBuscar w100' maxlength='7' validate='EnteroPositivo' />
		</div>
		<div>
			<label for='inputBuscarConcepto' class='filtroBuscar'>Concepto:</label>
			<input id='inputBuscarConcepto' class='textbox filtroBuscar w220' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'popUpAgregar();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
