<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Chequera';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divTablaChequera').html('');
	}

	function hayErrorGuardar(){
		if($('#inputBanco_selectedValue').val() == '') {
			return 'Debe seleccionar un banco';
		}

		if($('#inputSucursal_selectedValue').val() == '') {
			return 'Debe seleccionar una sucursal';
		}

		if($('#inputNumeroInicio').val() == '') {
			return 'Debe ingresar un número de inicio para la chequera';
		}

		if($('#inputNumeroFin').val() == '') {
			return 'Debe ingresar un número de fin para la chequera';
		}

		return false;
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/tesoreria/cheques/chequera/buscar.php?';
		url += 'idCuentaBancaria=' + $('#inputBuscarCuentaBancaria_selectedValue').val();
		url += '&fechaDesde=' + $('#inputFechaDesde').val();
		url += '&fechaHasta=' + $('#inputFechaHasta').val();
		var msgError = 'No hay cheques con ese filtro',
			cbSuccess = function(json){
				llenarPantalla(json);
				funciones.cambiarTitulo(tituloPrograma);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function guardar(){
		var url = '/content/administracion/tesoreria/cheques/chequera/agregar.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar());
		} catch (ex) {
			$.error(ex);
		}
	}

	function llenarPantalla(json) {
		var divTabla = $('#divTablaChequera'),
			tabla,
			tbody,
			item;

		$.each(json, function(key, value){
			item = value;

			tabla = $('<table>').addClass('registrosAlternados w100p')
				.append($('<thead>').addClass('tableHeader')
							.append($('<tr>').append(
						$('<th>').addClass('w80p').append('Cheque número'),
						$('<th>').addClass('w20p').append('Borrar')
					)));
			tbody = $('<tbody>');

			$.each(item.detalle, function(key, value){
				tbody.append($('<tr>').append(
					$('<td>').append('Cheque número: ' + value.numero + ' - ' + item.cuentaBancaria),
					$('<td>').addClass('aCenter').append($('<input>')
															 .attr('type', 'checkbox')
															 .attr('id', 'inputCheckbox_' + item.id + '_' + value.idChequeraItem)
															 .addClass('textbox koiCheckbox borrarChequeraItem borrarChequeraItem' + item.id)
															 .data('idChequera', item.id)
															 .data('idChequeraItem', value.idChequeraItem))))
			});
			tabla.append(tbody);
			divTabla.append(
				$('<div>').attr('id', 'divChequera_' + item.id)
					.append($('<div>')
								.append('Chequera Nº ' + item.id + ' - ' + item.cuentaBancaria + ' - ' + item.fecha + ' (' + item.numeroInicio + ' - ' + item.numeroFin + ')')
								.append($('<div>')
											.append('Borrar todos: ')
											.addClass('fRight pRight10')
											.append($('<input>')
														.attr('type', 'checkbox')
														.attr('id', 'inputCheckbox_' + item.id)
														.attr('class', 'textbox koiCheckbox borrarChequera')
														.data('idChequera', item.id))))
					.append($('<div>').append(tabla))
			);
		});

		$('.koiCheckbox').enable();
		$('.acordeon').acordeon();
		$('.borrarChequera').click(function(){
			var checkboxes = $('.borrarChequeraItem' + $(this).data('idChequera'));
			$(this).isChecked() ? checkboxes.check() : checkboxes.uncheck();
		});
		$('.borrarChequeraItem').click(function(){
			var all = true;
			$('.borrarChequeraItem' + $(this).data('idChequera')).each(function(){
				if (!$(this).isChecked())
					all = false;
			});
			var checkbox = $('#inputCheckbox_' + $(this).data('idChequera'));
			all ? checkbox.check() : checkbox.uncheck();
		});
	}

	function borrar(){
		var chequesBorrar = armoObjetoBorrar(),
			arrayChequeras = [];

		$.each(chequesBorrar.cheques, function(key, value){
			arrayChequeras[value.idChequera] = value.idChequera;
		});
		if(arrayChequeras.length > 0){
			var msg = '¿Está seguro que desea borrar los cheques seleccionados?',
				msgVariasChequeras = 'Se seleccionaron cheques de diferentes chequeras. ',
				url = '/content/administracion/tesoreria/cheques/chequera/borrar.php';
			$.confirm((arrayChequeras.length > 2 ? msgVariasChequeras : '') + msg, function(r){
				if (r == funciones.si){
					$.showLoading();
					$.postJSON(url, chequesBorrar, function(json){
						$.hideLoading();
						switch (funciones.getJSONType(json)){
							case funciones.jsonNull:
							case funciones.jsonEmpty:
								$.error('Ocurrió un error');
								break;
							case funciones.jsonError:
								$.error(funciones.getJSONMsg(json));
								break;
							case funciones.jsonSuccess:
								$.success('El cheque se ha borrado correctamente de la chequera', function(){
									cambiarModo('inicio');
								});
								break;
						}
					});
				}
			});
		}else{
			$.error('No se seleccionó ningun cheque para borrar.');
		}
	}

	function armoObjetoBorrar(){
		var aux,
			arrayChequesBorrar = {},
			i = 0;
		$('.borrarChequeraItem').each(function(){
			if($(this).isChecked()){
				aux = {};
				aux['idChequera'] = $(this).data('idChequera');
				aux['idChequeraItem'] = $(this).data('idChequeraItem');
				arrayChequesBorrar[i++] = aux;
			}
		});
		return {cheques: arrayChequesBorrar};
	}
	function armoObjetoGuardar(){
		return {
			idCuentaBancaria: $('#inputCuentaBancaria_selectedValue').val(),
			numeroInicio: $('#inputNumeroInicio').val(),
			numeroFin: $('#inputNumeroFin').val(),
			fecha: $('#inputFecha').val()
		};
	}
	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				$('#divTablaChequera').show();
				$('#divAgregar').hide();
				break;
			case 'agregar':
				$('#divAgregar').show();
				$('#divTablaChequera').hide();
				$('#inputCuentaBancaria').focus();
				$('#inputFecha').val(funciones.hoy());
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divChequera' class='pantalla customScroll'>
		<div id='divAgregar'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 4, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->style->width = '150px';
			$cells[0][0]->content = '<label>Cuenta Bancaria:</label>';
			$cells[0][1]->style->width = '250px';
			$cells[0][1]->content = '<input id="inputCuentaBancaria" class="textbox obligatorio autoSuggestBox inputForm w230" name="CuentaBancaria" rel="cuentaBancaria" />';

			$cells[1][0]->content = '<label>Número inicio:</label>';
			$cells[1][1]->content = '<input id="inputNumeroInicio" class="textbox obligatorio inputForm w230" rel="numeroInicio" validate="Cheque" />';

			$cells[2][0]->content = '<label>Número fin:</label>';
			$cells[2][1]->content = '<input id="inputNumeroFin" class="textbox obligatorio inputForm w230" rel="numeroFin" validate="Cheque" />';

			$cells[3][0]->content = '<label>Fecha:</label>';
			$cells[3][1]->content = '<input id="inputFecha" class="textbox obligatorio inputForm w210" validate="Fecha" rel="fecha" />';

			$tabla->create();//impresion
			?>
		</div>
		<div id='divTablaChequera' class='acordeon'></div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w150' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputFechaHasta' class='textbox filtroBuscar w150' from='inputFechaHasta'' validate='Fecha' />
		</div>
		<div>
			<label for="inputBuscarCuentaBancaria" class='filtroBuscar'>Cuenta bancaria:</label>
			<input id='inputBuscarCuentaBancaria' class='textbox autoSuggestBox filtroBuscar w170' name="CuentaBancaria" />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/tesoreria/cheques/chequera/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/tesoreria/cheques/chequera/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>