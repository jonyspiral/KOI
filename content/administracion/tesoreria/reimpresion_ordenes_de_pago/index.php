<?php
$usuario = Usuario::logueado()->id;
?>
<script type='text/javascript'>
	usuarioLogueado = <?php echo '"' . $usuario . '"';?>;
	$(document).ready(function(){
		tituloPrograma = 'Reimpresión de ordenes de pago';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('#divReimpresionOrdenesDePago').html('');
		funciones.cambiarTitulo();
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/administracion/tesoreria/reimpresion_ordenes_de_pago/buscar.php?';
			url += 'idProveedor=' + $('#inputBuscarProveedor_selectedValue').val();
			url += '&desde=' + $('#inputBuscarDesde').val();
			url += '&hasta=' + $('#inputBuscarHasta').val();
			url += '&tipo=' + $('#inputBuscarAutonoma').val();
			url += '&mailEnviado=' + $('#inputBuscarMail').val();
			url += '&numero=' + $('#inputBuscarNumero').val();
			url += '&orderBy=' + $('#inputOrderBy').val();
		var msgError = 'No hay ordenes de pago con ese filtro',
			cbSuccess = function(json){
				llenarPantalla(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text((o.idProveedor == null ? o.beneficiario : o.idProveedor + ' - ' + o.razonSocialProveedor) + ' - ORDEN DE PAGO Nº ' + o.numero)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fecha + (o.usuarioBaja ? ' - Anulado por: ' + o.usuarioBaja : '')),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importeTotal))
				)
			)
		);
		return table;
	}

	function divEstado(o) {
		var div = $('<div>').addClass('aCenter');
		if (o.mailEnviado == 'S')
			div.append(
				$('<img>').addClass('pLeft10').attr('src', '/img/varias/mail_sent.png')
			);
		return div;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('botonera aCenter');
		var botonMail, botonPdf, botonEditar, botonBorrar;

		botonEditar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
						.append($('<img>').attr('src', '/img/botones/40/editar' + (o.anulado == 'S' ? '_off' : '') + '.gif')
						.data('op', o)
						.click(function(e) {if(o.anulado == 'N')editarOrdenDePago(e.target);}));
		botonMail = $('<a>').addClass('boton').attr('href', '#').attr('title', (o.mailEnviado == 'S' ? 'Re-' : '') + 'Enviar')
						.click(function(){if(o.anulado == 'N')mailClickOrdenDePago(o.numero)})
						.append($('<img>').attr('src', '/img/botones/40/mail' + (o.anulado == 'S' ? '_off' : '') + '.gif'));
		botonPdf = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir')
						.click(function(){pdfClickOrdenDePago(o.numero)})
						.append($('<img>').attr('src', '/img/botones/40/pdf.gif'));
		botonBorrar = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Borrar')
						.append($('<img>').attr('src', '/img/botones/40/borrar' + (o.importeTotal != o.importePendiente || o.anulado == 'S' ? '_off' : '') + '.gif'))
						.click(function(){if((o.importeTotal == o.importePendiente) && (o.anulado == 'N'))borrarOp(o.numero)});

		div.append(botonEditar, botonMail, botonPdf, botonBorrar);
		return div;
	}

	function returnTr(o) {
		var tr = $('<tr>').attr('id', 'tr_' + o.numero).append(
					$('<td>').addClass('w75p').append(divDatos(o)),
					$('<td>').addClass('w5p').append(divEstado(o)),
					$('<td>').addClass('w20p').append(divBotones(o))
				);
		if(o.anulado == 'S'){
			tr.addClass('indicador-rojo');
		}

		return tr;
	}

	function llenarPantalla(json) {
		var div = $('#divReimpresionOrdenesDePago');
        var table = $('<table>').attr('id', 'tablaOrdenesDePago').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function refrescarOrdenDePago(numero, deleted) {
		var before = $('#tr_' + numero).prev();
		if (before.length < 1)
			$('#tr_' + numero).remove();
		else
			before.next().remove();
		if (!deleted) {
			var url = '/content/administracion/tesoreria/reimpresion_ordenes_de_pago/buscar.php?';
				url += 'numero=' + numero;
			$.postJSON(url, function(json){
				switch (funciones.getJSONType(json)) {
					case funciones.jsonObject:
						if (before.length < 1) {
							$('#tablaOrdenesDePago tbody').first().prepend(returnTr(json.data[0]));
							$('#tablaOrdenesDePago tr').first().shine();
						} else {
							before.after(returnTr(json.data[0]));
							before.next().shine();
						}
						break;
					default:
						$.error(funciones.getJSONMsg(json));
						break;
				}
			});
		}
	}

	function refrescarListaOrdenesPago(id) {
		$('#tr_' + id).remove();
	}

	function borrarOp(numero){
		var msg = '¿Está seguro que desea anular la orden de pago Nº "' + numero + '"?',
			url = '/content/administracion/tesoreria/reimpresion_ordenes_de_pago/borrar.php';
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, {numero: numero}, function(json){
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
							$.success('La orden de pago fue anulada correctamente');
							var tr = $('#tr_' + numero).html('');
							tr.html('');
							tr.append(
								$('<td>').addClass('w75p').append(divDatos(json.data)),
								$('<td>').addClass('w5p').append(divEstado(json.data)),
								$('<td>').addClass('w20p').append(divBotones(json.data))
							);
							if(json.data.anulado == 'S'){
								tr.addClass('indicador-rojo');
							}
							break;
					}
				});
			}
		});
	}

	function ajax(url, cbSuccess, cbAlert) {
		$.showLoading();
		$.postJSON(url, function(json){
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
					cbSuccess(json);
					break;
				case funciones.jsonAlert:
					cbAlert(json);
					break;
			}
		});
	}

	function mailClickOrdenDePago(nro) {
		var url = '/content/administracion/tesoreria/reimpresion_ordenes_de_pago/sendMail.php';
			url += '?numero=' + nro;
		ajax(url, function(json) {
			refrescarOrdenDePago(json.data.numero);
			$.success('La orden de pago se ha enviado correctamente');
		});
	}

	function pdfClickOrdenDePago(nro) {
		var url = '/content/administracion/tesoreria/reimpresion_ordenes_de_pago/getPdf.php';
			url += '?numero=' + nro;
		funciones.pdfClick(url);
	}

	function editarOrdenDePago(btn) {
		var OrdenDePago = $(btn).data('op'),
			body = $('<tbody>').append($(
				'<tr><td><label for="inputImputacion">Imputación: </label></td><td><input id="inputImputacion" type="text" class="textbox autoSuggestBox obligatorio w190" name="Imputacion" /></td></tr>' +
				'<tr><td><label for="inputBeneficiario">Beneficiario: </label></td><td><input id="inputBeneficiario" type="text" class="textbox w190" /></td></tr>' +
				'<tr><td><label for="inputObservaciones">Observaciones: </label></td><td><textarea id="inputObservaciones" class="textbox w190" rel="descripcion" /></td></tr>'
		)),
			div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append($('<table>').append(body)),
			botones = [{value: 'Guardar', action: function() {goEditarCheque(btn);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		llenarCamposEditarOrdenDePago(OrdenDePago);
	}

	function llenarCamposEditarOrdenDePago(OrdenDePago){
		setTimeout(function(){
			$('#inputImputacion').val(OrdenDePago.idImputacion).autoComplete();
			$('#inputBeneficiario').val(OrdenDePago.beneficiario);
			$('#inputObservaciones').val(OrdenDePago.observaciones);
		},500);
	}

	function goEditarCheque(btn){
		var objeto = {
				numero: $(btn).data('op').numero,
				idImputacion: $('#inputImputacion_selectedValue').val(),
				beneficiario: $('#inputBeneficiario').val(),
				observaciones: $('#inputObservaciones').val()
			},
			url = '/content/administracion/tesoreria/reimpresion_ordenes_de_pago/editar.php';

		if(objeto.idImputacion == '') {
			$.error('El campo imputación es obligatorio.');
		} else {
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
						$(btn).data('op').idImputacion = objeto.idImputacion;
						$(btn).data('op').beneficiario = objeto.beneficiario;
						$(btn).data('op').observaciones = objeto.observaciones;

						var obj = jQuery.extend(true, {}, $(btn).data('op')),
							tr = $('#tr_' + objeto.numero);
						tr.html('');
						tr.append(
							$('<td>').addClass('w75p').append(divDatos(obj)),
							$('<td>').addClass('w5p').append(divEstado(obj)),
							$('<td>').addClass('w20p').append(divBotones(obj))
						);
						$.jPopUp.close();
						break;
				}
			});
		}
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.customRadio').enableRadioGroup();
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
	<div id='divReimpresionOrdenesDePago' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox autoSuggestBox filtroBuscar w190' name='Proveedor' />
		</div>
		<div>
			<label for='inputBuscarDesde' class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarHasta' class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarDesde' validate='Fecha' />
		</div>
		<div>
			<label for="inputBuscarAutonoma" class='filtroBuscar'>Tipo:</label>
			<select id='inputBuscarAutonoma' class='textbox filtroBuscar w190'>
				<option value=''>Ambas</option>
				<option value='N'>De proveedor</option>
				<option value='S'>Autónoma</option>
			</select>
		</div>
		<div>
			<label for="inputBuscarMail" class='filtroBuscar'>Mail enviado:</label>
			<select id='inputBuscarMail' class='textbox filtroBuscar w190'>
				<option value=''>Ambas</option>
				<option value='S'>Si</option>
				<option value='N'>No</option>
			</select>
		</div>
		<div>
			<label for='inputBuscarNumero' class='filtroBuscar'>Numero:</label>
			<input id='inputBuscarNumero' class='textbox filtroBuscar w190' validate='Entero' />
		</div>
		<div>
			<label for="inputOrderBy" class='filtroBuscar'>Ordenar por:</label>
			<select id='inputOrderBy' class='textbox filtroBuscar w190'>
				<option value='0'>Nº OP descendente</option>
				<option value='1'>Fecha descendente</option>
			</select>
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
