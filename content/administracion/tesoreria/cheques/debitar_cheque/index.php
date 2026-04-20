<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Debitar cheque';
		cambiarModo('inicio');
		buscar(true);
	});

	function limpiarScreen(){
		$('#divDebitarCheque').html('');
		$('#spanTotal').html('');
	}

	function buscar(primeraVez) {
		if (typeof primeraVez === 'undefined')
			primeraVez = false;
		var url = '/content/administracion/tesoreria/cheques/debitar_cheque/buscar.php?';
			url += 'idCuentaBancaria=' + $('#inputBuscarCuentaBancaria_selectedValue').val() + '&fechaDesde=' + $('#inputBuscarFechaDesde').val() + '&fechaHasta=' + $('#inputBuscarFechaHasta').val() + '&primeraVez=' + (primeraVez ? 'S' : 'N');
		var msgError = 'Ocurrió un error al intentar buscar cheques',
			cbSuccess = function(json){
				if(json.length == 0){
					setTimeout(function(){
						funciones.cancelarBuscarClick();
					},500)
				}else{
					llenarPantalla(json);
				}
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text('BANCO: ' + o.nombreBanco + ' (' + o.nombreCuenta + ') - CHEQUE Nº ' + o.numero + ' - CUIT LIBRADOR: ' + o.cuitLibrador + ' - ENTREGADO A: ' + o.entregadoA)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha Vencimiento: ' + o.fechaVencimiento),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importe))
				)
			)
		);
		return table;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('aCenter');
		var btn1;
		btn1 = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Debitar')
						.attr('onclick', 'debitarCheque(' + o.idCheque + ')')
						.append($('<img>').attr('src', '/img/botones/40/download.gif'));
		div.append(btn1);
		return div;
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.idCheque).append(
			$('<td>').addClass('w75p').append(divDatos(o)),
			$('<td>').addClass('w5p').append(divBotones(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divDebitarCheque');
        var table = $('<table>').attr('id', 'tablaFacturas').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.cheques.length; i++) {
			table.append(returnTr(json.cheques[i]));
        }
        div.append(table);
		if(json.total > 0){
			$('#spanTotal').text(funciones.formatearMoneda(json.total));
		}
	}

	function refrescarListaCheques(id) {
		$('#tr_' + id).remove();
	}

	function debitarCheque(idCheque) {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td><label for="inputFecha">Fecha:</label></td><td><input id="inputFecha" class="textbox obligatorio aRight w190" validate="Fecha" value="' + funciones.hoy() + '" /></td></tr>' +
				  '<tr><td><label for="inputObservaciones" class="filtroBuscar">Observaciones:</label></td><td><textarea id="inputObservaciones" class="textbox w190" /></td></tr>' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {goDebitarCheque(idCheque);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
	}

	function goDebitarCheque(idCheque) {
		var fecha = $('#inputFecha').val();
		var observaciones = $('#inputObservaciones').val();
		var url = '/content/administracion/tesoreria/cheques/debitar_cheque/agregar.php';
		var objeto = {idCheque: idCheque, fecha: fecha, observaciones: observaciones};
		$.showLoading();
		$.postJSON(url, objeto, function(json){
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
					refrescarListaCheques(json.data.idCheque);
					$.success('El cheque fue debitado correctamente');
					$.jPopUp.close();
					break;
			}
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divDebitarCheque').html('');
				$('#divTotal').hide();
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
				$('#divTotal').show();
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
	<div id='divDebitarCheque' class='w100p customScroll acordeon h470'>
		<?php // TABLOTA ?>
	</div>
	<div id='divTotal' class='fRight mTop5 mRight5'><span class='bold'>Total: </span><span id='spanTotal'></span></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarCuentaBancaria' class='filtroBuscar'>Cuenta bancaria:</label>
			<input id='inputBuscarCuentaBancaria' class='textbox autoSuggestBox filtroBuscar w190' name='CuentaBancaria' />
		</div>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar aRight w170' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar aRight w170' from='inputFechaHasta'' validate='Fecha' />
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
